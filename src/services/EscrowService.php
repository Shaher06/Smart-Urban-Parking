<?php

require_once BASE_PATH . '/models/Payment.php';

class EscrowService
{
    private Payment $paymentModel;

    public function __construct()
    {
        $this->paymentModel = new Payment();
    }

    public function lockFunds(int $paymentId): bool
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("UPDATE payments SET escrow_locked=1 WHERE id=?");
        return $stmt->execute([$paymentId]);
    }

    public function releaseFunds(int $paymentId): bool
    {
        return $this->paymentModel->releaseEscrow($paymentId);
    }

    public function isLocked(int $paymentId): bool
    {
        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT escrow_locked FROM payments WHERE id=?");
        $stmt->execute([$paymentId]);
        return (bool)$stmt->fetchColumn();
    }
}