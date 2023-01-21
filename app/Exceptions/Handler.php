<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\{
    HttpException,
    MethodNotAllowedHttpException,
    NotFoundHttpException
};

class Handler extends ExceptionHandler
{
    use ApiResponse;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return $this->notFoundResponse([], 'Page Not Found.');
            }
        });

        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return $this->methodNotAllowedResponse();
            }
        });

        $this->renderable(function (AuthenticationException $exception, $request) {
            if ($request->is('api/*')) {
                return $this->unauthorizedResponse();
            }
        });

        $this->renderable(function (ValidationException $exception, $request) {
            if ($request->is('api/*')) {
                return $this->unprocessableResponse($exception->errors());
            }
        });

        $this->renderable(function (\Illuminate\Http\Exceptions\PostTooLargeException $exception, $request) {
            if ($request->is('api/*')) {

                return $this->unprocessableResponse();
            }
        });
    }
}
