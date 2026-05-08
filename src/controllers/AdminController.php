<?php
/**
 * ADMIN CONTROLLER — Refactored
 *
 * PATTERN: ServiceFactory used throughout (Factory Pattern)
 * All service instantiation goes through ServiceFactory::make()
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/ServiceFactory.php';
require_once BASE_PATH . '/models/Admin.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/EventZone.php';
require_once BASE_PATH . '/models/FileUpload.php';
require_once BASE_PATH . '/models/Payout.php';

class AdminController extends Controller
{
    private Admin     $adminModel;
    private User      $userModel;
    private EventZone $eventZoneModel;

    public function __construct()
    {
        parent::__construct();
        $this->adminModel     = new Admin();
        $this->userModel      = new User();
        $this->eventZoneModel = new EventZone();
    }

    public function dashboard(): void
    {
        $this->requireRoles(['admin', 'officer']);
        $stats = $this->adminModel->getAdminStats();

        /** @var FineService $fineService */
        $fineService = ServiceFactory::make('fine'); // PATTERN: Factory
        $fineStats   = $fineService->getFineStats();

        /** @var OccupancyService $occupancy */
        $occupancy   = ServiceFactory::make('occupancy');
        $liveOccupancy = $occupancy->getLiveOccupancy();

        $this->render('admin/dashboard', [
            'stats'         => $stats,
            'fine_stats'    => $fineStats,
            'live_occupancy'=> $liveOccupancy,
        ]);
    }

    public function users(): void
    {
        $this->requireRole('admin');
        $search = trim($this->get('search'));
        $users  = $search
            ? $this->userModel->searchByName($search)
            : $this->userModel->findAll();
        $this->render('admin/users', ['users' => $users, 'search' => $search]);
    }

    public function addUser(): void
    {
        $this->requireRole('admin');
        if ($this->isPost()) {
            $v = new \Validator();
            $v->required('name', $this->post('name'))
              ->required('email', $this->post('email'))
              ->email('email', $this->post('email'))
              ->required('password', $this->post('password'))
              ->minLength('password', $this->post('password'), 6);

            if ($v->fails()) {
                set_flash('error', $v->firstError());
                $this->redirect('?page=admin-users');
                return;
            }

            $existing = $this->userModel->findByEmail($this->post('email'));
            if ($existing) {
                set_flash('error', 'Email already registered.');
                $this->redirect('?page=admin-users');
                return;
            }

            $this->userModel->create([
                'name'     => trim($this->post('name')),
                'email'    => trim($this->post('email')),
                'password' => $this->post('password'),
                'phone'    => trim($this->post('phone')),
                'role'     => $this->post('role', 'driver'),
                'status'   => 'active',
            ]);

            /** @var AuditTrailService $audit */
            $audit = ServiceFactory::make('audit');
            $audit->log(current_user_id(), 'admin_add_user',
                "Admin created user: " . $this->post('email'));

            set_flash('success', 'User added successfully.');
        }
        $this->redirect('?page=admin-users');
    }

    public function deleteUser(): void
    {
        $this->requireRole('admin');
        $id = (int)$this->get('id');

        if (!$id || $id === current_user_id()) {
            set_flash('error', 'Cannot delete this user.');
            $this->redirect('?page=admin-users');
            return;
        }

        $this->userModel->deleteById($id);

        $audit = ServiceFactory::make('audit');
        $audit->log(current_user_id(), 'admin_delete_user', "Deleted user #{$id}");

        set_flash('success', 'User deleted.');
        $this->redirect('?page=admin-users');
    }

    public function updateUserStatus(): void
    {
        $this->requireRole('admin');
        if ($this->isPost()) {
            $id     = (int)$this->post('user_id');
            $status = $this->post('status');

            if ($id && in_array($status, ['active', 'suspended', 'blacklisted'], true)) {
                $this->userModel->update($id, ['status' => $status]);

                $audit = ServiceFactory::make('audit');
                $audit->log(current_user_id(), 'admin_status_change',
                    "User #{$id} status changed to {$status}");

                set_flash('success', 'User status updated.');
            }
        }
        $this->redirect('?page=admin-users');
    }

    public function roles(): void
    {
        $this->requireRole('admin');
        if ($this->isPost()) {
            $userId = (int)$this->post('user_id');
            $role   = $this->post('role');
            if ($userId && in_array($role, ['driver', 'owner', 'admin', 'officer'], true)) {
                $this->userModel->update($userId, ['role' => $role]);
                $audit = ServiceFactory::make('audit');
                $audit->log(current_user_id(), 'admin_role_change',
                    "User #{$userId} role → {$role}");
                set_flash('success', 'Role updated.');
            }
        }
        $users = $this->userModel->findAll();
        $this->render('admin/roles', ['users' => $users]);
    }

    public function blacklist(): void
    {
        $this->requireRole('admin');
        $blacklisted = $this->userModel->findWhere('status', 'blacklisted');
        $suspended   = $this->userModel->findWhere('status', 'suspended');
        $this->render('admin/blacklist', [
            'blacklisted' => $blacklisted,
            'suspended'   => $suspended,
        ]);
    }

    public function eventZones(): void
    {
        $this->requireRole('admin');
        if ($this->isPost()) {
            $name  = trim($this->post('name'));
            $start = $this->post('start_time');
            $end   = $this->post('end_time');

            if (!$name || !$start || !$end) {
                set_flash('error', 'Name, start time, and end time are required.');
                $this->redirect('?page=event-zones');
                return;
            }

            $this->eventZoneModel->create([
                'name'              => $name,
                'description'       => $this->post('description'),
                'affected_spot_ids' => $this->post('affected_spot_ids'),
                'start_time'        => $start,
                'end_time'          => $end,
                'locked_by'         => current_user_id(),
            ]);

            $audit = ServiceFactory::make('audit');
            $audit->log(current_user_id(), 'event_zone_created', "Event zone '{$name}' locked.");
            set_flash('success', 'Event zone locked.');
            $this->redirect('?page=event-zones');
            return;
        }
        $zones = $this->eventZoneModel->getActive();
        $this->render('admin/event-zones', ['zones' => $zones]);
    }

    public function emergencyOverride(): void
    {
        $this->requireRole('admin');
        $db = $this->db();

        if ($this->isPost()) {
            $spotId       = (int)$this->post('spot_id');
            $reason       = trim($this->post('reason'));
            $cancelRes    = (bool)$this->post('cancel_reservation');
            $targetStatus = $this->post('target_status', 'active');
            $allowed      = ['active', 'inactive', 'maintenance'];

            if (!$spotId || !$reason) {
                set_flash('error', 'Spot and reason are required.');
                $this->redirect('?page=emergency-override');
                return;
            }
            if (!in_array($targetStatus, $allowed, true)) {
                $targetStatus = 'active';
            }

            $db->prepare("UPDATE parking_spots SET status = ? WHERE id = ?")
               ->execute([$targetStatus, $spotId]);

            // Optionally cancel active/confirmed reservation on this spot
            if ($cancelRes) {
                $resStmt = $db->prepare(
                    "SELECT id, user_id FROM reservations
                     WHERE spot_id = ? AND status IN ('confirmed','active') LIMIT 1"
                );
                $resStmt->execute([$spotId]);
                $affectedRes = $resStmt->fetch();
                if ($affectedRes) {
                    $db->prepare("UPDATE reservations SET status='cancelled' WHERE id=?")
                       ->execute([$affectedRes['id']]);
                    create_notification(
                        (int)$affectedRes['user_id'],
                        'Reservation Cancelled — Emergency',
                        "Your reservation at spot #{$spotId} was cancelled due to an emergency: {$reason}",
                        'system'
                    );
                }
            }

            $audit = ServiceFactory::make('audit');
            $audit->log(current_user_id(), 'emergency_override',
                "EMERGENCY OVERRIDE spot #{$spotId} → {$targetStatus}. Cancel reservation: "
                . ($cancelRes ? 'yes' : 'no') . ". Reason: {$reason}");

            set_flash('success', "Emergency override applied. Spot #{$spotId} is now {$targetStatus}.");
            $this->redirect('?page=emergency-override');
            return;
        }

        // Load open emergency reports for context
        $reports = [];
        try {
            $reports = $db->query(
                "SELECT er.*, u.name as driver_name, ps.name as spot_name
                 FROM emergency_reports er
                 JOIN users u ON u.id = er.user_id
                 LEFT JOIN parking_spots ps ON ps.id = er.spot_id
                 WHERE er.status = 'open'
                 ORDER BY er.created_at DESC LIMIT 20"
            )->fetchAll();
        } catch (\Exception $e) { /* table may not exist yet */ }

        $spots = $db->query("SELECT * FROM parking_spots ORDER BY status ASC, name ASC")->fetchAll();
        $this->render('admin/emergency-override', ['spots' => $spots, 'reports' => $reports]);
    }

    /**
     * OFFICER DISPATCH — SRS: Enforcement Officer Dispatch
     *
     * Admin assigns an officer to a driver incident.
     * Creates notification for officer + audit log entry.
     */
    public function officerDispatch(): void
    {
        $this->requireRoles(['admin', 'officer']);

        if ($this->isPost()) {
            $officerId    = (int)$this->post('officer_id');
            $targetId     = (int)$this->post('driver_id');
            $spotId       = (int)$this->post('spot_id') ?: null;
            $location     = trim($this->post('location'));
            $note         = trim($this->post('note'));
            $incidentType = $this->post('incident_type', 'violation');

            if (!$officerId || !$targetId) {
                set_flash('error', 'Officer and target are required.');
                $this->redirect('?page=officer-dispatch');
                return;
            }

            // Build location hint from spot if selected
            if ($spotId) {
                $spotRow = $this->db()->prepare("SELECT name, address, city FROM parking_spots WHERE id=?");
                $spotRow->execute([$spotId]);
                $sp = $spotRow->fetch();
                if ($sp && !$location) {
                    $location = "{$sp['name']}, {$sp['address']}, {$sp['city']}";
                }
            }

            $dispatchMsg = "DISPATCH: You have been assigned to a {$incidentType} incident."
                . ($location ? " Location: {$location}." : '')
                . ($note ? " Note: {$note}." : '');

            create_notification($officerId, 'Dispatch Assignment', $dispatchMsg, 'system');

            // Also notify the driver
            if ($targetId) {
                create_notification($targetId, 'Enforcement Notice',
                    "An enforcement officer has been dispatched regarding a {$incidentType} incident at your location.",
                    'system');
            }

            $audit = ServiceFactory::make('audit');
            $audit->log(
                current_user_id(),
                'officer_dispatch',
                "Officer #{$officerId} dispatched to driver #{$targetId}. "
                . "Spot: " . ($spotId ?: 'N/A') . ". "
                . "Type: {$incidentType}. Location: {$location}. Note: {$note}"
            );

            set_flash('success', 'Officer dispatched and notified. Driver also informed.');
            $this->redirect('?page=officer-dispatch');
            return;
        }

        $officers = $this->userModel->getByRole('officer');
        $drivers  = $this->userModel->getByRole('driver');
        $spots    = $this->db()->query("SELECT id, name, city FROM parking_spots ORDER BY name")->fetchAll();

        // Fetch active overstay candidates (drivers checked in past their end_time)
        $overstayStmt = $this->db()->query(
            "SELECT r.id, r.user_id, r.end_time, r.spot_id,
                    u.name as driver_name, ps.name as spot_name, ps.address
             FROM reservations r
             JOIN users u ON u.id = r.user_id
             JOIN parking_spots ps ON ps.id = r.spot_id
             WHERE r.status = 'active'
               AND r.end_time < NOW()
             ORDER BY r.end_time ASC LIMIT 20"
        );
        $overstays = $overstayStmt->fetchAll();

        $this->render('admin/officer-dispatch', [
            'officers'  => $officers,
            'drivers'   => $drivers,
            'spots'     => $spots,
            'overstays' => $overstays,
        ]);
    }

    public function systemHealth(): void
    {
        $this->requireRoles(['admin', 'officer']);
        $health = ServiceFactory::make('health')->getSystemHealth(); // PATTERN: Factory
        $this->render('admin/system-health', ['health' => $health]);
    }

    public function auditLogs(): void
    {
        $this->requireRole('admin');
        $audit = ServiceFactory::make('audit');
        $logs  = $audit->getAll(200);
        $this->render('admin/audit-logs', ['logs' => $logs]);
    }

    public function ownersVerification(): void
    {
        $this->requireRole('admin');

        if ($this->isPost()) {
            $ownerId = (int)$this->post('owner_id');
            $action  = $this->post('action');

            if ($action === 'verify') {
                $this->userModel->update($ownerId, ['status' => 'active']);
                create_notification($ownerId, 'Verification Approved',
                    'Your ownership documents have been verified. Welcome aboard!', 'system');
                set_flash('success', 'Owner verified.');
            } elseif ($action === 'reject') {
                $this->userModel->update($ownerId, ['status' => 'suspended']);
                create_notification($ownerId, 'Verification Rejected',
                    'Your ownership documents could not be verified. Please re-submit.', 'system');
                set_flash('warning', 'Owner verification rejected.');
            }

            $audit = ServiceFactory::make('audit');
            $audit->log(current_user_id(), 'owner_verification',
                "Owner #{$ownerId} {$action}d by admin.");
            $this->redirect('?page=owners-verification');
            return;
        }

        $docs   = (new FileUpload())->getByType('owner_document');
        $owners = $this->userModel->getByRole('owner');
        $this->render('admin/owners-verification', ['docs' => $docs, 'owners' => $owners]);
    }

    /**
     * PAYOUTS — Admin processes pending owner payouts.
     */
    public function managePayouts(): void
    {
        $this->requireRole('admin');

        if ($this->isPost()) {
            // CSRF validation
            $submittedToken = $this->post('csrf_token');
            if (
                empty($submittedToken) ||
                empty($_SESSION['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'], $submittedToken)
            ) {
                set_flash('error', 'Invalid request. Please try again.');
                $this->redirect('?page=manage-payouts');
                return;
            }
            // Rotate token after use
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            $payoutId = (int)$this->post('payout_id');

            if ($payoutId <= 0) {
                set_flash('error', 'Invalid payout.');
                $this->redirect('?page=manage-payouts');
                return;
            }

            $payoutModel = new Payout();
            $payout      = $payoutModel->findById($payoutId);

            if (!$payout) {
                set_flash('error', 'Payout not found.');
                $this->redirect('?page=manage-payouts');
                return;
            }

            if ($payout['status'] !== 'pending') {
                set_flash('warning', 'This payout is already processed.');
                $this->redirect('?page=manage-payouts');
                return;
            }

            $ok = $payoutModel->markPaid($payoutId);
            if ($ok) {
                // Safe: net_amount is a decimal from DB, cast to float for formatting
                $netFormatted = number_format((float)$payout['net_amount'], 2);
                create_notification(
                    (int)$payout['owner_id'],
                    'Payout Processed',
                    "Your payout of \${$netFormatted} has been processed.",
                    'payment'
                );
                set_flash('success', 'Payout marked as paid.');
            } else {
                set_flash('warning', 'Payout could not be marked paid (it may have already been processed).');
            }

            $this->redirect('?page=manage-payouts');
            return;
        }

        $payoutModel = new Payout();
        $payouts     = $payoutModel->getAll();
        $this->render('admin/manage-payouts', ['payouts' => $payouts]);
    }
    /**
     * OCCUPANCY DASHBOARD — SRS: Real-Time Occupancy Predictor
     */
    public function occupancyDashboard(): void
    {
        $this->requireRoles(['admin', 'officer']);

        /** @var OccupancyService $occupancy */
        $occupancy   = ServiceFactory::make('occupancy');
        $liveData    = $occupancy->getLiveOccupancy();

        $this->render('reports/occupancy', ['live_data' => $liveData]);
    }

    /**
     * Admin — view all emergency reports submitted by drivers (Priority 8).
     */
    public function emergencyReports(): void
    {
        $this->requireRoles(['admin', 'officer']);
        $db = $this->db();

        if ($this->isPost()) {
            $reportId = (int)$this->post('report_id');
            $status   = $this->post('status');
            $note     = trim($this->post('admin_note', ''));
            $allowed  = ['open', 'in_progress', 'resolved'];

            if ($reportId && in_array($status, $allowed, true)) {
                $db->prepare(
                    "UPDATE emergency_reports SET status = ?, admin_note = ? WHERE id = ?"
                )->execute([$status, $note, $reportId]);

                $audit = ServiceFactory::make('audit');
                $audit->log(current_user_id(), 'emergency_report_updated',
                    "Emergency report #{$reportId} set to {$status}");

                set_flash('success', "Report #{$reportId} updated to {$status}.");
            }
            $this->redirect('?page=admin-emergency-reports');
            return;
        }

        $reports = [];
        try {
            $reports = $db->query(
                "SELECT er.*, u.name as driver_name, u.email as driver_email,
                         ps.name as spot_name
                 FROM emergency_reports er
                 JOIN users u ON u.id = er.user_id
                 LEFT JOIN parking_spots ps ON ps.id = er.spot_id
                 ORDER BY er.created_at DESC"
            )->fetchAll();
        } catch (\Exception $e) { /* table may not exist yet in older installations */ }

        $this->render('admin/emergency-reports', ['reports' => $reports]);
    }
}