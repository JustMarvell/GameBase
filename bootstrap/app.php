<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Daftarkan middleware dari Spatie dengan nama pendek
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' =>
                \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        // Admin yang sudah login lalu membuka /login diarahkan ke dashboard admin
        $middleware->redirectUsersTo(
            fn() => auth()->user()->hasAnyRole(['admin', 'developer']) ? '/admin/dashboard' : '/'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();