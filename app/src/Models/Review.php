<?php

class Review
{
    public static function findByCourse(int $courseId): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT r.*, u.full_name, u.avatar
             FROM reviews r
             JOIN users u ON r.user_id = u.id
             WHERE r.course_id = ?
             ORDER BY r.created_at DESC'
        );
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public static function findByUserCourse(int $userId, int $courseId): ?array
    {
        $stmt = Database::connect()->prepare(
            'SELECT * FROM reviews WHERE user_id = ? AND course_id = ?'
        );
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch() ?: null;
    }

    public static function create(int $courseId, int $userId, int $rating, ?string $comment): int
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO reviews (course_id, user_id, rating, comment) VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)'
        );
        $stmt->execute([$courseId, $userId, $rating, $comment]);
        return (int) Database::connect()->lastInsertId();
    }

    public static function getAverage(int $courseId): float
    {
        $stmt = Database::connect()->prepare('SELECT AVG(rating) FROM reviews WHERE course_id = ?');
        $stmt->execute([$courseId]);
        $avg = $stmt->fetchColumn();
        return $avg ? round((float) $avg, 1) : 0.0;
    }

    public static function count(int $courseId): int
    {
        $stmt = Database::connect()->prepare('SELECT COUNT(*) FROM reviews WHERE course_id = ?');
        $stmt->execute([$courseId]);
        return (int) $stmt->fetchColumn();
    }
}
