<?php

class AuthController
{
    public function loginForm(): void
    {
        Router::render('auth/login', [], 'main');
    }

    public function login(): void
    {
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            Router::redirect('/login');
        }

        $user = User::findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Email ou mot de passe incorrect.';
            Router::redirect('/login');
        }

        Auth::login($user['id']);
        $_SESSION['success'] = 'Bon retour, ' . $user['full_name'] . ' !';
        Router::redirect('/');
    }

    public function registerForm(): void
    {
        Router::render('auth/register', [], 'main');
    }

    public function register(): void
    {
        $username = $_POST['username'] ?? '';
        $email    = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $fullName = $_POST['full_name'] ?? '';

        if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs.';
            Router::redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Email invalide.';
            Router::redirect('/register');
        }

        if (User::findByEmail($email)) {
            $_SESSION['error'] = 'Cet email est déjà utilisé.';
            Router::redirect('/register');
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            Router::redirect('/register');
        }

        $userId = User::create([
            'username'  => $username,
            'email'     => $email,
            'password'  => $password,
            'full_name' => $fullName,
        ]);

        Auth::login($userId);
        $_SESSION['success'] = 'Compte créé avec succès !';
        Router::redirect('/');
    }

    public function logout(): void
    {
        Auth::logout();
        $_SESSION['success'] = 'Vous êtes déconnecté.';
        Router::redirect('/');
    }
}
