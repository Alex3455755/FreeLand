<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPMailer\PHPMailer\PHPMailer;

class RegisterController extends Controller
{
    public function registred()
    {
        request()->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'login' => ['required', 'email', 'max:255', 'unique:users,login'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'role' => ['required', 'string'],
        ]);

        $verificationCode = (string) random_int(100000, 999999);

        $user = User::create([
            'full_name' => request('full_name'),
            'phone' => request('phone'),
            'login' => request('login'),
            'avatar' => request('avatar'),
            'password' => Hash::make(request('password')),
            'email_verification_code' => $verificationCode,
            'email_verified_at' => null,
            'role' => request('role') == 'фрилансер' ? 'freelancer' : 'customer',
            'balance' => 0.00,
            'rating' => 0.00,
        ]);

        try {
            $this->sendVerificationCodeMail($user->login, $verificationCode);
        } catch (\Exception $e) {
            $user->delete();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'requires_verification' => true,
            'message' => 'На email отправлен код подтверждения',
            'login' => $user->login,
        ]);
    }

    public function verifyEmailCode(Request $request)
    {
        $request->validate([
            'login' => ['required', 'email'],
            'code' => ['required', 'string'],
        ]);

        $code = preg_replace('/\D/', '', $request->code);
        if (strlen($code) !== 6) {
            return response()->json([
                'success' => false,
                'message' => 'Введите 6-значный код',
            ], 422);
        }

        $user = User::where('login', $request->login)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => true,
                'message' => 'Email уже подтверждён',
            ]);
        }

        if (
            !$user->email_verification_code
            || !hash_equals((string) $user->email_verification_code, $code)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Неверный код',
            ], 422);
        }

        $user->forceFill([
            'email_verified_at' => now(),
            'email_verification_code' => null,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Email подтверждён, можно войти',
        ]);
    }

    public function resendEmailCode(Request $request)
    {
        $request->validate([
            'login' => ['required', 'email'],
        ]);

        $user = User::where('login', $request->login)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Этот email уже подтверждён',
            ], 422);
        }

        $verificationCode = (string) random_int(100000, 999999);
        $user->update([
            'email_verification_code' => $verificationCode,
        ]);

        try {
            $this->sendVerificationCodeMail($user->login, $verificationCode);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Новый код отправлен на email',
        ]);
    }

    private function sendVerificationCodeMail(string $toEmail, string $plainCode): void
    {
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
        $mail->Subject = 'FreeLand — код подтверждения email';
        $safeCode = htmlspecialchars($plainCode, ENT_QUOTES | ENT_HTML5, 'UTF-8');
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
              <div style="margin-top:6px;font-size:13px;color:#94c5ff;opacity:0.9;">Подтверждение регистрации</div>
            </td>
          </tr>
          <tr>
            <td style="padding:12px 28px 8px 28px;">
              <p style="margin:0;font-size:15px;line-height:1.55;color:#dbeafe;">Здравствуйте! Чтобы завершить регистрацию, введите этот код на сайте:</p>
            </td>
          </tr>
          <tr>
            <td style="padding:8px 28px 24px 28px;text-align:center;">
              <div style="display:inline-block;padding:16px 32px;background:rgba(15,23,42,0.65);border-radius:12px;border:1px solid rgba(148,197,255,0.35);letter-spacing:0.35em;font-size:26px;font-weight:700;color:#f8fafc;font-family:Consolas,Monaco,monospace;">{$safeCode}</div>
            </td>
          </tr>
          <tr>
            <td style="padding:0 28px 28px 28px;">
              <p style="margin:0;font-size:13px;line-height:1.5;color:#93c5fd;opacity:0.85;">Код действует ограниченное время. Если вы не регистрировались на FreeLand, просто проигнорируйте это письмо.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        $mail->AltBody = "FreeLand — код подтверждения: {$plainCode}";

        $mail->Timeout = 10;
        $mail->Debugoutput = function ($str, $level) {
            error_log("SMTP: $str");
        };

        if (!$mail->send()) {
            throw new \Exception($mail->ErrorInfo);
        }
    }
}
