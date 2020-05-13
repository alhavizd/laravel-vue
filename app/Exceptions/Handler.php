<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if (method_exists($exception,'responseJson')) {
            return $exception->responseJson();
        }

        if ($request->wantsJson()) {
            if (!empty($exception) ) {
                $response = [];
                $status = 500;
                $response['error'] = [
                    'message' => 'Error Found',
                    'status_code' => 500,
                    'error' => 'Something Went Wrong'
                ];

                if (config('app.debug')) {
                    $response['error']['message'] = $exception->getMessage();
                    $response['error']['trace'] = $exception->getTrace();
                    $response['error']['code'] = $exception->getCode();
                }

                if ($exception instanceof \Illuminate\Validation\ValidationException) {
                  return $this->convertValidationExceptionToResponse($exception, $request);
                } else if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
                  throw new \App\Exceptions\AuthenticationException();
                } else if ($exception instanceof \PDOException) {
                  $status = 500;
                  $response['error'] = 'Can not finish your query request!';
                } else if ($this->isHttpException($exception)) {
                  $status = $exception->getStatusCode();
                  $response['error'] = 'Request error!';
                } else {
                  $status = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 400;
                }
                return response()->json($response,$status);
            }
        }

        return parent::render($request, $exception);
    }
}
