<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categries = Category::latest()->get();
        return response()->json($categries);
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
        $cat = new Category([
            "name" => $request['name'],
            "description" => $request['description'],
        ]);
        $cat->save();

         return response()->json([
            'success' => true,
            'message' => 'Категория успешно создана'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Categries $categries)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categries $categries)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        Category::find($request->id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'категория успешно обновлена'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $categries)
    {
         $categries->delete();
        return response()->json([
            'success' => true,
            'message' => 'категория удалена'
        ]);
    }
}
