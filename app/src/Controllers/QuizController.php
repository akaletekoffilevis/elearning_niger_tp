<?php

class QuizController
{
    public function submit(array $params): void
    {
        Auth::require();

        $slug = $params['slug'] ?? '';
        $lessonId = (int) ($params['id'] ?? 0);

        $course = Course::findBySlug($slug);
        if (!$course) Router::redirect('/courses');

        $quiz = Quiz::findByLesson($lessonId);
        if (!$quiz) Router::redirect('/courses/' . $course['slug']);

        $questions = Quiz::getQuestions($quiz['id']);
        $total = count($questions);
        $score = 0;

        $userId = (int) $_SESSION['user_id'];

        $attemptId = Quiz::saveAttempt($userId, $quiz['id'], 0, $total, false);

        foreach ($questions as $q) {
            $answer = $_POST['question_' . $q['id']] ?? null;
            $correct = false;

            if ($answer) {
                foreach ($q['options'] as $opt) {
                    if ((int) $opt['id'] === (int) $answer && $opt['is_correct']) {
                        $score++;
                        $correct = true;
                    }
                }
            }

            $stmt = Database::connect()->prepare(
                'INSERT INTO quiz_answers (attempt_id, question_id, option_id, is_correct) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$attemptId, $q['id'], $answer ? (int) $answer : null, $correct ? 1 : 0]);
        }

        $passed = $total > 0 && (($score / $total) * 100) >= $quiz['pass_score'];

        $stmt = Database::connect()->prepare(
            'UPDATE quiz_attempts SET score = ?, passed = ? WHERE id = ?'
        );
        $stmt->execute([$score, $passed ? 1 : 0, $attemptId]);

        Enrollment::completeLesson($userId, $lessonId);

        $allLessons = Lesson::getCourseLessons($course['id']);
        $allCompleted = true;
        $completedCount = 0;

        foreach ($allLessons as $l) {
            if (Enrollment::isLessonCompleted($userId, $l['id'])) {
                $completedCount++;
            } else {
                $allCompleted = false;
            }
        }

        if ($allCompleted && $total > 0) {
            Enrollment::markCompleted($userId, $course['id']);
            if (!Certificate::findByUserCourse($userId, $course['id'])) {
                Certificate::create($userId, $course['id']);
                Notification::create($userId, 'success',
                    'Félicitations ! Vous avez terminé le cours : ' . $course['title'],
                    '/certificate/' . $course['id']
                );
            }
        }

        Router::redirect('/courses/' . $course['slug'] . '/lessons/' . $lessonId . '/results');
    }

    public function results(array $params): void
    {
        Auth::require();

        $slug = $params['slug'] ?? '';
        $lessonId = (int) ($params['id'] ?? 0);

        $course = Course::findBySlug($slug);
        if (!$course) Router::redirect('/courses');

        $lesson = Lesson::find($lessonId);
        if (!$lesson) Router::redirect('/courses/' . $course['slug']);

        $quiz = Quiz::findByLesson($lessonId);
        if (!$quiz) Router::redirect('/courses/' . $course['slug'] . '/lessons/' . $lessonId);

        $userId = (int) $_SESSION['user_id'];
        $attempt = Quiz::getUserScore($userId, $quiz['id']);
        if (!$attempt) Router::redirect('/courses/' . $course['slug'] . '/lessons/' . $lessonId);

        $questions = Quiz::getQuestions($quiz['id']);

        $answers = [];
        $stmt = Database::connect()->prepare(
            'SELECT qa.*, qo.option_text, qo.is_correct as opt_correct, qq.question
             FROM quiz_answers qa
             JOIN quiz_questions qq ON qa.question_id = qq.id
             LEFT JOIN quiz_options qo ON qa.option_id = qo.id
             WHERE qa.attempt_id = ?
             ORDER BY qq.sort_order ASC'
        );
        $stmt->execute([$attempt['id']]);
        $answerRows = $stmt->fetchAll();

        foreach ($answerRows as $row) {
            $answers[$row['question_id']] = $row;
        }

        $modules = Module::findByCourse($course['id']);
        $allLessons = Lesson::getCourseLessons($course['id']);

        Router::render('lessons/results', [
            'course'    => $course,
            'lesson'    => $lesson,
            'quiz'      => $quiz,
            'attempt'   => $attempt,
            'questions' => $questions,
            'answers'   => $answers,
            'modules'   => $modules,
            'allLessons'=> $allLessons,
        ], 'main');
    }
}
