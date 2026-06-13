<?php

class Lesson
{
    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare(
            'SELECT l.*, m.course_id, m.title as module_title
             FROM lessons l
             JOIN modules m ON l.module_id = m.id
             WHERE l.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByModule(int $moduleId): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT * FROM lessons WHERE module_id = ? ORDER BY sort_order ASC'
        );
        $stmt->execute([$moduleId]);
        return $stmt->fetchAll();
    }

    public static function countByCourse(int $courseId): int
    {
        $stmt = Database::connect()->prepare(
            'SELECT COUNT(*) FROM lessons l
             JOIN modules m ON l.module_id = m.id
             WHERE m.course_id = ?'
        );
        $stmt->execute([$courseId]);
        return (int) $stmt->fetchColumn();
    }

    public static function getCourseLessons(int $courseId): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT l.*, m.title as module_title, m.sort_order as module_sort
             FROM lessons l
             JOIN modules m ON l.module_id = m.id
             WHERE m.course_id = ?
             ORDER BY m.sort_order ASC, l.sort_order ASC'
        );
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public static function create(array $data): int
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO lessons (module_id, title, content, video_url, duration, sort_order)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['module_id'],
            $data['title'],
            $data['content'] ?? null,
            $data['video_url'] ?? null,
            $data['duration'] ?? null,
            $data['sort_order'] ?? 0,
        ]);
        return (int) Database::connect()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::connect()->prepare(
            'UPDATE lessons SET title = ?, content = ?, video_url = ?, duration = ?, sort_order = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['title'],
            $data['content'] ?? null,
            $data['video_url'] ?? null,
            $data['duration'] ?? null,
            $data['sort_order'] ?? 0,
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connect()->prepare('DELETE FROM lessons WHERE id = ?');
        $stmt->execute([$id]);
    }
}
