<?php

class Notification
{
    public static function create(int $userId, string $type, string $message, ?string $link = null): int
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO notifications (user_id, type, message, link) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $type, $message, $link]);
        return (int) Database::connect()->lastInsertId();
    }

    public static function findByUser(int $userId): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function unreadCount(int $userId): int
    {
        $stmt = Database::connect()->prepare(
            'SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0'
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public static function markAsRead(int $id): void
    {
        $stmt = Database::connect()->prepare('UPDATE notifications SET is_read = 1 WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function markAllAsRead(int $userId): void
    {
        $stmt = Database::connect()->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
        $stmt->execute([$userId]);
    }

    public static function all(): array
    {
        $stmt = Database::connect()->query(
            'SELECT n.*, u.full_name as user_name
             FROM notifications n
             JOIN users u ON n.user_id = u.id
             ORDER BY n.created_at DESC LIMIT 50'
        );
        return $stmt->fetchAll();
    }

    public static function sendToAll(string $type, string $message, ?string $link = null): void
    {
        $users = Database::connect()->query('SELECT id FROM users')->fetchAll();
        $stmt = Database::connect()->prepare(
            'INSERT INTO notifications (user_id, type, message, link) VALUES (?, ?, ?, ?)'
        );
        foreach ($users as $u) {
            $stmt->execute([$u['id'], $type, $message, $link]);
        }
    }
}
