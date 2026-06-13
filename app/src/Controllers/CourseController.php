<?php

class CourseController
{
    public function index(): void
    {
        $search    = $_GET['search'] ?? '';
        $category  = $_GET['category'] ?? '';
        $categories = Category::all();

        if (!empty($search)) {
            $courses = Course::search($search);
        } elseif (!empty($category)) {
            $cat = Category::findBySlug($category);
            $courses = $cat ? Course::findByCategory($cat['id']) : [];
        } else {
            $courses = Course::allPublished();
        }

        Router::render('courses/index', [
            'courses'    => $courses,
            'categories' => $categories,
            'search'     => $search,
            'selectedCategory' => $category,
        ], 'main');
    }

    public function show(array $params): void
    {
        $slug = $params['slug'] ?? '';
        $course = Course::findBySlug($slug);

        if (!$course) {
            Router::render('errors/404', [], 'main');
            return;
        }

        $modules = Module::findByCourse($course['id']);
        $isEnrolled = Auth::check() && Enrollment::isEnrolled($_SESSION['user_id'], $course['id']);
        $studentCount = Enrollment::countByCourse($course['id']);

        Router::render('courses/show', [
            'course'        => $course,
            'modules'       => $modules,
            'isEnrolled'    => $isEnrolled,
            'studentCount'  => $studentCount,
        ], 'main');
    }

    public function enroll(array $params): void
    {
        Auth::require();

        $slug = $params['slug'] ?? '';
        $course = Course::findBySlug($slug);

        if (!$course) {
            Router::redirect('/courses');
        }

        Enrollment::enroll($_SESSION['user_id'], $course['id']);
        $_SESSION['success'] = 'Inscription réussie au cours : ' . $course['title'];
        Router::redirect('/courses/' . $course['slug']);
    }
}
