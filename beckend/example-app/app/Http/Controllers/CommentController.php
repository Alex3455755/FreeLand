<?php

namespace App\Http\Controllers;

use App\Models\Comment;
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $com = new Comment([
            "text" => $request['text'],
            "rating" => $request['rating'],
            "project_id" => $request['project_id'],
            "user_id" => $request['user_id'],
        ]);
        $com->save();

         return response()->json([
            'success' => true,
            'message' => 'Отзыв успешно создан'
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Отзыв успешно обновлен'
        ]);
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
