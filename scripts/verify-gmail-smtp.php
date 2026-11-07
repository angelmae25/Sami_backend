<?php
require dirname(__DIR__) . '/vendor/autoload.php';
$app = require dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $mailer = config('mail.default');
    $transport = Illuminate\Support\Facades\Mail::mailer($mailer)->getSymfonyTransport();
    $transport->start();
    echo "Google SMTP authentication succeeded. No email was sent." . PHP_EOL;
} catch (Throwable $exception) {
    fwrite(STDERR, "Google rejected the App Password or SMTP access. Create a fresh App Password, then run configure-gmail-smtp.ps1 again. No email was sent." . PHP_EOL);
    exit(1);
}
