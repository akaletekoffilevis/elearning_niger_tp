<?php

class AdminController
{
    public function dashboard(): void
    {
        Auth::requireRole('admin');

        $stats = [
            'users'       => User::count(),
            'courses'     => Course::count(),
            'published'   => Course::countPublished(),
            'enrollments' => Enrollment::count(),
        ];

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

        Router::render('admin/dashboard', [
            'stats'             => $stats,
            'recentUsers'       => $recentUsers,
            'recentEnrollments' => $recentEnrollments,
        ], 'admin');
    }

    public function users(): void
    {
        Auth::requireRole('admin');
        $users = User::all();
        Router::render('admin/users', ['users' => $users], 'admin');
    }

    public function courses(): void
    {
        Auth::requireRole('admin');
        $courses = Course::all();
        Router::render('admin/courses', ['courses' => $courses], 'admin');
    }

    public function courseForm(array $params = []): void
    {
        Auth::requireRole('admin');

        $course = null;
        $courseId = $params['id'] ?? null;
        if ($courseId) {
            $course = Course::find((int) $courseId);
        }

        $categories = Category::all();
        Router::render('admin/course_form', [
            'course'     => $course,
            'categories' => $categories,
        ], 'admin');
    }

    public function courseSave(array $params = []): void
    {
        Auth::requireRole('admin');

        $courseId = $params['id'] ?? null;
        $userId = $_SESSION['user_id'];
        $data = [
            'title'       => $_POST['title'] ?? '',
            'slug'        => $_POST['slug'] ?? '',
            'description' => $_POST['description'] ?? '',
            'content'     => $_POST['content'] ?? '',
            'thumbnail'   => $_POST['thumbnail'] ?? '',
            'category_id' => !empty($_POST['category_id']) ? (int) $_POST['category_id'] : null,
            'status'      => $_POST['status'] ?? 'draft',
            'user_id'     => $userId,
        ];

        if (empty($data['slug'])) {
            $data['slug'] = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $data['title']), '-'));
        }

        if ($courseId) {
            Course::update((int) $courseId, $data);
            $_SESSION['success'] = 'Cours mis à jour.';
        } else {
            $courseId = Course::create($data);
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
            'course_id'  => $courseId,
            'title'      => $_POST['title'] ?? '',
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
        if (!$module) {
            Router::redirect('/admin/courses');
        }

        $lessons = Lesson::findByModule($moduleId);
        Router::render('admin/lessons', [
            'module'  => $module,
            'lessons' => $lessons,
        ], 'admin');
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
        if (!$module) {
            Router::redirect('/admin/courses');
        }

        Router::render('admin/lesson_form', [
            'lesson' => $lesson,
            'module' => $module,
        ], 'admin');
    }

    public function lessonSave(array $params = []): void
    {
        Auth::requireRole('admin');

        $lessonId = $params['id'] ?? null;
        $moduleId = (int) ($_POST['module_id'] ?? 0);

        $data = [
            'module_id'  => $moduleId,
            'title'      => $_POST['title'] ?? '',
            'content'    => $_POST['content'] ?? '',
            'video_url'  => $_POST['video_url'] ?? '',
            'duration'   => !empty($_POST['duration']) ? (int) $_POST['duration'] : null,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ];

        if ($lessonId) {
            Lesson::update((int) $lessonId, $data);
            $_SESSION['success'] = 'Leçon mise à jour.';
        } else {
            Lesson::create($data);
            $_SESSION['success'] = 'Leçon créée.';
        }

        $module = Module::find($moduleId);
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
            'name'        => $_POST['name'] ?? '',
            'slug'        => $_POST['slug'] ?? '',
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
}
