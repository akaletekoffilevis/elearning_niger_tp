<?php

class ReviewController
{
    public function store(array $params): void
    {
        Auth::require();

        $slug = $params['slug'] ?? '';
        $course = Course::findBySlug($slug);
        if (!$course) Router::redirect('/courses');

        if (!Enrollment::isEnrolled($_SESSION['user_id'], $course['id'])) {
            $_SESSION['error'] = 'Vous devez être inscrit pour laisser un avis.';
            Router::redirect('/courses/' . $slug);
        }

        $rating = (int) ($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            $_SESSION['error'] = 'Note invalide (1-5).';
            Router::redirect('/courses/' . $slug);
        }

        Review::create($course['id'], (int) $_SESSION['user_id'], $rating, $comment ?: null);
        $_SESSION['success'] = 'Avis enregistré. Merci !';
        Router::redirect('/courses/' . $slug);
    }
}
