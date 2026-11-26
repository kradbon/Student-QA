<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\QuestionController;
use App\Controllers\ReferenceController;
use App\Core\Router;

require dirname(__DIR__) . '/vendor/autoload.php';

$router = new Router();
$router->get('/', [HomeController::class, 'index']);
$router->get('/questions', [QuestionController::class, 'index']);
$router->get('/questions/{id}', [QuestionController::class, 'show']);
$router->get('/reference', [ReferenceController::class, 'index']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
