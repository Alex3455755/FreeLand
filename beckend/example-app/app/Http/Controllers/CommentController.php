<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $comments = Comment::latest()->get();
        return response()->json($comments);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function userComments(User $user){
        $comments = $user->comments()->latest()->get();
    
    return response()->json($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $author = User::find($request['author_id']);
        $reviewed = User::find($request['user_id']);

        if (!$author || !$reviewed) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не найден',
            ], 422);
        }

        $authorRole = $this->normalizeRole($author->role);
        $targetRole = $this->normalizeRole($reviewed->role);

        $pairAllowed = ($authorRole === 'freelancer' && $targetRole === 'customer')
            || ($authorRole === 'customer' && $targetRole === 'freelancer');

        if (!$pairAllowed) {
            return response()->json([
                'success' => false,
                'message' => 'Фрилансеры могут оставлять отзывы только заказчикам, заказчики — только фрилансерам.',
            ], 403);
        }

        $com = new Comment([
            "text" => $request['text'],
            "rating" => $request['rating'],
            "author_id" => $request['author_id'],
            "user_id" => $request['user_id'],
        ]);
        $com->save();


        $this->updateUserRating($request->user_id);

         return response()->json([
            'success' => true,
            'message' => 'Отзыв успешно создан',
            'comment_id' => $com->id,
        ]);
    }

    /**
     * @return string|null 'freelancer'|'customer'|'admin'|null
     */
    private function normalizeRole($role): ?string
    {
        if ($role === null || $role === '') {
            return null;
        }

        $r = mb_strtolower(trim((string) $role));

        if (in_array($r, ['freelancer', 'фрилансер', '2'], true)) {
            return 'freelancer';
        }
        if (in_array($r, ['customer', 'заказчик', '1'], true)) {
            return 'customer';
        }
        if (in_array($r, ['admin', 'администратор', '3'], true)) {
            return 'admin';
        }

        return null;
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        Comment::find($request->id)->update($request->all());

        $this->updateUserRating($request->user_id);

        return response()->json([
            'success' => true,
            'message' => 'Отзыв успешно обновлен'
        ]);
    }
    public function updateUserRating($userId)
    {
        // Получаем все отзывы для пользователя
        $comments = Comment::where('user_id', $userId)->get();
        
        if ($comments->isEmpty()) {
            // Если отзывов нет, устанавливаем рейтинг в 0
            User::where('id', $userId)->update(['rating' => 0]);
            return;
        }
        
        // Вычисляем средний рейтинг
        $averageRating = $comments->avg('rating');
        
        // Округляем до 1 знака после запятой
        $averageRating = round($averageRating, 1);
        
        // Обновляем рейтинг пользователя
        User::where('id', $userId)->update(['rating' => $averageRating]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json([
            'success' => true,
            'message' => 'Отзыв удален'
        ]);
    }
}
