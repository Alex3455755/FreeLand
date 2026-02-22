<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $messages = Message::latest()->get();
        return response()->json($messages);
    }

    public function messagesFromOneProject(Request $request){

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
        $message = new Message([
            "author_id" => $request['author_id'],
            "project_id" => $request['project_id'],
            "text" => $request['text'],
            "time" => now()
        ]);
        $message->save();

         return response()->json([
            'success' => true,
            'message' => 'сообщение успешно создано'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
         Message::find($request->id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'сообщение успешно обновлено'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        $message->delete();
        return response()->json([
            'success' => true,
            'message' => 'сообщение удалено'
        ]);
    }
}
