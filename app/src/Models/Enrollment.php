<?php

class Enrollment
{
    public static function enroll(int $userId, int $courseId): void
    {
        $stmt = Database::connect()->prepare(
            'INSERT IGNORE INTO enrollments (user_id, course_id) VALUES (?, ?)'
        );
        $stmt->execute([$userId, $courseId]);
    }

    public static function isEnrolled(int $userId, int $courseId): bool
    {
        $stmt = Database::connect()->prepare(
            'SELECT COUNT(*) FROM enrollments WHERE user_id = ? AND course_id = ?'
        );
        $stmt->execute([$userId, $courseId]);
        return (bool) $stmt->fetchColumn();
    }

    public static function findByUser(int $userId): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT e.*, c.title, c.slug, c.thumbnail,
             (SELECT COUNT(*) FROM lessons l
              JOIN modules m ON l.module_id = m.id
              WHERE m.course_id = c.id) as total_lessons,
             (SELECT COUNT(*) FROM lesson_progress lp
              JOIN lessons l ON lp.lesson_id = l.id
              JOIN modules m ON l.module_id = m.id
              WHERE m.course_id = c.id AND lp.user_id = e.user_id AND lp.completed = 1) as completed_lessons
             FROM enrollments e
             JOIN courses c ON e.course_id = c.id
             WHERE e.user_id = ?
             ORDER BY e.enrolled_at DESC'
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function countByCourse(int $courseId): int
    {
        $stmt = Database::connect()->prepare('SELECT COUNT(*) FROM enrollments WHERE course_id = ?');
        $stmt->execute([$courseId]);
        return (int) $stmt->fetchColumn();
    }

    public static function count(): int
    {
        return (int) Database::connect()->query('SELECT COUNT(*) FROM enrollments')->fetchColumn();
    }

    public static function markCompleted(int $userId, int $courseId): void
    {
        $stmt = Database::connect()->prepare(
            'UPDATE enrollments SET completed = 1, completed_at = NOW() WHERE user_id = ? AND course_id = ?'
        );
        $stmt->execute([$userId, $courseId]);
    }

    public static function completeLesson(int $userId, int $lessonId): void
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO lesson_progress (user_id, lesson_id, completed, completed_at)
             VALUES (?, ?, 1, NOW())
             ON DUPLICATE KEY UPDATE completed = 1, completed_at = NOW()'
        );
        $stmt->execute([$userId, $lessonId]);
    }

    public static function isLessonCompleted(int $userId, int $lessonId): bool
    {
        $stmt = Database::connect()->prepare(
            'SELECT completed FROM lesson_progress WHERE user_id = ? AND lesson_id = ?'
        );
        $stmt->execute([$userId, $lessonId]);
        $result = $stmt->fetch();
        return $result && $result['completed'];
    }
}
