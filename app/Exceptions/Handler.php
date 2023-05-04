<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response as HttpStatusCode;
use App\Core\Exceptions\BaseException;
use Throwable;

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
     * Render an exception into an HTTP response.
     *
     * @param Request   $request   Request
     * @param Throwable $exception Exception
     *
     * @return HttpStatusCode
     * @throws Throwable
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function render($request, Throwable $exception): HttpStatusCode
    {
        dd($exception->getMessage());
        $statusCode = HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR;
        $errors = [];
        $messageCode = 'errors.unhandled_exception';
        $message = __('messages.unhandled_exception');

        switch (true) {
            case $exception instanceof ValidationException:
                $statusCode = HttpStatusCode::HTTP_UNPROCESSABLE_ENTITY;
                $messageCode = 'validation.fail';

                foreach ($exception->errors() as $k => $error) {
                    $errorMessage = $error;

                    if (is_array($error)) {
                        $errorMessage = $error[0];
                    }

                    if (str_starts_with($errorMessage, 'validation.')) {
                        $errorMessage = substr($errorMessage, 11);
                        $errorMessage = __('messages.validation.' . $k . '.' . $errorMessage);
                    }

                    $errors[$k] = $errorMessage;
                }

                $message = __('messages.validation_error');
                break;

            case $exception instanceof NotFoundHttpException:
            case $exception instanceof MethodNotAllowedHttpException:
            case $exception instanceof AccessDeniedHttpException:
            case $exception instanceof AuthorizationException:
                $statusCode = HttpStatusCode::HTTP_NOT_FOUND;
                $messageCode = 'route.not_found';
                $message = __('messages.route_not_found');
                break;

            case $exception instanceof ModelNotFoundException:
                $statusCode = HttpStatusCode::HTTP_NOT_FOUND;
                $messageCode = 'record.not_found';
                $message = __('messages.record_not_found');
                break;

//            case $exception instanceof JWTException:
//            case $exception instanceof TokenInvalidException:
//            case $exception instanceof TokenBlacklistedException:
            case $exception instanceof AuthenticationException:
                $statusCode = HttpStatusCode::HTTP_UNAUTHORIZED;
                $messageCode = 'session.not_found';
                $message = __('messages.session_not_found');
                break;

            case $exception instanceof ThrottleRequestsException:
                $statusCode = HttpStatusCode::HTTP_TOO_MANY_REQUESTS;
                $messageCode = 'request.max_attempts';
                $message = __('messages.throttle_request');
                break;

            case $exception instanceof BaseException:
            case $exception instanceof BusinessException:
                $statusCode = $exception->getCode();
                $messageCode = method_exists($exception, 'getMessageCode') ? $exception->getMessageCode() : 'errors.unhandled_exception';
                $message = $exception->getMessage();
                break;

            case $exception instanceof TokenMismatchException:
                Log::debug("Ip address 1 : " . getIp());
                Log::debug("Ip address 2 : " . request()->ip());
                Log::debug("Route : " . request()->fullUrl());
                break;

            default:
                break;
        }

        $jsonResponse = [
            'code' => $messageCode,
            'errors' => $errors,
            'message' => $message,
        ];

        if (config('app.debug')) {
            $jsonResponse['debug'] = $this->prepareJsonResponse($request, $exception);
        }

        return request()->is('api/*') || request()->is('ajax/*')
            ? response()->json($jsonResponse, $statusCode)
            : parent::render($request, $exception);
    }
}
