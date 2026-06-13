<?php

class Comment
{
    public static function all(int $lessonId): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT c.*, u.full_name, u.avatar
             FROM comments c
             JOIN users u ON c.user_id = u.id
             WHERE c.lesson_id = ?
             ORDER BY c.created_at ASC'
        );
        $stmt->execute([$lessonId]);
        return $stmt->fetchAll();
    }

    public static function create(int $lessonId, int $userId, string $content): int
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO comments (lesson_id, user_id, content) VALUES (?, ?, ?)'
        );
        $stmt->execute([$lessonId, $userId, $content]);
        return (int) Database::connect()->lastInsertId();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM comments WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connect()->prepare('DELETE FROM comments WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function countByLesson(int $lessonId): int
    {
        $stmt = Database::connect()->prepare('SELECT COUNT(*) FROM comments WHERE lesson_id = ?');
        $stmt->execute([$lessonId]);
        return (int) $stmt->fetchColumn();
    }

    public static function count(): int
    {
        return (int) Database::connect()->query('SELECT COUNT(*) FROM comments')->fetchColumn();
    }
}
