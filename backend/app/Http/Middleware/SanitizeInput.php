<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        
        $sanitized = $this->sanitizeArray($input);
        
        $request->replace($sanitized);

        return $next($request);
    }

    /**
     * Recursively sanitize array data
     */
    protected function sanitizeArray(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            $sanitizedKey = $this->sanitizeString($key);
            
            if (is_array($value)) {
                $sanitized[$sanitizedKey] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $sanitized[$sanitizedKey] = $this->sanitizeString($value);
            } else {
                $sanitized[$sanitizedKey] = $value;
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize string input
     */
    protected function sanitizeString(string $input): string
    {
        // Remove null bytes
        $input = str_replace("\0", '', $input);
        
        // Remove or encode potentially dangerous characters
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        
        // Remove script tags and other dangerous HTML
        $input = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $input);
        $input = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/mi', '', $input);
        $input = preg_replace('/javascript:/i', '', $input);
        $input = preg_replace('/on\w+\s*=/i', '', $input);
        
        // Trim whitespace
        $input = trim($input);
        
        return $input;
    }

    /**
     * Check if input contains potentially malicious content
     */
    protected function containsMaliciousContent(string $input): bool
    {
        $maliciousPatterns = [
            '/(<script[^>]*>.*?<\/script>)/is',
            '/(<iframe[^>]*>.*?<\/iframe>)/is',
            '/(javascript:)/i',
            '/(vbscript:)/i',
            '/(onload|onerror|onclick|onmouseover)\s*=/i',
            '/(<object[^>]*>.*?<\/object>)/is',
            '/(<embed[^>]*>.*?<\/embed>)/is',
            '/(<form[^>]*>.*?<\/form>)/is',
        ];

        foreach ($maliciousPatterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }
}