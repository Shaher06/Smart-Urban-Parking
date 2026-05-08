<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/models/SpaceOwner.php';
require_once BASE_PATH . '/models/Payout.php';
require_once BASE_PATH . '/models/Report.php';
require_once BASE_PATH . '/models/FileUpload.php';
require_once BASE_PATH . '/models/AuditLog.php';
require_once BASE_PATH . '/services/TaxService.php';
require_once BASE_PATH . '/services/UploadService.php';
require_once BASE_PATH . '/services/PricingService.php';

class OwnerController extends Controller
{
    private SpaceOwner    $ownerModel;
    private Payout        $payoutModel;
    private Report        $reportModel;
    private TaxService    $taxService;
    private UploadService $uploadService;

    public function __construct()
    {
        parent::__construct();
        $this->ownerModel    = new SpaceOwner();
        $this->payoutModel   = new Payout();
        $this->reportModel   = new Report();
        $this->taxService    = new TaxService();
        $this->uploadService = new UploadService();
    }

    public function dashboard(): void
    {
        $this->requireRole('owner');
        $stats = $this->ownerModel->getOwnerStats(current_user_id());
        $this->render('owner/dashboard', ['stats' => $stats]);
    }

    public function earnings(): void
    {
        $this->requireRole('owner');
        $earnings = $this->reportModel->getOwnerEarnings(current_user_id());
        $total    = $this->ownerModel->getTotalEarnings(current_user_id());
        $this->render('owner/earnings', ['earnings' => $earnings, 'total' => $total]);
    }

    public function payouts(): void
    {
        $this->requireRole('owner');

        if ($this->isPost()) {
            // CSRF validation
            $submittedToken = $this->post('csrf_token');
            if (
                empty($submittedToken) ||
                empty($_SESSION['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'], $submittedToken)
            ) {
                set_flash('error', 'Invalid request. Please try again.');
                $this->redirect('?page=owner-payouts');
                return;
            }
            // Rotate token after use
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            $result = $this->payoutModel->requestAvailablePayout(current_user_id());
            if (!empty($result['success'])) {
                set_flash('success', 'Payout requested successfully.');
            } else {
                set_flash('error', $result['message'] ?? 'No available earnings to withdraw.');
            }
            $this->redirect('?page=owner-payouts');
            return;
        }

        $payouts = $this->payoutModel->getByOwner(current_user_id());
        $this->render('owner/payouts', ['payouts' => $payouts]);
    }

    public function verification(): void
    {
        $this->requireRole('owner');
        if ($this->isPost() && !empty($_FILES['document']['name'])) {
            $path = $this->uploadService->uploadOwnerDocument($_FILES['document'], current_user_id());
            if ($path) {
                set_flash('success', 'Document submitted for verification.');
            } else {
                set_flash('error', 'Failed to upload document.');
            }
            $this->redirect('?page=owner-verification');
        }
        $docs = (new FileUpload())->getByUser(current_user_id());
        $this->render('owner/verification', ['docs' => $docs]);
    }

    public function taxDetails(): void
    {
        $this->requireRole('owner');
        if ($this->isPost()) {
            $year = (int)$this->post('year', (string)date('Y'));
            $data = $this->taxService->generateTaxReport(current_user_id(), $year);
            $this->render('owner/tax-details', [
                'taxData' => $data,
                'history' => $this->taxService->getOwnerTaxHistory(current_user_id()),
            ]);
            return;
        }
        $history = $this->taxService->getOwnerTaxHistory(current_user_id());
        $this->render('owner/tax-details', ['taxData' => null, 'history' => $history]);
    }

    /**
     * Availability schedule management.
     */
    public function availability(): void
    {
        $this->requireRole('owner');
        $db = $this->db();

        if ($this->isPost()) {
            $spotId    = (int)$this->post('spot_id');
            $status    = $this->post('status');
            $available = (int)$this->post('available_slots');
            $allowed   = ['active', 'inactive', 'maintenance', 'owner-use'];

            if ($spotId && in_array($status, $allowed, true)) {
                // Verify ownership
                $check = $db->prepare("SELECT id, total_slots FROM parking_spots WHERE id = ? AND owner_id = ?");
                $check->execute([$spotId, current_user_id()]);
                $row = $check->fetch();

                if ($row) {
                    $available = max(0, min($available, (int)$row['total_slots']));
                    $db->prepare(
                        "UPDATE parking_spots SET status = ?, available_slots = ? WHERE id = ? AND owner_id = ?"
                    )->execute([$status, $available, $spotId, current_user_id()]);

                    (new \AuditLog())->log(current_user_id(), 'spot_availability_updated',
                        "Spot #{$spotId} status={$status} available={$available}");

                    set_flash('success', 'Spot availability updated.');
                }
            } else {
                set_flash('error', 'Invalid status value.');
            }
            $this->redirect('?page=owner-availability');
            return;
        }

        $spots = $db->prepare(
            "SELECT * FROM parking_spots WHERE owner_id = ? ORDER BY id DESC"
        );
        $spots->execute([current_user_id()]);
        $mySpots = $spots->fetchAll();
        $this->render('owner/availability', ['spots' => $mySpots]);
    }

    /**
     * Pricing management.
     */
    public function pricing(): void
    {
        $this->requireRole('owner');
        $db    = $this->db();
        $spots = $db->prepare(
            "SELECT * FROM parking_spots WHERE owner_id = ? ORDER BY id DESC"
        );
        $spots->execute([current_user_id()]);
        $mySpots = $spots->fetchAll();

        if ($this->isPost()) {
            $spotId   = (int)$this->post('spot_id');
            $newPrice = (float)$this->post('price_per_hour');
            if ($spotId && $newPrice > 0) {
                $upd = $db->prepare(
                    "UPDATE parking_spots SET price_per_hour = ? WHERE id = ? AND owner_id = ?"
                );
                $upd->execute([$newPrice, $spotId, current_user_id()]);
                set_flash('success', 'Pricing updated.');
                $this->redirect('?page=owner-pricing');
                return;
            }
            set_flash('error', 'Invalid spot or price.');
        }

        $pricing   = new PricingService();
        $avgStmt   = $db->prepare(
            "SELECT AVG(price_per_hour) FROM parking_spots WHERE city = ? AND status = 'active'"
        );
        $suggested = [];
        foreach ($mySpots as $row) {
            $avgStmt->execute([$row['city']]);
            $cityAvg = (float) $avgStmt->fetchColumn();
            $suggested[(int)$row['id']] = $pricing->suggestMarketHourly(
                (float) $row['price_per_hour'],
                $cityAvg,
                (int) $row['available_slots'],
                (int) $row['total_slots']
            );
        }

        $this->render('owner/pricing', ['spots' => $mySpots, 'suggested' => $suggested]);
    }
}