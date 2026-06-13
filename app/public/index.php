<?php

session_start();

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Router.php';

require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Category.php';
require_once __DIR__ . '/../src/Models/Course.php';
require_once __DIR__ . '/../src/Models/Module.php';
require_once __DIR__ . '/../src/Models/Lesson.php';
require_once __DIR__ . '/../src/Models/Enrollment.php';
require_once __DIR__ . '/../src/Models/Quiz.php';

require_once __DIR__ . '/../src/Controllers/HomeController.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/CourseController.php';
require_once __DIR__ . '/../src/Controllers/LessonController.php';
require_once __DIR__ . '/../src/Controllers/QuizController.php';
require_once __DIR__ . '/../src/Controllers/DashboardController.php';
require_once __DIR__ . '/../src/Controllers/AdminController.php';

$router = new Router();

$home = new HomeController();
$auth = new AuthController();
$courseCtrl = new CourseController();
$lessonCtrl = new LessonController();
$quizCtrl = new QuizController();
$dash = new DashboardController();
$admin = new AdminController();

// Home
$router->get('/', [$home, 'index']);

// Auth
$router->get('/login', [$auth, 'loginForm']);
$router->post('/login', [$auth, 'login']);
$router->get('/register', [$auth, 'registerForm']);
$router->post('/register', [$auth, 'register']);
$router->get('/logout', [$auth, 'logout']);

// Courses
$router->get('/courses', [$courseCtrl, 'index']);
$router->get('/courses/{slug}', [$courseCtrl, 'show']);
$router->post('/courses/{slug}/enroll', [$courseCtrl, 'enroll']);

// Lessons
$router->get('/courses/{slug}/lessons/{id}', [$lessonCtrl, 'show']);

// Quiz
$router->post('/courses/{slug}/lessons/{id}/quiz', [$quizCtrl, 'submit']);

// Dashboard
$router->get('/dashboard', [$dash, 'index']);

// Admin
$router->get('/admin', [$admin, 'dashboard']);
$router->get('/admin/users', [$admin, 'users']);
$router->get('/admin/courses', [$admin, 'courses']);
$router->get('/admin/courses/create', [$admin, 'courseForm']);
$router->post('/admin/courses/create', [$admin, 'courseSave']);
$router->get('/admin/courses/edit/{id}', [$admin, 'courseForm']);
$router->post('/admin/courses/edit/{id}', [$admin, 'courseSave']);
$router->get('/admin/courses/delete/{id}', [$admin, 'courseDelete']);
$router->post('/admin/courses/{course_id}/modules', [$admin, 'moduleSave']);
$router->get('/admin/modules/delete/{id}', [$admin, 'moduleDelete']);
$router->get('/admin/modules/{module_id}/lessons', [$admin, 'lessons']);
$router->get('/admin/modules/{module_id}/lessons/create', [$admin, 'lessonForm']);
$router->post('/admin/lessons/create', [$admin, 'lessonSave']);
$router->get('/admin/lessons/edit/{id}', [$admin, 'lessonForm']);
$router->post('/admin/lessons/edit/{id}', [$admin, 'lessonSave']);
$router->get('/admin/lessons/delete/{id}', [$admin, 'lessonDelete']);
$router->get('/admin/categories', [$admin, 'categories']);
$router->post('/admin/categories/save', [$admin, 'categorySave']);
$router->get('/admin/categories/delete/{id}', [$admin, 'categoryDelete']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
