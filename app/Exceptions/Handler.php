<?php

namespace App\Exceptions;

use App\Mail\Reminder;
use App\User;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;

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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        // 发送通知邮件
        if ($exception) {
            $url = \Request::fullUrl();
            $user = \Auth::user();

            $mailTo = User::whereIn('username', explode(',', 'sy0011'))->get();

            \Mail::to($mailTo)->send(new Reminder($exception, $url, $user));
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    protected function mailReport($e): bool
    {
        $es = [

            HttpResponseException::class,
            \ErrorException::class,
            \PDOException::class,

        ];

        foreach ($es as $type) {
            if ($e instanceof $type) {
                return true;
            }
        }

        return false;
    }
}
