<?php

use App\Enum\ResponseErrorCode;
use App\Enum\ResponseStatus;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'status' => ResponseStatus::Error->value,
                    'code' => ResponseErrorCode::fromException($e)->value,
                    'message' => $e->getMessage(),
                    'details' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->renderable(function (NotFoundHttpException|ModelNotFoundException $e, $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'status' => ResponseStatus::Error->value,
                    'code' => ResponseErrorCode::NotFound->value,
                    'message' => $e->getMessage(),
                ], 404);
            }
        });

        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })->create();
