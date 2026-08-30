<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
            'delivery' => \App\Http\Middleware\DeliveryMiddleware::class,
            'pickup' => \App\Http\Middleware\PickupMiddleware::class,
            'staff.status' => \App\Http\Middleware\CheckStaffStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, Request $request) {
            Log::error('Application Error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => Auth::id() ?? 'guest',
            ]);

            // 404
            if ($e instanceof NotFoundHttpException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Resource not found.', 'success' => false], 404);
                }
                return response()->view('errors.404', [], 404);
            }

            // 403
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 403) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['message' => $e->getMessage() ?: 'Access denied.', 'success' => false], 403);
                }
                return response()->view('errors.403', ['exception' => $e], 403);
            }

            // 419
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Session expired. Please refresh and try again.', 'success' => false], 419);
                }
                return response()->view('errors.419', [], 419);
            }

            // 500
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException && $e->getStatusCode() === 500) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Server error. Please try again later.', 'success' => false], 500);
                }
                return response()->view('errors.500', [], 500);
            }

            // Validation
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors(), 'success' => false], 422);
                }
                return null;
            }

            // Authentication
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Unauthenticated. Please login.', 'success' => false], 401);
                }
                return response()->view('errors.401', [], 401);
            }

            // Authorization
            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => $e->getMessage() ?: 'Unauthorized action.', 'success' => false], 403);
                }
                return response()->view('errors.403', ['exception' => $e], 403);
            }

            return null;
        });

        $exceptions->report(function (Throwable $e) {
            if (app()->environment('production')) {
                // Add production error reporting
            }
        });

        $exceptions->dontReport([
            \Illuminate\Auth\AuthenticationException::class,
            \Illuminate\Auth\Access\AuthorizationException::class,
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
            \Illuminate\Database\Eloquent\ModelNotFoundException::class,
            \Illuminate\Validation\ValidationException::class,
        ]);

        $exceptions->dontFlash([
            'password',
            'password_confirmation',
        ]);
    })
    ->create();
