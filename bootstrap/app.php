<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// سجّل ميدلوير الدور إن كان عندك
use App\Http\Middleware\EnsureRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',   // <-- مهم جدًا
        commands: __DIR__.'/../routes/console.php',
         channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // alias للدور (لو الملف موجود)
        if (class_exists(EnsureRole::class)) {
            $middleware->alias([
                'role' => EnsureRole::class,
            ]);
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
