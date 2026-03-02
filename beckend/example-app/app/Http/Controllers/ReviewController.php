<?php

namespace App\Http\Controllers;

use App\Models\Review; // Исправлено: Rewiew -> Review
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller // Исправлено: RewiewController -> ReviewController
{
    /**
     * Display a listing of the reviews.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = Review::latest()->get();
        return response()->json($payments);
    }

    /**
     * Store a newly created review in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cat = new Review([
            "email" => $request['email'],
            "name" => $request['name'],
            "text" => $request['text'],
        ]);
        $cat->save();

         return response()->json([
            'success' => true,
            'message' => 'отзыв успешно создан'
        ]);
    }

    /**
     * Display the specified review.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $review = Review::findOrFail($id); // Исправлено: Rewiew -> Review

            // Проверяем, одобрен ли отзыв (если не админ)
            if ($review->status !== 'approved' && !auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Отзыв не найден или еще не опубликован'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $review
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Отзыв не найден'
            ], 404);
        }
    }

    /**
     * Update the specified review (for admin).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            // Проверка прав администратора
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            $review = Review::findOrFail($id); // Исправлено: Rewiew -> Review

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:100',
                'email' => 'sometimes|email|max:100',
                'text' => 'sometimes|string|min:10|max:1000',
                'status' => 'sometimes|in:pending,approved,rejected'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Обновляем только переданные поля
            $review->update($request->only(['name', 'email', 'text', 'status']));

            return response()->json([
                'success' => true,
                'message' => 'Отзыв успешно обновлен',
                'data' => $review
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении отзыва'
            ], 500);
        }
    }

    /**
     * Remove the specified review (for admin).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            // Проверка прав администратора
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            $review = Review::findOrFail($id); // Исправлено: Rewiew -> Review
            $review->delete();

            return response()->json([
                'success' => true,
                'message' => 'Отзыв успешно удален'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении отзыва'
            ], 500);
        }
    }

    /**
     * Get pending reviews (for admin).
     *
     * @return \Illuminate\Http\Response
     */
    public function getPending()
    {
        try {
            // Проверка прав администратора
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            $reviews = Review::where('status', 'pending') // Исправлено: Rewiew -> Review
                            ->orderBy('created_at', 'desc')
                            ->get();

            return response()->json([
                'success' => true,
                'data' => $reviews
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке отзывов'
            ], 500);
        }
    }

    /**
     * Approve a review (for admin).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id)
    {
        try {
            // Проверка прав администратора
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            $review = Review::findOrFail($id); // Исправлено: Rewiew -> Review
            $review->status = 'approved';
            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Отзыв одобрен и опубликован',
                'data' => $review
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при одобрении отзыва'
            ], 500);
        }
    }

    /**
     * Reject a review (for admin).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reject($id)
    {
        try {
            // Проверка прав администратора
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            $review = Review::findOrFail($id); // Исправлено: Rewiew -> Review
            $review->status = 'rejected';
            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Отзыв отклонен',
                'data' => $review
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при отклонении отзыва'
            ], 500);
        }
    }

    /**
     * Get reviews statistics (for admin).
     *
     * @return \Illuminate\Http\Response
     */
    public function getStats()
    {
        try {
            // Проверка прав администратора
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            $stats = [
                'total' => Review::count(), // Исправлено: Rewiew -> Review
                'pending' => Review::where('status', 'pending')->count(),
                'approved' => Review::where('status', 'approved')->count(),
                'rejected' => Review::where('status', 'rejected')->count(),
                'today' => Review::whereDate('created_at', today())->count(),
                'week' => Review::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'month' => Review::whereMonth('created_at', now()->month)->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении статистики'
            ], 500);
        }
    }

    /**
     * Get latest approved reviews (for widget).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getLatest(Request $request)
    {
        try {
            $limit = $request->get('limit', 5);
            
            $reviews = Review::where('status', 'approved') // Исправлено: Rewiew -> Review
                            ->orderBy('created_at', 'desc')
                            ->limit($limit)
                            ->get();

            return response()->json([
                'success' => true,
                'data' => $reviews
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке отзывов'
            ], 500);
        }
    }

    /**
     * Search reviews (for admin).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        try {
            // Проверка прав администратора
            if (!auth()->check() || !auth()->user()->isAdmin()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен'
                ], 403);
            }

            $query = Review::query(); // Исправлено: Rewiew -> Review

            if ($request->has('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('text', 'LIKE', "%{$searchTerm}%");
                });
            }

            if ($request->has('status') && $request->status !== 'all') {
                $query->where('status', $request->status);
            }

            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $reviews = $query->orderBy('created_at', 'desc')
                            ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $reviews
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при поиске отзывов'
            ], 500);
        }
    }
}