<?php

class DashboardController
{
    public function index(): void
    {
        Auth::require();

        $user = Auth::user();
        $enrollments = Enrollment::findByUser($user['id']);

        Router::render('dashboard/index', [
            'user'        => $user,
            'enrollments' => $enrollments,
        ], 'main');
    }
}
