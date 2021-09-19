<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
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
    }

    public function render($request, Throwable $exception)
    {
        if($exception instanceof ValidationException) {
            return response()->json([
                'Status' => false,
                'Message' => 'ValidationError'
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        } elseif($exception instanceof AuthorizationException) {
            $uri = substr(request()->getPathInfo(),1);

            return response()->json([
                'Status' => false,
                'Message' => $uri == 'user/add' ? 'RegistrationUnavailable' : Response::$statusTexts[Response::HTTP_NOT_ACCEPTABLE]
            ],Response::HTTP_NOT_ACCEPTABLE);
        } elseif($exception instanceof \ErrorException) {
            return response()->json([
                'Status' => false,
                'Message' => $exception->getMessage()
            ],Response::HTTP_NOT_ACCEPTABLE);
        }
    }
}
