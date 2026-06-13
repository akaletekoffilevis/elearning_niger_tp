<?php

class ProfileController
{
    public function index(): void
    {
        Auth::require();

        $user = Auth::user();
        $enrollments = Enrollment::findByUser($user['id']);
        $certificates = Certificate::findByUser($user['id']);

        Router::render('profile/index', [
            'user' => $user,
            'enrollments' => $enrollments,
            'certificates' => $certificates,
        ], 'main');
    }

    public function update(): void
    {
        Auth::require();

        $fullName = trim($_POST['full_name'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $avatar = trim($_POST['avatar'] ?? '');

        if (empty($fullName)) {
            $_SESSION['error'] = 'Le nom est requis.';
            Router::redirect('/profile');
        }

        $stmt = Database::connect()->prepare(
            'UPDATE users SET full_name = ?, bio = ?, avatar = ? WHERE id = ?'
        );
        $stmt->execute([$fullName, $bio ?: null, $avatar ?: null, (int) $_SESSION['user_id']]);

        $_SESSION['success'] = 'Profil mis à jour.';
        Router::redirect('/profile');
    }

    public function switchTheme(): void
    {
        Auth::require();

        $user = Auth::user();
        $newTheme = $user['theme'] === 'dark' ? 'light' : 'dark';

        $stmt = Database::connect()->prepare('UPDATE users SET theme = ? WHERE id = ?');
        $stmt->execute([$newTheme, (int) $_SESSION['user_id']]);

        $_SESSION['theme'] = $newTheme;
        $_SESSION['success'] = 'Thème changé.';

        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        Router::redirect($referer);
    }
}
