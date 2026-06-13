<?php

class Module
{
    public static function findByCourse(int $courseId): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT * FROM modules WHERE course_id = ? ORDER BY sort_order ASC'
        );
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM modules WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO modules (course_id, title, sort_order) VALUES (?, ?, ?)'
        );
        $stmt->execute([
            $data['course_id'],
            $data['title'],
            $data['sort_order'] ?? 0,
        ]);
        return (int) Database::connect()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::connect()->prepare(
            'UPDATE modules SET title = ?, sort_order = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['title'],
            $data['sort_order'] ?? 0,
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connect()->prepare('DELETE FROM modules WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function findLessons(int $moduleId): array
    {
        return Lesson::findByModule($moduleId);
    }
}

    public static function findLessons(int $moduleId): array
    {
        return Lesson::findByModule($moduleId);
    }
}
