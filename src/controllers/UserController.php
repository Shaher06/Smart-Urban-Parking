<?php
/**
 * USER CONTROLLER — Refactored
 *
 * FIX: Profile image upload now correctly:
 * 1. Validates file via UploadService
 * 2. Saves to /uploads/profile_images/
 * 3. Stores relative path in users.profile_image column
 * 4. Refreshes session so navbar shows new image immediately
 * 5. Old image deleted if replaced
 *
 * PATTERN: ServiceFactory used to get UploadService (Factory Pattern)
 */

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/core/ServiceFactory.php';
require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/core/Validator.php';

class UserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function profile(): void
    {
        $this->requireLogin();
        $user = $this->userModel->findById(current_user_id());
        if (!$user) {
            set_flash('error', 'Profile not found.');
            $this->redirect('?page=login');
            return;
        }
        $this->render('user/profile', ['user' => $user]);
    }

    /**
     * EDIT PROFILE — includes profile image upload fix.
     */
    public function editProfile(): void
    {
        $this->requireLogin();
        $user = $this->userModel->findById(current_user_id());

        if ($this->isPost()) {
            $name  = trim($this->post('name'));
            $phone = trim($this->post('phone'));
            $lang  = $this->post('language', 'en');

            // Validate
            $v = new Validator();
            $v->required('name', $name)->maxLength('name', $name, 100);
            if ($v->fails()) {
                set_flash('error', $v->firstError());
                $this->render('user/edit-profile', ['user' => $user]);
                return;
            }

            $updateData = [
                'name'     => $name,
                'phone'    => $phone,
                'language' => $lang,
            ];

            // ── Profile image upload ──────────────────────────────────────────
            // FIX: Check file was actually uploaded (not just form submitted)
            $hasNewImage = isset($_FILES['profile_image'])
                        && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK
                        && $_FILES['profile_image']['size'] > 0;

            if ($hasNewImage) {
                /** @var UploadService $uploadService */
                $uploadService = ServiceFactory::make('upload'); // PATTERN: Factory

                $newPath = $uploadService->uploadProfileImage(
                    $_FILES['profile_image'],
                    current_user_id()
                );

                if ($newPath) {
                    // Delete old profile image if it exists
                    if (!empty($user['profile_image'])) {
                        $uploadService->deleteFile($user['profile_image']);
                    }
                    $updateData['profile_image'] = $newPath;
                } else {
                    set_flash('error', 'Image upload failed. Ensure the file is JPG, PNG, or WebP under 5MB.');
                    $this->render('user/edit-profile', ['user' => $user]);
                    return;
                }
            }

            $this->userModel->update(current_user_id(), $updateData);

            // Refresh session with updated user data
            $refreshed = $this->userModel->findById(current_user_id());
            login_user($refreshed);

            set_flash('success', 'Profile updated successfully.');
            $this->redirect('?page=profile');
            return;
        }

        $this->render('user/edit-profile', ['user' => $user]);
    }

    public function deleteProfile(): void
    {
        $this->requireLogin();
        if ($this->isPost()) {
            $userId = current_user_id();
            $user   = $this->userModel->findById($userId);

            // Delete profile image file if exists
            if (!empty($user['profile_image'])) {
                /** @var UploadService $us */
                $us = ServiceFactory::make('upload');
                $us->deleteFile($user['profile_image']);
            }

            logout_user();
            $this->userModel->deleteById($userId);
            set_flash('success', 'Your account has been permanently deleted.');
            $this->redirect('?page=login');
            return;
        }
        $this->render('user/delete-profile');
    }

    public function language(): void
    {
        $this->requireLogin();
        if ($this->isPost()) {
            $lang = $this->post('language', 'en');
            if (in_array($lang, ['en', 'ar', 'fr', 'de', 'es'], true)) {
                $this->userModel->update(current_user_id(), ['language' => $lang]);
                $updated = $this->userModel->findById(current_user_id());
                login_user($updated);
                set_flash('success', 'Language updated to ' . strtoupper($lang) . '.');
            }
            $this->redirect('?page=profile');
        }
        $this->render('user/language');
    }

    /**
     * Change password — separate from profile edit for security.
     */
    public function changePassword(): void
    {
        $this->requireLogin();
        if ($this->isPost()) {
            $current = $this->post('current_password');
            $new     = $this->post('new_password');
            $confirm = $this->post('confirm_password');

            $user = $this->userModel->findById(current_user_id());

            if (!password_verify($current, $user['password'])) {
                set_flash('error', 'Current password is incorrect.');
                $this->redirect('?page=edit-profile');
                return;
            }

            $v = new Validator();
            $v->minLength('new_password', $new, 6)
              ->matches('confirm_password', $confirm, $new, 'new password');

            if ($v->fails()) {
                set_flash('error', $v->firstError());
                $this->redirect('?page=edit-profile');
                return;
            }

            $this->userModel->updatePassword(current_user_id(), $new);
            set_flash('success', 'Password changed successfully.');
            $this->redirect('?page=profile');
        }
    }
}