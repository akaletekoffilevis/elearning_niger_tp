<?php

class CommentController
{
    public function store(array $params): void
    {
        Auth::require();

        $slug = $params['slug'] ?? '';
        $lessonId = (int) ($params['id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        if (empty($content)) {
            $_SESSION['error'] = 'Le commentaire ne peut pas être vide.';
            Router::redirect('/courses/' . $slug . '/lessons/' . $lessonId);
        }

        $course = Course::findBySlug($slug);
        if (!$course) Router::redirect('/courses');

        $lesson = Lesson::find($lessonId);
        if (!$lesson) Router::redirect('/courses/' . $slug);

        Comment::create($lessonId, (int) $_SESSION['user_id'], $content);
        $_SESSION['success'] = 'Commentaire ajouté.';
        Router::redirect('/courses/' . $slug . '/lessons/' . $lessonId);
    }

    public function delete(array $params): void
    {
        Auth::require();

        $commentId = (int) ($params['id'] ?? 0);
        $comment = Comment::find($commentId);

        if (!$comment) {
            $_SESSION['error'] = 'Commentaire introuvable.';
            Router::redirect('/');
        }

        if ($comment['user_id'] != $_SESSION['user_id'] && !Auth::isAdmin()) {
            $_SESSION['error'] = 'Action non autorisée.';
            Router::redirect('/');
        }

        Comment::delete($commentId);
        $_SESSION['success'] = 'Commentaire supprimé.';
        Router::redirect('/courses/' . $params['slug'] . '/lessons/' . $params['lesson_id']);
    }
}
