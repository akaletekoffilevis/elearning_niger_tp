<?php

class Course
{
    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare(
            'SELECT c.*, cat.name as category_name, u.full_name as instructor_name
             FROM courses c
             LEFT JOIN categories cat ON c.category_id = cat.id
             LEFT JOIN users u ON c.user_id = u.id
             WHERE c.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = Database::connect()->prepare(
            'SELECT c.*, cat.name as category_name, u.full_name as instructor_name
             FROM courses c
             LEFT JOIN categories cat ON c.category_id = cat.id
             LEFT JOIN users u ON c.user_id = u.id
             WHERE c.slug = ?'
        );
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public static function allPublished(): array
    {
        $stmt = Database::connect()->query(
            'SELECT c.*, cat.name as category_name, u.full_name as instructor_name
             FROM courses c
             LEFT JOIN categories cat ON c.category_id = cat.id
             LEFT JOIN users u ON c.user_id = u.id
             WHERE c.status = "published"
             ORDER BY c.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        $stmt = Database::connect()->query(
            'SELECT c.*, cat.name as category_name, u.full_name as instructor_name,
             (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as student_count
             FROM courses c
             LEFT JOIN categories cat ON c.category_id = cat.id
             LEFT JOIN users u ON c.user_id = u.id
             ORDER BY c.created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public static function search(string $query): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT c.*, cat.name as category_name, u.full_name as instructor_name
             FROM courses c
             LEFT JOIN categories cat ON c.category_id = cat.id
             LEFT JOIN users u ON c.user_id = u.id
             WHERE c.status = "published" AND (c.title LIKE ? OR c.description LIKE ?)
             ORDER BY c.created_at DESC'
        );
        $like = '%' . $query . '%';
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    public static function findByCategory(int $categoryId): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT c.*, cat.name as category_name, u.full_name as instructor_name
             FROM courses c
             LEFT JOIN categories cat ON c.category_id = cat.id
             LEFT JOIN users u ON c.user_id = u.id
             WHERE c.status = "published" AND c.category_id = ?
             ORDER BY c.created_at DESC'
        );
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }

    public static function count(): int
    {
        return (int) Database::connect()->query('SELECT COUNT(*) FROM courses')->fetchColumn();
    }

    public static function countPublished(): int
    {
        $stmt = Database::connect()->prepare("SELECT COUNT(*) FROM courses WHERE status = 'published'");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public static function create(array $data): int
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO courses (title, slug, description, content, thumbnail, category_id, user_id, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['description'] ?? null,
            $data['content'] ?? null,
            $data['thumbnail'] ?? null,
            $data['category_id'] ?? null,
            $data['user_id'],
            $data['status'] ?? 'draft',
        ]);
        return (int) Database::connect()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::connect()->prepare(
            'UPDATE courses SET title = ?, slug = ?, description = ?, content = ?, thumbnail = ?,
             category_id = ?, status = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['title'],
            $data['slug'],
            $data['description'] ?? null,
            $data['content'] ?? null,
            $data['thumbnail'] ?? null,
            $data['category_id'] ?? null,
            $data['status'] ?? 'draft',
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connect()->prepare('DELETE FROM courses WHERE id = ?');
        $stmt->execute([$id]);
    }
}
