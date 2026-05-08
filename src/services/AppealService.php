<?php

require_once BASE_PATH . '/models/Appeal.php';
require_once BASE_PATH . '/models/Fine.php';
require_once BASE_PATH . '/models/AuditLog.php';
require_once BASE_PATH . '/helpers/notification_helper.php';

class AppealService
{
    private Appeal   $appealModel;
    private Fine     $fineModel;
    private AuditLog $auditLog;

    public function __construct()
    {
        $this->appealModel = new Appeal();
        $this->fineModel   = new Fine();
        $this->auditLog    = new AuditLog();
    }

    public function submit(int $fineId, int $userId, string $reason, ?string $evidencePath = null): array
    {
        $fine = $this->fineModel->findById($fineId);
        if (!$fine || $fine['user_id'] !== $userId) {
            return ['success' => false, 'message' => 'Fine not found.'];
        }
        if ($this->appealModel->hasPendingForFine($fineId)) {
            return ['success' => false, 'message' => 'An appeal is already pending for this fine.'];
        }

        $appealId = $this->appealModel->create([
            'fine_id'       => $fineId,
            'user_id'       => $userId,
            'reason'        => $reason,
            'evidence_file' => $evidencePath,
        ]);

        $this->fineModel->markAppealed($fineId);
        $this->auditLog->log($userId, 'appeal_submitted', "Appeal #{$appealId} for fine #{$fineId}");

        return ['success' => true, 'appeal_id' => $appealId];
    }

    public function review(int $appealId, int $adminId, string $decision, string $note): array
    {
        $appeal = $this->appealModel->findById($appealId);
        if (!$appeal) {
            return ['success' => false, 'message' => 'Appeal not found.'];
        }

        $this->appealModel->review($appealId, $decision, $note, $adminId);

        if ($decision === 'approved') {
            $this->fineModel->waive($appeal['fine_id']);
            create_notification(
                $appeal['user_id'],
                'Appeal Approved',
                'Your fine appeal has been approved and the fine waived.',
                'appeal'
            );
        } else {
            // Use Fine::update() (now properly defined) to restore status to unpaid
            $this->fineModel->update($appeal['fine_id'], ['status' => 'unpaid']);
            create_notification(
                $appeal['user_id'],
                'Appeal Rejected',
                "Your fine appeal was rejected. Note: {$note}",
                'appeal'
            );
        }

        $this->auditLog->log($adminId, 'appeal_reviewed', "Appeal #{$appealId} {$decision} by admin #{$adminId}");

        return ['success' => true];
    }
}