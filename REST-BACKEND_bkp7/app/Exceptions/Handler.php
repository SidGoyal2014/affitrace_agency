<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {


        /*
        $this->reportable(function (Throwable $e) {
            //
        });
        */

        $this->renderable(function (Throwable $e) {

            /*
            return response()->json(['error' => $e->getMessage()], 400);
            //return response()->view('errors', [], 500);

            return redirect('dashboard');//->with('error', $e->getMessage());
            */

        });



    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        return $request->expectsJson() || $request->is('api/*')
            ? response()->json(['message' => $exception->getMessage()], 401)
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }
}


