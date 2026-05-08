<?php

require_once BASE_PATH . '/models/AuditLog.php';

class AuditTrailService
{
    private AuditLog $auditLog;

    public function __construct()
    {
        $this->auditLog = new AuditLog();
    }

    public function log(int $userId = null, string $action = '', string $description = ''): void
    {
        $this->auditLog->log($userId, $action, $description);
    }

    public function getAll(int $limit = 200): array
    {
        return $this->auditLog->getAll($limit);
    }
}