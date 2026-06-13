<?php

class QuizController
{
    public function submit(array $params): void
    {
        Auth::require();

        $slug = $params['slug'] ?? '';
        $lessonId = $params['id'] ?? 0;

        $course = Course::findBySlug($slug);
        if (!$course) {
            Router::redirect('/courses');
        }

        $quiz = Quiz::findByLesson($lessonId);
        if (!$quiz) {
            Router::redirect('/courses/' . $course['slug']);
        }

        $questions = Quiz::getQuestions($quiz['id']);
        $total = count($questions);
        $score = 0;

        foreach ($questions as $q) {
            $answer = $_POST['question_' . $q['id']] ?? null;
            if ($answer) {
                foreach ($q['options'] as $opt) {
                    if ((int) $opt['id'] === (int) $answer && $opt['is_correct']) {
                        $score++;
                    }
                }
            }
        }

        $passed = $total > 0 && (($score / $total) * 100) >= $quiz['pass_score'];
        Quiz::saveAttempt($_SESSION['user_id'], $quiz['id'], $score, $total, $passed);

        Enrollment::completeLesson($_SESSION['user_id'], (int) $lessonId);

        $allLessons = Lesson::getCourseLessons($course['id']);
        $allCompleted = true;
        $completedCount = 0;

        foreach ($allLessons as $l) {
            if (Enrollment::isLessonCompleted($_SESSION['user_id'], $l['id'])) {
                $completedCount++;
            } else {
                $allCompleted = false;
            }
        }

        if ($allCompleted && $total > 0) {
            Enrollment::markCompleted($_SESSION['user_id'], $course['id']);
        }

        $_SESSION['quiz_result'] = [
            'score'   => $score,
            'total'   => $total,
            'passed'  => $passed,
            'percent' => $total > 0 ? round(($score / $total) * 100) : 0,
        ];

        Router::redirect('/courses/' . $course['slug'] . '/lessons/' . $lessonId);
    }
}
