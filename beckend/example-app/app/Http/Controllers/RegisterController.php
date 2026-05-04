<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;

class RegisterController extends Controller
{
    // Временно отключено: отправка email при регистрации
    // private PhpMailerService $mailer;
    //
    // public function __construct(PhpMailerService $mailer)
    // {
    //     $this->mailer = $mailer;
    // }

    public function registred()
    {

        $mail = new PHPMailer(true);
        request()->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'login' => ['required', 'email', 'max:255', 'unique:users,login'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
            'role' => ['required', 'string'],
        ]);

        // Временно отключено: email-верификация при регистрации
        $verificationCode = (string) random_int(100000, 999999);

        $user = User::create([
            'full_name' => request('full_name'),
            'phone' => request('phone'),
            'login' => request('login'),
            'avatar' => request('avatar'),
            'password' => Hash::make(request('password')),
            'email_verification_code' => null,
            'email_verified_at' => now(),
            'role' => request('role') == 'фрилансер' ? 'freelancer' : 'customer',
            'balance' => 0.00,
            'rating' => 0.00,
        ]);

        // Временно отключено: отправка кода на email
        // $name = $user->full_name ?: 'Пользователь';
        // $this->mailer->send(
        //     $user->login,
        //     $name,
        //     'Код подтверждения FreeLand',
        //     "Здравствуйте, {$name}!<br><br>Ваш код подтверждения: <b>{$verificationCode}</b><br><br>Если вы не регистрировались, просто проигнорируйте это письмо."
        // );

         try {
            $mail->SMTPDebug = 2;

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
            $mail->addAddress($user->login);

            $mail->isHTML(true);
            $mail->Subject = 'Код подтверждения';
            $mail->Body = "Код для активации - {$verificationCode}";

            $mail->SMTPDebug = 2;
            $mail->Timeout = 10;
            $mail->Debugoutput = function($str, $level) {
                error_log("SMTP: $str");
            };

            if (!$mail->send()) {
                throw new \Exception($mail->ErrorInfo);
            }

            return response()->json([
                'success' => true,
                'message' => 'Письмо отправлено'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'requires_verification' => false,
            'message' => 'Регистрация успешно завершена',
            'login' => $user->login,
        ]);
    }

    public function verifyEmailCode(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Подтверждение email временно отключено',
        ]);
    }

    public function resendEmailCode(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Отправка кода временно отключена',
        ]);
    }
}
