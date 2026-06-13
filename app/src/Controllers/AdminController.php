<?php

class AdminController
{
    public function dashboard(): void
    {
        Auth::requireRole('admin');

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;
        $usersTotal = User::count();
        $pagination = paginate($usersTotal, $page, $perPage);

        $recentUsers = Database::connect()->query(
            'SELECT * FROM users ORDER BY created_at DESC LIMIT 5'
        )->fetchAll();

        $recentEnrollments = Database::connect()->query(
            'SELECT e.*, u.full_name, u.email, c.title as course_title
             FROM enrollments e
             JOIN users u ON e.user_id = u.id
             JOIN courses c ON e.course_id = c.id
             ORDER BY e.enrolled_at DESC LIMIT 5'
        )->fetchAll();

        $stats = [
            'users'       => $usersTotal,
            'courses'     => Course::count(),
            'published'   => Course::countPublished(),
            'enrollments' => Enrollment::count(),
            'comments'    => Comment::count(),
            'certificates'=> count(Certificate::all()),
        ];

        Router::render('admin/dashboard', [
            'stats'             => $stats,
            'recentUsers'       => $recentUsers,
            'recentEnrollments' => $recentEnrollments,
        ], 'admin');
    }

    public function users(): void
    {
        Auth::requireRole('admin');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;
        $total = User::count();
        $pag = paginate($total, $page, $perPage);

        $users = Database::connect()->prepare(
            'SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?'
        );
        $users->execute([$pag['perPage'], $pag['offset']]);

        Router::render('admin/users', [
            'users' => $users->fetchAll(),
            'pag' => $pag,
        ], 'admin');
    }

    public function courses(): void
    {
        Auth::requireRole('admin');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 20;
        $total = Course::count();
        $pag = paginate($total, $page, $perPage);

        $courses = Database::connect()->prepare(
            'SELECT c.*, cat.name as category_name, u.full_name as instructor_name,
             (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as student_count
             FROM courses c
             LEFT JOIN categories cat ON c.category_id = cat.id
             LEFT JOIN users u ON c.user_id = u.id
             ORDER BY c.created_at DESC LIMIT ? OFFSET ?'
        );
        $courses->execute([$pag['perPage'], $pag['offset']]);

        Router::render('admin/courses', [
            'courses' => $courses->fetchAll(),
            'pag' => $pag,
        ], 'admin');
    }

    public function courseForm(array $params = []): void
    {
        Auth::requireRole('admin');
        $course = null;
        $courseId = $params['id'] ?? null;
        if ($courseId) $course = Course::find((int) $courseId);
        $categories = Category::all();
        Router::render('admin/course_form', [
            'course' => $course,
            'categories' => $categories,
        ], 'admin');
    }

    public function courseSave(array $params = []): void
    {
        Auth::requireRole('admin');
        $courseId = $params['id'] ?? null;
        $data = [
            'title' => $_POST['title'] ?? '',
            'slug' => $_POST['slug'] ?? '',
            'description' => $_POST['description'] ?? '',
            'content' => $_POST['content'] ?? '',
            'thumbnail' => $_POST['thumbnail'] ?? '',
            'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
            'status' => $_POST['status'] ?? 'draft',
            'user_id' => (int) $_SESSION['user_id'],
        ];
        if (empty($data['slug'])) {
            $data['slug'] = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $data['title']), '-'));
        }
        if ($courseId) {
            Course::update((int) $courseId, $data);
            $_SESSION['success'] = 'Cours mis à jour.';
        } else {
            Course::create($data);
            $_SESSION['success'] = 'Cours créé.';
        }
        Router::redirect('/admin/courses');
    }

    public function courseDelete(array $params): void
    {
        Auth::requireRole('admin');
        Course::delete((int) $params['id']);
        $_SESSION['success'] = 'Cours supprimé.';
        Router::redirect('/admin/courses');
    }

    public function moduleSave(array $params): void
    {
        Auth::requireRole('admin');
        $courseId = (int) ($params['course_id'] ?? 0);
        Module::create([
            'course_id' => $courseId,
            'title' => $_POST['title'] ?? '',
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ]);
        $_SESSION['success'] = 'Module ajouté.';
        Router::redirect('/admin/courses/edit/' . $courseId);
    }

    public function moduleDelete(array $params): void
    {
        Auth::requireRole('admin');
        $module = Module::find((int) $params['id']);
        if ($module) {
            Module::delete($module['id']);
            $_SESSION['success'] = 'Module supprimé.';
            Router::redirect('/admin/courses/edit/' . $module['course_id']);
        }
        Router::redirect('/admin/courses');
    }

    public function lessons(array $params): void
    {
        Auth::requireRole('admin');
        $moduleId = (int) ($params['module_id'] ?? 0);
        $module = Module::find($moduleId);
        if (!$module) Router::redirect('/admin/courses');
        $lessons = Lesson::findByModule($moduleId);
        Router::render('admin/lessons', ['module' => $module, 'lessons' => $lessons], 'admin');
    }

    public function lessonForm(array $params = []): void
    {
        Auth::requireRole('admin');
        $lesson = null;
        $lessonId = $params['id'] ?? null;
        $moduleId = (int) ($params['module_id'] ?? 0);
        if ($lessonId) {
            $lesson = Lesson::find((int) $lessonId);
            $moduleId = $lesson ? $lesson['module_id'] : $moduleId;
        }
        $module = Module::find($moduleId);
        if (!$module) Router::redirect('/admin/courses');
        Router::render('admin/lesson_form', ['lesson' => $lesson, 'module' => $module], 'admin');
    }

    public function lessonSave(array $params = []): void
    {
        Auth::requireRole('admin');
        $lessonId = $params['id'] ?? null;
        $moduleId = (int) ($_POST['module_id'] ?? 0);
        $data = [
            'module_id' => $moduleId,
            'title' => $_POST['title'] ?? '',
            'content' => $_POST['content'] ?? '',
            'video_url' => $_POST['video_url'] ?? '',
            'duration' => !empty($_POST['duration']) ? (int) $_POST['duration'] : null,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ];
        if ($lessonId) {
            Lesson::update((int) $lessonId, $data);
            $_SESSION['success'] = 'Leçon mise à jour.';
        } else {
            Lesson::create($data);
            $_SESSION['success'] = 'Leçon créée.';
        }
        Router::redirect('/admin/modules/' . $moduleId . '/lessons');
    }

    public function lessonDelete(array $params): void
    {
        Auth::requireRole('admin');
        $lesson = Lesson::find((int) $params['id']);
        if ($lesson) {
            $module = Module::find($lesson['module_id']);
            Lesson::delete($lesson['id']);
            $_SESSION['success'] = 'Leçon supprimée.';
            Router::redirect('/admin/modules/' . $module['id'] . '/lessons');
        }
        Router::redirect('/admin/courses');
    }

    public function categories(): void
    {
        Auth::requireRole('admin');
        $allCategories = Category::all();
        Router::render('admin/categories', ['categories' => $allCategories], 'admin');
    }

    public function categorySave(): void
    {
        Auth::requireRole('admin');
        $id = $_POST['id'] ?? null;
        $data = [
            'name' => $_POST['name'] ?? '',
            'slug' => $_POST['slug'] ?? '',
            'description' => $_POST['description'] ?? '',
        ];
        if (empty($data['slug'])) {
            $data['slug'] = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $data['name']), '-'));
        }
        if ($id) {
            Category::update((int) $id, $data);
        } else {
            Category::create($data);
        }
        $_SESSION['success'] = 'Catégorie sauvegardée.';
        Router::redirect('/admin/categories');
    }

    public function categoryDelete(array $params): void
    {
        Auth::requireRole('admin');
        Category::delete((int) $params['id']);
        $_SESSION['success'] = 'Catégorie supprimée.';
        Router::redirect('/admin/categories');
    }

    // === NEW QUIZ MANAGEMENT ===
    public function quizForm(array $params): void
    {
        Auth::requireRole('admin');
        $lessonId = (int) ($params['lesson_id'] ?? 0);
        $lesson = Lesson::find($lessonId);
        if (!$lesson) Router::redirect('/admin/courses');

        $quiz = Quiz::findByLesson($lessonId);
        $questions = $quiz ? Quiz::getQuestions($quiz['id']) : [];

        Router::render('admin/quiz_form', [
            'lesson' => $lesson,
            'quiz' => $quiz,
            'questions' => $questions,
        ], 'admin');
    }

    public function quizSave(): void
    {
        Auth::requireRole('admin');
        $lessonId = (int) ($_POST['lesson_id'] ?? 0);

        $existing = Quiz::findByLesson($lessonId);
        if ($existing) {
            $stmt = Database::connect()->prepare('UPDATE quizzes SET title = ?, pass_score = ? WHERE id = ?');
            $stmt->execute([$_POST['title'] ?? 'Quiz', (int) ($_POST['pass_score'] ?? 80), $existing['id']]);
            $quizId = $existing['id'];
        } else {
            $stmt = Database::connect()->prepare('INSERT INTO quizzes (lesson_id, title, pass_score) VALUES (?, ?, ?)');
            $stmt->execute([$lessonId, $_POST['title'] ?? 'Quiz', (int) ($_POST['pass_score'] ?? 80)]);
            $quizId = (int) Database::connect()->lastInsertId();
        }

        $questions = $_POST['questions'] ?? [];
        foreach ($questions as $qData) {
            if (empty(trim($qData['question']))) continue;

            $stmt = Database::connect()->prepare(
                'INSERT INTO quiz_questions (quiz_id, question, sort_order) VALUES (?, ?, ?)'
            );
            $stmt->execute([$quizId, $qData['question'], (int) ($qData['sort_order'] ?? 0)]);
            $questionId = (int) Database::connect()->lastInsertId();

            $options = $qData['options'] ?? [];
            foreach ($options as $optData) {
                if (empty(trim($optData['text']))) continue;
                $stmt = Database::connect()->prepare(
                    'INSERT INTO quiz_options (question_id, option_text, is_correct, sort_order) VALUES (?, ?, ?, ?)'
                );
                $stmt->execute([$questionId, $optData['text'], (int) ($optData['is_correct'] ?? 0), (int) ($optData['sort_order'] ?? 0)]);
            }
        }

        $_SESSION['success'] = 'Quiz sauvegardé.';
        Router::redirect('/admin/lessons/edit/' . $lessonId);
    }

    // === NOTIFICATIONS ===
    public function notifications(): void
    {
        Auth::requireRole('admin');
        $all = Notification::all();
        Router::render('admin/notifications', ['notifications' => $all], 'admin');
    }

    public function notificationSend(): void
    {
        Auth::requireRole('admin');
        $type = $_POST['type'] ?? 'info';
        $message = trim($_POST['message'] ?? '');
        $userId = $_POST['user_id'] ?? null;

        if (empty($message)) {
            $_SESSION['error'] = 'Message requis.';
            Router::redirect('/admin/notifications');
        }

        if ($userId === 'all') {
            Notification::sendToAll($type, $message);
            $_SESSION['success'] = 'Notification envoyée à tous.';
        } else {
            Notification::create((int) $userId, $type, $message);
            $_SESSION['success'] = 'Notification envoyée.';
        }

        Router::redirect('/admin/notifications');
    }

    // === CERTIFICATES ===
    public function certificates(): void
    {
        Auth::requireRole('admin');
        $all = Certificate::all();
        Router::render('admin/certificates', ['certificates' => $all], 'admin');
    }

    // === COMMENTS ===
    public function comments(): void
    {
        Auth::requireRole('admin');
        $stmt = Database::connect()->query(
            'SELECT c.*, u.full_name, l.title as lesson_title
             FROM comments c
             JOIN users u ON c.user_id = u.id
             JOIN lessons l ON c.lesson_id = l.id
             ORDER BY c.created_at DESC LIMIT 50'
        );
        Router::render('admin/comments', ['comments' => $stmt->fetchAll()], 'admin');
    }

    public function commentDelete(array $params): void
    {
        Auth::requireRole('admin');
        Comment::delete((int) $params['id']);
        $_SESSION['success'] = 'Commentaire supprimé.';
        Router::redirect('/admin/comments');
    }

    // === EXPORT CSV ===
    public function exportCsv(array $params): void
    {
        Auth::requireRole('admin');
        $type = $params['type'] ?? 'users';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $type . '_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        if ($type === 'users') {
            fputcsv($output, ['ID', 'Nom', 'Email', 'Pseudo', 'Role', 'Date']);
            $users = User::all();
            foreach ($users as $u) {
                fputcsv($output, [$u['id'], $u['full_name'], $u['email'], $u['username'], $u['role'], $u['created_at']]);
            }
        } elseif ($type === 'courses') {
            fputcsv($output, ['ID', 'Titre', 'Catégorie', 'Instructeur', 'Statut', 'Étudiants']);
            $courses = Database::connect()->query(
                'SELECT c.*, cat.name as category_name, u.full_name as instructor_name,
                 (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) as student_count
                 FROM courses c
                 LEFT JOIN categories cat ON c.category_id = cat.id
                 LEFT JOIN users u ON c.user_id = u.id
                 ORDER BY c.created_at DESC'
            )->fetchAll();
            foreach ($courses as $c) {
                fputcsv($output, [$c['id'], $c['title'], $c['category_name'] ?? '', $c['instructor_name'], $c['status'], $c['student_count'] ?? 0]);
            }
        } elseif ($type === 'enrollments') {
            fputcsv($output, ['ID', 'Étudiant', 'Email', 'Cours', 'Date', 'Complété']);
            $stmt = Database::connect()->query(
                'SELECT e.*, u.full_name, u.email, c.title FROM enrollments e
                 JOIN users u ON e.user_id = u.id JOIN courses c ON e.course_id = c.id
                 ORDER BY e.enrolled_at DESC'
            );
            while ($row = $stmt->fetch()) {
                fputcsv($output, [$row['id'], $row['full_name'], $row['email'], $row['title'], $row['enrolled_at'], $row['completed'] ? 'Oui' : 'Non']);
            }
        }

        fclose($output);
        exit;
    }
}
