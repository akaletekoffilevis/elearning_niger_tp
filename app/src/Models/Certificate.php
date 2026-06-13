<?php

class Certificate
{
    public static function findByUserCourse(int $userId, int $courseId): ?array
    {
        $stmt = Database::connect()->prepare(
            'SELECT * FROM certificates WHERE user_id = ? AND course_id = ?'
        );
        $stmt->execute([$userId, $courseId]);
        return $stmt->fetch() ?: null;
    }

    public static function findByUser(int $userId): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT c.*, co.title as course_title
             FROM certificates c
             JOIN courses co ON c.course_id = co.id
             WHERE c.user_id = ?
             ORDER BY c.issued_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function create(int $userId, int $courseId): int
    {
        $code = 'CERT-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $stmt = Database::connect()->prepare(
            'INSERT INTO certificates (user_id, course_id, certificate_code) VALUES (?, ?, ?)'
        );
        $stmt->execute([$userId, $courseId, $code]);
        return (int) Database::connect()->lastInsertId();
    }

    public static function all(): array
    {
        $stmt = Database::connect()->query(
            'SELECT cert.*, u.full_name, u.email, c.title as course_title
             FROM certificates cert
             JOIN users u ON cert.user_id = u.id
             JOIN courses c ON cert.course_id = c.id
             ORDER BY cert.issued_at DESC'
        );
        return $stmt->fetchAll();
    }
}
