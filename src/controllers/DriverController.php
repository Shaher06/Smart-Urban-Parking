<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/Driver.php';
require_once BASE_PATH . '/models/AuditLog.php';
require_once BASE_PATH . '/helpers/notification_helper.php';

class DriverController extends Controller
{
    private Driver $driverModel;

    public function __construct()
    {
        parent::__construct();
        $this->driverModel = new Driver();
    }

    public function dashboard(): void
    {
        $this->requireRole('driver');
        $stats = $this->driverModel->getDriverStats(current_user_id());
        $this->render('driver/dashboard', ['stats' => $stats]);
    }

    /**
     * Report an on-site emergency — notifies admins/officers and writes audit trail.
     */
    public function emergencyReport(): void
    {
        $this->requireRole('driver');
        if ($this->isPost()) {
            $type    = $this->post('report_type', 'other');
            $desc    = trim((string)$this->post('description', ''));
            $spotId  = (int)$this->post('spot_id') ?: null;
            $resId   = (int)$this->post('reservation_id') ?: null;
            $allowed = ['accident','illegal_parking','safety_issue','blocked_spot','other'];

            if ($desc === '') {
                set_flash('error', 'Please describe the situation.');
                $this->redirect('?page=driver-emergency');
                return;
            }
            if (!in_array($type, $allowed, true)) {
                $type = 'other';
            }

            // Store in emergency_reports table
            $db = Database::getInstance();
            $db->prepare(
                "INSERT INTO emergency_reports (user_id, report_type, description, spot_id, reservation_id)
                 VALUES (?, ?, ?, ?, ?)"
            )->execute([current_user_id(), $type, $desc, $spotId, $resId]);
            $reportId = (int)$db->lastInsertId();

            // Audit log
            $audit = new AuditLog();
            $audit->log(current_user_id(), 'emergency_report',
                "Report #{$reportId} type={$type}: " . substr($desc, 0, 200));

            // Notify all admins and officers
            $admins = $db->query(
                "SELECT id FROM users WHERE role IN ('admin','officer') AND status = 'active'"
            )->fetchAll(PDO::FETCH_COLUMN);
            $summary = ucfirst(str_replace('_', ' ', $type)) . ' reported by driver #' . current_user_id()
                . ': ' . substr($desc, 0, 300);
            foreach ($admins as $adminId) {
                create_notification((int)$adminId, '🚨 Emergency Report', $summary, 'system');
            }

            set_flash('success', 'Emergency report submitted. Staff have been notified.');
            $this->redirect('?page=driver-dashboard');
            return;
        }

        // Pass the driver's active reservations so they can optionally link
        $db   = Database::getInstance();
        $stmt = $db->prepare(
            "SELECT r.id, ps.name as spot_name FROM reservations r
             JOIN parking_spots ps ON ps.id = r.spot_id
             WHERE r.user_id = ? AND r.status IN ('confirmed','active')
             ORDER BY r.start_time DESC LIMIT 20"
        );
        $stmt->execute([current_user_id()]);
        $activeReservations = $stmt->fetchAll();

        $spotStmt = $db->query("SELECT id, name, city FROM parking_spots WHERE status='active' ORDER BY name");
        $activeSpots = $spotStmt->fetchAll();

        $this->render('driver/emergency', [
            'activeReservations' => $activeReservations,
            'activeSpots'        => $activeSpots,
        ]);
    }
}