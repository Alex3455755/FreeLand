<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;

class FreeLandMailService
{
    public function sendVerificationCode(string $toEmail, string $plainCode): void
    {
        $this->sendCodeMail(
            $toEmail,
            $plainCode,
            'FreeLand — код подтверждения email',
            'Подтверждение регистрации',
            'Чтобы завершить регистрацию, введите этот код на сайте:'
        );
    }

    public function sendPasswordResetCode(string $toEmail, string $plainCode): void
    {
        $this->sendCodeMail(
            $toEmail,
            $plainCode,
            'FreeLand — код для входа',
            'Восстановление доступа',
            'Чтобы войти в аккаунт, введите этот код на странице входа:'
        );
    }

    private function sendCodeMail(
        string $toEmail,
        string $plainCode,
        string $subject,
        string $heading,
        string $description
    ): void {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'alexeigyll@gmail.com';
        $mail->Password = 'wjdd rplm hkhi ijhp';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        ];

        $mail->setFrom('alexeigyll@gmail.com', 'freeland');
        $mail->addAddress($toEmail);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $safeCode = htmlspecialchars($plainCode, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $safeHeading = htmlspecialchars($heading, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $safeDescription = htmlspecialchars($description, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="ru">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#0f172a;font-family:Segoe UI,Roboto,Helvetica,Arial,sans-serif;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#0f172a;padding:32px 16px;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:480px;background:linear-gradient(145deg,#1e3a5f 0%,#0c2744 100%);border-radius:16px;border:1px solid rgba(148,197,255,0.25);overflow:hidden;box-shadow:0 12px 40px rgba(0,0,0,0.35);">
          <tr>
            <td style="padding:28px 28px 8px 28px;text-align:center;">
              <div style="font-size:22px;font-weight:700;letter-spacing:0.02em;color:#e8f4ff;">FreeLand</div>
              <div style="margin-top:6px;font-size:13px;color:#94c5ff;opacity:0.9;">{$safeHeading}</div>
            </td>
          </tr>
          <tr>
            <td style="padding:12px 28px 8px 28px;">
              <p style="margin:0;font-size:15px;line-height:1.55;color:#dbeafe;">{$safeDescription}</p>
            </td>
          </tr>
          <tr>
            <td style="padding:8px 28px 24px 28px;text-align:center;">
              <div style="display:inline-block;padding:16px 32px;background:rgba(15,23,42,0.65);border-radius:12px;border:1px solid rgba(148,197,255,0.35);letter-spacing:0.35em;font-size:26px;font-weight:700;color:#f8fafc;font-family:Consolas,Monaco,monospace;">{$safeCode}</div>
            </td>
          </tr>
          <tr>
            <td style="padding:0 28px 28px 28px;">
              <p style="margin:0;font-size:13px;line-height:1.5;color:#93c5fd;opacity:0.85;">Если вы не запрашивали это письмо, просто проигнорируйте его.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        $mail->AltBody = "FreeLand — код: {$plainCode}";

        $mail->Timeout = 10;
        $mail->Debugoutput = function ($str, $level) {
            error_log("SMTP: $str");
        };

        if (!$mail->send()) {
            throw new \Exception($mail->ErrorInfo);
        }
    }
}
