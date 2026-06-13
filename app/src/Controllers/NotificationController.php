<?php

class NotificationController
{
    public function index(): void
    {
        Auth::require();

        $notifications = Notification::findByUser((int) $_SESSION['user_id']);

        Router::render('notifications/index', [
            'notifications' => $notifications,
        ], 'main');
    }

    public function read(array $params): void
    {
        Auth::require();
        Notification::markAsRead((int) ($params['id'] ?? 0));
        Router::redirect('/notifications');
    }

    public function readAll(): void
    {
        Auth::require();
        Notification::markAllAsRead((int) $_SESSION['user_id']);
        Router::redirect('/notifications');
    }
}
