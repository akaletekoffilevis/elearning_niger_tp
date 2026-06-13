<?php

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
}

function require_csrf(): void
{
    $token = $_POST['_csrf_token'] ?? '';
    if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        die('Session expirée. Veuillez réessayer.');
    }
}

function time_ago(string $datetime): string
{
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) return 'À l\'instant';
    if ($diff < 3600) return floor($diff / 60) . ' min';
    if ($diff < 86400) return floor($diff / 3600) . 'h';
    if ($diff < 2592000) return floor($diff / 86400) . ' jours';
    return date('d/m/Y', $timestamp);
}

function progress_percent(int $completed, int $total): int
{
    if ($total === 0) return 0;
    return (int) round(($completed / $total) * 100);
}

function paginate(int $total, int $page = 1, int $perPage = 20): array
{
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    return [
        'page' => $page,
        'perPage' => $perPage,
        'offset' => $offset,
        'totalPages' => $totalPages,
        'total' => $total,
        'hasPrev' => $page > 1,
        'hasNext' => $page < $totalPages,
        'prevPage' => $page - 1,
        'nextPage' => $page + 1,
    ];
}
