<?php

class LessonController
{
    public function show(array $params): void
    {
        Auth::require();

        $slug = $params['slug'] ?? '';
        $lessonId = $params['id'] ?? 0;

        $course = Course::findBySlug($slug);
        if (!$course) {
            Router::redirect('/courses');
        }

        if (!Enrollment::isEnrolled($_SESSION['user_id'], $course['id'])) {
            Router::redirect('/courses/' . $course['slug']);
        }

        $lesson = Lesson::find($lessonId);
        if (!$lesson || $lesson['course_id'] != $course['id']) {
            Router::redirect('/courses/' . $course['slug']);
        }

        $allLessons = Lesson::getCourseLessons($course['id']);
        $modules = Module::findByCourse($course['id']);

        $currentIndex = array_search($lessonId, array_column($allLessons, 'id'));
        $prevLesson = $currentIndex > 0 ? $allLessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < count($allLessons) - 1 ? $allLessons[$currentIndex + 1] : null;

        $quiz = Quiz::findByLesson($lesson['id']);
        $questions = $quiz ? Quiz::getQuestions($quiz['id']) : [];
        $userScore = $quiz && Auth::check() ? Quiz::getUserScore($_SESSION['user_id'], $quiz['id']) : null;
        $isCompleted = Enrollment::isLessonCompleted($_SESSION['user_id'], $lesson['id']);

        Router::render('lessons/show', [
            'course'      => $course,
            'lesson'      => $lesson,
            'modules'     => $modules,
            'allLessons'  => $allLessons,
            'prevLesson'  => $prevLesson,
            'nextLesson'  => $nextLesson,
            'quiz'        => $quiz,
            'questions'   => $questions,
            'userScore'   => $userScore,
            'isCompleted' => $isCompleted,
        ], 'main');
    }

    public function complete(array $params): void
    {
        Auth::require();

        $slug = $params['slug'] ?? '';
        $lessonId = (int) ($params['id'] ?? 0);

        $course = Course::findBySlug($slug);
        if (!$course) Router::redirect('/courses');

        if (!Enrollment::isEnrolled($_SESSION['user_id'], $course['id'])) {
            Router::redirect('/courses/' . $course['slug']);
        }

        $lesson = Lesson::find($lessonId);
        if (!$lesson) Router::redirect('/courses/' . $course['slug']);

        Enrollment::completeLesson($_SESSION['user_id'], $lessonId);

        $allLessons = Lesson::getCourseLessons($course['id']);
        $allCompleted = true;

        foreach ($allLessons as $l) {
            if (!Enrollment::isLessonCompleted($_SESSION['user_id'], $l['id'])) {
                $allCompleted = false;
                break;
            }
        }

        if ($allCompleted) {
            Enrollment::markCompleted($_SESSION['user_id'], $course['id']);
            if (!Certificate::findByUserCourse($_SESSION['user_id'], $course['id'])) {
                Certificate::create($_SESSION['user_id'], $course['id']);
                Notification::create($_SESSION['user_id'], 'success',
                    'Félicitations ! Vous avez terminé le cours : ' . $course['title'],
                    '/certificate/' . $course['id']
                );
            }
        }

        $_SESSION['success'] = 'Leçon marquée comme terminée !';
        Router::redirect('/courses/' . $course['slug'] . '/lessons/' . $lessonId);
    }
}
