<?php

class HomeController
{
    public function index(): void
    {
        $courses = Course::allPublished();
        $categories = Category::all();

        $stats = [
            'courses'     => Course::countPublished(),
            'students'    => User::countByRole('user'),
            'categories'  => count($categories),
        ];

        Router::render('home/index', [
            'courses'    => array_slice($courses, 0, 6),
            'categories' => $categories,
            'stats'      => $stats,
        ], 'main');
    }
}
