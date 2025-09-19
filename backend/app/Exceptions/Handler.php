<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Handle API requests with JSON responses
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions with standardized German error responses
     */
    private function handleApiException(Request $request, Throwable $exception): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->getGermanErrorMessage($exception),
            'error_code' => $this->getErrorCode($exception),
        ];

        // Add validation errors if applicable
        if ($exception instanceof ValidationException) {
            $response['errors'] = $this->translateValidationErrors($exception->errors());
        }

        // Add debug information in development
        if (config('app.debug')) {
            $response['debug_info'] = [
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        $statusCode = $this->getStatusCode($exception);

        // Log the error for monitoring
        if ($statusCode >= 500) {
            \Log::error('API Error', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Get German error message for exception
     */
    private function getGermanErrorMessage(Throwable $exception): string
    {
        return match (get_class($exception)) {
            ValidationException::class => 'Die übermittelten Daten sind ungültig.',
            AuthenticationException::class => 'Authentifizierung erforderlich.',
            AuthorizationException::class => 'Keine Berechtigung für diese Aktion.',
            ModelNotFoundException::class => 'Der angeforderte Datensatz wurde nicht gefunden.',
            NotFoundHttpException::class => 'Die angeforderte Ressource wurde nicht gefunden.',
            MethodNotAllowedHttpException::class => 'Diese HTTP-Methode ist nicht erlaubt.',
            TooManyRequestsHttpException::class => 'Zu viele Anfragen. Bitte versuchen Sie es später erneut.',
            default => $this->getGenericErrorMessage($exception),
        };
    }

    /**
     * Get generic error message based on HTTP status code
     */
    private function getGenericErrorMessage(Throwable $exception): string
    {
        $statusCode = $this->getStatusCode($exception);

        return match (true) {
            $statusCode >= 500 => 'Ein interner Serverfehler ist aufgetreten. Bitte versuchen Sie es später erneut.',
            $statusCode >= 400 => 'Die Anfrage konnte nicht verarbeitet werden.',
            default => 'Ein unerwarteter Fehler ist aufgetreten.',
        };
    }

    /**
     * Get error code for exception
     */
    private function getErrorCode(Throwable $exception): string
    {
        return match (get_class($exception)) {
            ValidationException::class => 'VALIDATION_FAILED',
            AuthenticationException::class => 'AUTHENTICATION_REQUIRED',
            AuthorizationException::class => 'AUTHORIZATION_FAILED',
            ModelNotFoundException::class => 'RESOURCE_NOT_FOUND',
            NotFoundHttpException::class => 'ENDPOINT_NOT_FOUND',
            MethodNotAllowedHttpException::class => 'METHOD_NOT_ALLOWED',
            TooManyRequestsHttpException::class => 'RATE_LIMIT_EXCEEDED',
            default => 'INTERNAL_ERROR',
        };
    }

    /**
     * Get HTTP status code for exception
     */
    private function getStatusCode(Throwable $exception): int
    {
        if (method_exists($exception, 'getStatusCode')) {
            return $exception->getStatusCode();
        }

        return match (get_class($exception)) {
            ValidationException::class => 422,
            AuthenticationException::class => 401,
            AuthorizationException::class => 403,
            ModelNotFoundException::class => 404,
            NotFoundHttpException::class => 404,
            MethodNotAllowedHttpException::class => 405,
            TooManyRequestsHttpException::class => 429,
            default => 500,
        };
    }

    /**
     * Translate validation errors to German
     */
    private function translateValidationErrors(array $errors): array
    {
        $translations = [
            'required' => 'ist erforderlich',
            'email' => 'muss eine gültige E-Mail-Adresse sein',
            'string' => 'muss ein Text sein',
            'numeric' => 'muss eine Zahl sein',
            'integer' => 'muss eine Ganzzahl sein',
            'min' => 'muss mindestens :min Zeichen haben',
            'max' => 'darf höchstens :max Zeichen haben',
            'between' => 'muss zwischen :min und :max liegen',
            'in' => 'ist ungültig',
            'unique' => 'ist bereits vergeben',
            'confirmed' => 'stimmt nicht mit der Bestätigung überein',
            'date' => 'muss ein gültiges Datum sein',
            'after' => 'muss nach :date liegen',
            'before' => 'muss vor :date liegen',
            'regex' => 'hat ein ungültiges Format',
            'json' => 'muss gültiges JSON sein',
        ];

        $translatedErrors = [];

        foreach ($errors as $field => $messages) {
            $translatedMessages = [];
            
            foreach ($messages as $message) {
                // Try to translate common validation messages
                $translatedMessage = $message;
                
                foreach ($translations as $key => $translation) {
                    if (str_contains($message, $key)) {
                        $translatedMessage = str_replace($key, $translation, $message);
                        break;
                    }
                }
                
                $translatedMessages[] = $translatedMessage;
            }
            
            $translatedErrors[$field] = $translatedMessages;
        }

        return $translatedErrors;
    }

    /**
     * Handle unauthenticated users
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Authentifizierung erforderlich.',
                'error_code' => 'AUTHENTICATION_REQUIRED',
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}