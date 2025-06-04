<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'validate.outlet.access' => \App\Http\Middleware\ValidateOutletAccess::class,
        ]);
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Item Not Found'], 404);
            }
        });

        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
        });

        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unprocessable Entity',
                    'errors' => method_exists($e, 'errors') ? $e->errors() : [],
                ], 422);
            }
        });

        $exceptions->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'The requested link does not exist'], 404);
            }
        });

        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated: Bearer token missing or invalid',
                ], 401);
            }
        });

        $exceptions->renderable(function (TokenExpiredException $e, $request) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token has expired',
            ], 401);
        });

        $exceptions->renderable(function (TokenInvalidException $e, $request) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is invalid',
            ], 401);
        });

        $exceptions->renderable(function (JWTException $e, $request) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is missing',
            ], 401);
        });

        // role middleware
        $exceptions->renderable(function (UnauthorizedException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Role Denied',
                ], 403);
            }
        });

        $exceptions->renderable(function (\Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Internal Server Error',
                    'error' => $e->getMessage(),
                ], 500);
            }
        });



    })
    ->create();
