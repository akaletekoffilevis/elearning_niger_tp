<?php
/**
 * Smoke test pour vérifier que l'application fonctionne avec SQLite
 * Usage: DB_DRIVER=sqlite php test_sqlite.php
 */

putenv('DB_DRIVER=sqlite');
putenv('DB_SQLITE_PATH=' . __DIR__ . '/app/data/elearning.db');

require_once __DIR__ . '/app/src/Database.php';
require_once __DIR__ . '/app/src/SQLiteConnection.php';
require_once __DIR__ . '/app/src/helpers.php';
require_once __DIR__ . '/app/src/Auth.php';
require_once __DIR__ . '/app/src/Router.php';

require_once __DIR__ . '/app/src/Models/User.php';
require_once __DIR__ . '/app/src/Models/Category.php';
require_once __DIR__ . '/app/src/Models/Course.php';
require_once __DIR__ . '/app/src/Models/Module.php';
require_once __DIR__ . '/app/src/Models/Lesson.php';
require_once __DIR__ . '/app/src/Models/Enrollment.php';
require_once __DIR__ . '/app/src/Models/Quiz.php';
require_once __DIR__ . '/app/src/Models/Comment.php';
require_once __DIR__ . '/app/src/Models/Review.php';
require_once __DIR__ . '/app/src/Models/Notification.php';
require_once __DIR__ . '/app/src/Models/Certificate.php';

$passed = 0;
$failed = 0;

function test(string $name, callable $fn): void
{
    global $passed, $failed;
    try {
        $fn();
        echo "  ✓ {$name}\n";
        $passed++;
    } catch (Throwable $e) {
        echo "  ✗ {$name} : {$e->getMessage()}\n";
        $failed++;
    }
}

echo "=== Smoke Tests SQLite ===\n\n";

echo "--- Modèle User ---\n";
test('find admin', function () {
    $user = User::find(1);
    assert($user !== null, 'admin introuvable');
    assert($user['email'] === 'admin@elearning.com', 'email incorrect');
    assert($user['role'] === 'admin', 'role incorrect');
});

test('findByEmail', function () {
    $user = User::findByEmail('admin@elearning.com');
    assert($user !== null, 'email introuvable');
    assert($user['username'] === 'admin', 'username incorrect');
});

test('count', function () {
    assert(User::count() === 1, 'count devrait être 1');
});

test('countByRole admin', function () {
    assert(User::countByRole('admin') === 1);
});

test('create user', function () {
    $id = User::create([
        'username' => 'testuser',
        'email' => 'test@test.com',
        'password' => 'test123',
        'full_name' => 'Test User',
    ]);
    assert($id > 1, 'id devrait être > 1');
    $user = User::find($id);
    assert($user !== null);
    assert($user['full_name'] === 'Test User');
});

test('all users', function () {
    $users = User::all();
    assert(count($users) >= 2);
});

echo "\n--- Modèle Category ---\n";
test('all categories', function () {
    $cats = Category::all();
    assert(count($cats) === 5);
});

test('find category by slug', function () {
    $cat = Category::findBySlug('developpement-web');
    assert($cat !== null);
    assert($cat['name'] === 'Développement Web');
});

test('create category', function () {
    $id = Category::create([
        'name' => 'Test Cat',
        'slug' => 'test-cat',
        'description' => 'Description test',
    ]);
    assert($id > 5);
    $cat = Category::find($id);
    assert($cat['name'] === 'Test Cat');
});

echo "\n--- Modèle Course ---\n";
test('create course', function () {
    $id = Course::create([
        'title' => 'Cours Test',
        'slug' => 'cours-test',
        'description' => 'Description du cours test',
        'content' => 'Contenu du cours test',
        'category_id' => 1,
        'user_id' => 1,
        'status' => 'published',
    ]);
    assert($id > 0);
});

test('find course', function () {
    $course = Course::find(1);
    assert($course !== null);
    assert($course['title'] === 'Cours Test');
});

test('findBySlug', function () {
    $course = Course::findBySlug('cours-test');
    assert($course !== null);
});

test('allPublished', function () {
    $courses = Course::allPublished();
    assert(count($courses) >= 1);
});

test('count', function () {
    assert(Course::count() >= 1);
});

test('countPublished', function () {
    assert(Course::countPublished() >= 1);
});

echo "\n--- Modèle Module ---\n";
test('create module', function () {
    $id = Module::create([
        'course_id' => 1,
        'title' => 'Module 1',
        'sort_order' => 1,
    ]);
    assert($id > 0);
});

test('findLessons (empty)', function () {
    $lessons = Module::findLessons(1);
    assert(is_array($lessons));
    assert(count($lessons) === 0);
});

echo "\n--- Modèle Lesson ---\n";
test('create lesson', function () {
    $id = Lesson::create([
        'module_id' => 1,
        'title' => 'Leçon 1',
        'content' => 'Contenu de la leçon',
        'sort_order' => 1,
    ]);
    assert($id > 0);
});

test('find lessons by module', function () {
    $lessons = Module::findLessons(1);
    assert(count($lessons) === 1);
    assert($lessons[0]['title'] === 'Leçon 1');
});

echo "\n--- Modèle Enrollment ---\n";
test('enroll user', function () {
    Enrollment::enroll(1, 1);
    assert(Enrollment::isEnrolled(1, 1));
});

test('count enrollments', function () {
    assert(Enrollment::countByCourse(1) >= 1);
});

echo "\n--- Modèle Quiz ---\n";
test('findByLesson (null)', function () {
    $quiz = Quiz::findByLesson(1);
    assert($quiz === null);
});

test('create quiz', function () {
    $stmt = Database::connect()->prepare('INSERT INTO quizzes (lesson_id, title, pass_score) VALUES (?, ?, ?)');
    $stmt->execute([1, 'Test Quiz', 80]);
    $quizId = (int) Database::connect()->lastInsertId();
    assert($quizId > 0);

    $stmt = Database::connect()->prepare('INSERT INTO quiz_questions (quiz_id, question, sort_order) VALUES (?, ?, ?)');
    $stmt->execute([$quizId, 'Question 1', 1]);
    $qId = (int) Database::connect()->lastInsertId();

    $stmt = Database::connect()->prepare('INSERT INTO quiz_options (question_id, option_text, is_correct, sort_order) VALUES (?, ?, ?, ?)');
    $stmt->execute([$qId, 'Bonne réponse', 1, 1]);
    $stmt->execute([$qId, 'Mauvaise réponse', 0, 2]);
});

test('findByLesson (found)', function () {
    $quiz = Quiz::findByLesson(1);
    assert($quiz !== null);
    assert($quiz['title'] === 'Test Quiz');
});

echo "\n--- Modèle Comment ---\n";
test('create comment', function () {
    $id = Comment::create(1, 1, 'Super leçon !');
    assert($id > 0);
});

test('all comments by lesson', function () {
    $comments = Comment::all(1);
    assert(count($comments) === 1);
    assert($comments[0]['content'] === 'Super leçon !');
});

echo "\n--- Modèle Review ---\n";
test('create review', function () {
    $id = Review::create(1, 1, 4, 'Bon cours');
    assert($id > 0);
});

test('getAverage', function () {
    $avg = Review::getAverage(1);
    assert($avg == 4.0);
});

echo "\n--- Modèle Notification ---\n";
test('create notification', function () {
    $id = Notification::create(1, 'info', 'Bienvenue !', '/');
    assert($id > 0);
});

test('findByUser', function () {
    $notifs = Notification::findByUser(1);
    assert(count($notifs) >= 1);
});

test('unreadCount', function () {
    $count = Notification::unreadCount(1);
    assert($count >= 1);
});

echo "\n--- Modèle Certificate ---\n";
test('create certificate', function () {
    $id = Certificate::create(1, 1);
    assert($id > 0);
});

test('findByUser', function () {
    $certs = Certificate::findByUser(1);
    assert(count($certs) >= 1);
});

test('findByUserCourse', function () {
    $cert = Certificate::findByUserCourse(1, 1);
    assert($cert !== null);
});

echo "\n=== Résultat : {$passed} réussis, {$failed} échecs ===\n";
exit($failed > 0 ? 1 : 0);
