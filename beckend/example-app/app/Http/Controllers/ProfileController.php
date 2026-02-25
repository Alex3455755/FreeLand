<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Получить данные профиля текущего пользователя
     */
    public function index(User $user)
    {
        try {
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }
            
            // Получаем историю платежей с загрузкой связанных пользователей
            $sentPayments = Payment::where('customer_id', $user->id)
                ->with(['freelancer'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            $receivedPayments = Payment::where('freelancer_id', $user->id)
                ->with(['customer'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'user' => $user,
                'sent_payments' => $sentPayments,
                'received_payments' => $receivedPayments
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка получения профиля: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке профиля'
            ], 500);
        }
    }
    
    /**
     * Обновить данные пользователя
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }
            
            $validated = $request->validate([
                'full_name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20',
                'avatar' => 'nullable|string|max:255',
                'password' => 'sometimes|string|min:6|confirmed'
            ]);
            
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }
            
            $user->update($validated);
            
            return response()->json([
                'success' => true,
                'message' => 'Профиль успешно обновлен',
                'user' => $user
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка обновления профиля: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении профиля: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Пополнить баланс
     */
    /**
 * Пополнить баланс
 */
public function deposit(Request $request)
{
    try {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);
        
        $amount = $validated['amount'];
        
        // Обновляем баланс
        $user->balance += $amount;
        $user->save();
        
        // СОЗДАЕМ ЗАПИСЬ О ПОПОЛНЕНИИ
        $paymentData = [
            'customer_id' => $user->id,
            'freelancer_id' => $user->id, // для input/output ставим тот же ID
            'amount' => $amount,
            'commission' => 0,
            'status' => 'paid',
            'type' => 'input'
        ];
        
        $paymentId = \DB::table('payments')->insertGetId([
            'customer_id' => $user->id,
            'freelancer_id' => $user->id,
            'amount' => $amount,
            'commission' => 0,
            'status' => 'paid',
            'type' => 'input',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        Log::info('Пополнение баланса:', [
            'user_id' => $user->id,
            'amount' => $amount,
            'new_balance' => $user->balance
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Баланс успешно пополнен на {$amount} ₽",
            'balance' => $user->balance
        ]);
        
    } catch (\Exception $e) {
        Log::error('Ошибка пополнения баланса: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Ошибка при пополнении баланса'
        ], 500);
    }
}

/**
 * Вывести деньги
 */
public function withdraw(Request $request)
{
    try {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован'
            ], 401);
        }
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);
        
        $amount = $validated['amount'];
        
        if ($user->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно средств на балансе'
            ], 400);
        }
        
        // Обновляем баланс
        $user->balance -= $amount;
        $user->save();
        
        // СОЗДАЕМ ЗАПИСЬ О ВЫВОДЕ
        $paymentData = [
            'customer_id' => $user->id,
            'freelancer_id' => $user->id, // для input/output ставим тот же ID
            'amount' => $amount,
            'commission' => 0,
            'status' => 'paid',
            'type' => 'output'
        ];
        
        $paymentId = \DB::table('payments')->insertGetId([
            'customer_id' => $user->id,
            'freelancer_id' => $user->id,
            'amount' => $amount,
            'commission' => 0,
            'status' => 'paid',
            'type' => 'output',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        Log::info('Вывод средств:', [
            'user_id' => $user->id,
            'amount' => $amount,
            'new_balance' => $user->balance
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Сумма {$amount} ₽ успешно выведена",
            'balance' => $user->balance
        ]);
        
    } catch (\Exception $e) {
        Log::error('Ошибка вывода средств: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Ошибка при выводе средств'
        ], 500);
    }
}
    
    /**
     * Отправить деньги другому пользователю
     */
    public function sendMoney(Request $request, User $user)
    {
        try {
            // Проверяем заголовок Authorization
            $token = $request->header('Authorization');
            
            // Пробуем получить пользователя через Auth
            $sender = $user;
            
            if (!$sender) {
                // Если пользователь не найден через Auth, пробуем через токен вручную
                if ($token && str_starts_with($token, 'Bearer ')) {
                    $tokenValue = substr($token, 7);
                    
                    // Ищем токен в базе
                    $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($tokenValue);
                    if ($personalAccessToken) {
                        $sender = $personalAccessToken->tokenable;
                        Log::info('User found via token: ' . ($sender->id ?? 'null'));
                    }
                }
            }
            
            if (!$sender) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }
            
            // Проверяем, что пришло: receiver_id или receiver_login
            if ($request->has('receiver_id')) {
                $validated = $request->validate([
                    'receiver_id' => 'required|integer|exists:users,id',
                    'amount' => 'required|numeric|min:1'
                ]);
                $receiver = User::find($validated['receiver_id']);
            } elseif ($request->has('receiver_login')) {
                $validated = $request->validate([
                    'receiver_login' => 'required|string|exists:users,login',
                    'amount' => 'required|numeric|min:1'
                ]);
                $receiver = User::where('login', $validated['receiver_login'])->first();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Не указан получатель (receiver_id или receiver_login)'
                ], 400);
            }
            
            $amount = (float) $validated['amount'];
            $commission = round($amount * 0.05, 2);
            $totalDeduction = $amount + $commission;
            
            // Принудительно преобразуем в float для сравнения
            $senderBalance = (float) $sender->balance;
            $totalNeeded = (float) $totalDeduction;
            
            if ($senderBalance < $totalNeeded) {
                return response()->json([
                    'success' => false,
                    'message' => "Недостаточно средств. Баланс: {$senderBalance} ₽, нужно: {$totalNeeded} ₽ (включая комиссию 5%)"
                ], 400);
            }
            
            if ($receiver->id === $sender->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Нельзя отправлять деньги самому себе'
                ], 400);
            }
            
            // СОЗДАЕМ ПЛАТЕЖ
            $paymentData = [
                'customer_id' => $sender->id,
                'freelancer_id' => $receiver->id,
                'amount' => $amount,
                'commission' => $commission,
                'status' => 'paid'
            ];
            
            $paymentId = \DB::table('payments')->insertGetId([
                'customer_id' => $sender->id,
                'freelancer_id' => $receiver->id,
                'amount' => $amount,
                'commission' => $commission,
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $payment = Payment::with(['customer', 'freelancer'])->find($paymentId);
            
            // Обновляем балансы
            $oldBalance = $sender->balance;
            $sender->balance -= $totalDeduction;
            $receiver->balance += $amount;
            
            Log::info('Старый баланс отправителя: ' . $oldBalance);
            Log::info('Новый баланс отправителя: ' . $sender->balance);
            
            $sender->save();
            $receiver->save();
            
            Log::info('=== ОТПРАВКА ДЕНЕГ УСПЕШНО ЗАВЕРШЕНА ===');
            
            return response()->json([
                'success' => true,
                'message' => "Перевод выполнен успешно. Получатель: {$receiver->full_name}, сумма: {$amount} ₽, комиссия: {$commission} ₽",
                'balance' => $sender->balance,
                'payment' => $payment
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка отправки денег: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отправке денег: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function payments()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }
            
            $sentPayments = Payment::where('customer_id', $user->id)
                ->with(['freelancer'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            $receivedPayments = Payment::where('freelancer_id', $user->id)
                ->with(['customer'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'sent' => $sentPayments,
                'received' => $receivedPayments
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка получения платежей: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке платежей'
            ], 500);
        }
    }
}