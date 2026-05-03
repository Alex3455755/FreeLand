<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PhpMailerService
{
    public function send(string $toEmail, string $toName, string $subject, string $htmlBody): void
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST', 'smtp.gmail.com');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->Port = (int) env('MAIL_PORT', 465);

            $encryption = env('MAIL_ENCRYPTION', 'ssl');
            if ($encryption === 'tls') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            }

            $mail->CharSet = 'UTF-8';
            $mail->setFrom(
                env('MAIL_FROM_ADDRESS', env('MAIL_USERNAME')),
                env('MAIL_FROM_NAME', 'FreeLand')
            );

            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $htmlBody;
            $mail->AltBody = strip_tags(str_replace('<br>', PHP_EOL, $htmlBody));

            $mail->send();
        } catch (Exception $exception) {
            throw new \RuntimeException('Не удалось отправить письмо: ' . $exception->getMessage());
        }
    }
}
