<?php

class CertificateController
{
    public function download(array $params): void
    {
        Auth::require();

        $courseId = (int) ($params['course_id'] ?? 0);

        $cert = Certificate::findByUserCourse((int) $_SESSION['user_id'], $courseId);
        if (!$cert) {
            Router::redirect('/dashboard');
        }

        $course = Course::find($courseId);
        $user = Auth::user();

        Router::render('certificates/download', [
            'cert' => $cert,
            'course' => $course,
            'user' => $user,
        ], null);
    }
}
