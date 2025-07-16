<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\EmailNotificationService;

class AdminController extends Controller
{
    protected EmailNotificationService $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Get email configuration status
     */
    public function getEmailStatus(): JsonResponse
    {
        try {
            $status = $this->emailService->getEmailStatus();
            
            return response()->json([
                'success' => true,
                'email_status' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Laden der E-Mail-Konfiguration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test email to verify configuration
     */
    public function sendTestEmail(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'email' => 'nullable|email'
            ]);

            $testEmail = $request->input('email');
            $success = $this->emailService->sendTestEmail($testEmail);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test-E-Mail wurde erfolgreich gesendet!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Test-E-Mail konnte nicht gesendet werden. Bitte prüfen Sie die E-Mail-Konfiguration.'
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Senden der Test-E-Mail',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}