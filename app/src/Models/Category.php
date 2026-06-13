<?php

class Category
{
    public static function all(): array
    {
        $stmt = Database::connect()->query(
            'SELECT cat.*, (SELECT COUNT(*) FROM courses WHERE category_id = cat.id AND status = "published") as course_count
             FROM categories cat
             ORDER BY cat.name ASC'
        );
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = Database::connect()->prepare('SELECT * FROM categories WHERE slug = ?');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)'
        );
        $stmt->execute([$data['name'], $data['slug'], $data['description'] ?? null]);
        return (int) Database::connect()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = Database::connect()->prepare(
            'UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?'
        );
        $stmt->execute([$data['name'], $data['slug'], $data['description'] ?? null, $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = Database::connect()->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([$id]);
    }
}
