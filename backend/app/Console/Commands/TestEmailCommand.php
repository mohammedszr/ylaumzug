<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EmailNotificationService;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email?} {--status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration and send test email';

    protected EmailNotificationService $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        parent::__construct();
        $this->emailService = $emailService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('status')) {
            $this->showEmailStatus();
            return;
        }

        $email = $this->argument('email') ?? env('BUSINESS_EMAIL', 'info@yla-umzug.de');
        
        $this->info("Testing email configuration...");
        $this->info("Sending test email to: {$email}");
        
        $success = $this->emailService->sendTestEmail($email);
        
        if ($success) {
            $this->info("✅ Test email sent successfully!");
            $this->info("Check the inbox for: {$email}");
        } else {
            $this->error("❌ Test email failed to send!");
            $this->error("Check the Laravel logs for more details.");
        }
    }

    private function showEmailStatus()
    {
        $status = $this->emailService->getEmailStatus();
        
        $this->info("📧 Email Configuration Status");
        $this->line("================================");
        
        $this->line("Mailer: " . ($status['mailer'] ?? 'Not set'));
        $this->line("Host: " . ($status['host'] ?? 'Not set'));
        $this->line("Port: " . ($status['port'] ?? 'Not set'));
        $this->line("Encryption: " . ($status['encryption'] ?? 'None'));
        $this->line("From Address: " . ($status['from_address'] ?? 'Not set'));
        $this->line("From Name: " . ($status['from_name'] ?? 'Not set'));
        $this->line("Business Email: " . ($status['business_email'] ?? 'Not set'));
        
        if ($status['configured']) {
            $this->info("✅ Email appears to be configured");
        } else {
            $this->warn("⚠️  Email configuration incomplete");
        }
        
        $this->line("");
        $this->info("To test email sending, run:");
        $this->line("php artisan email:test your-email@example.com");
    }
}