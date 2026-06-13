<?php

class Quiz
{
    public static function findByLesson(int $lessonId): ?array
    {
        $stmt = Database::connect()->prepare(
            'SELECT * FROM quizzes WHERE lesson_id = ?'
        );
        $stmt->execute([$lessonId]);
        return $stmt->fetch() ?: null;
    }

    public static function getQuestions(int $quizId): array
    {
        $stmt = Database::connect()->prepare(
            'SELECT qq.*, qo.id as option_id, qo.option_text, qo.is_correct, qo.sort_order as opt_sort
             FROM quiz_questions qq
             LEFT JOIN quiz_options qo ON qq.id = qo.question_id
             WHERE qq.quiz_id = ?
             ORDER BY qq.sort_order ASC, qo.sort_order ASC'
        );
        $stmt->execute([$quizId]);
        $rows = $stmt->fetchAll();

        $questions = [];
        foreach ($rows as $row) {
            $qid = $row['id'];
            if (!isset($questions[$qid])) {
                $questions[$qid] = [
                    'id'       => $row['id'],
                    'question' => $row['question'],
                    'options'  => [],
                ];
            }
            if ($row['option_id']) {
                $questions[$qid]['options'][] = [
                    'id'         => $row['option_id'],
                    'text'       => $row['option_text'],
                    'is_correct' => $row['is_correct'],
                ];
            }
        }

        return array_values($questions);
    }

    public static function getUserScore(int $userId, int $quizId): ?array
    {
        $stmt = Database::connect()->prepare(
            'SELECT * FROM quiz_attempts WHERE user_id = ? AND quiz_id = ? ORDER BY attempted_at DESC LIMIT 1'
        );
        $stmt->execute([$userId, $quizId]);
        return $stmt->fetch() ?: null;
    }

    public static function saveAttempt(int $userId, int $quizId, int $score, int $total, bool $passed): void
    {
        $stmt = Database::connect()->prepare(
            'INSERT INTO quiz_attempts (user_id, quiz_id, score, total, passed) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $quizId, $score, $total, $passed ? 1 : 0]);
    }
}
