<?php

namespace App\Exceptions;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
class Handler extends ExceptionHandler
{
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
     */
    public function register()
{
    $this->reportable(function (Throwable $e) {
        // Log security exceptions
        if ($e instanceof \Illuminate\Auth\AuthenticationException ||
            $e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            \Log::channel('security')->warning('Security exception', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
            ]);
        }
    });
    
    // 🔒 SECURITY: Sanitize error messages in production
    $this->renderable(function (Throwable $e, $request) {
        if (app()->environment('production') && !$request->expectsJson()) {
            // Don't leak stack trace
            return response()->view('errors.500', [], 500);
        }
    });
}
}