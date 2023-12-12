<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;


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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException) {
            // Sample message: "No query results for model [App\Models\File] 1, 2, 3, 4"
            $contentAfterLastBracket = substr($exception->getMessage(), strrpos($exception->getMessage(), "]") + 1);

            $message = "Entry with id(s) $contentAfterLastBracket not found.";
            return response()->json([
                'message' => $message
            ], 404);
        }

        return parent::render($request, $exception);
    }
}
