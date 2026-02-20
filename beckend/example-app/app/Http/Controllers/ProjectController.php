<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::latest()->get();
        return response()->json($projects);
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
        $project = new Project([
            "title" => $request['title'],
            "description" => $request['description'],
            "budget" => $request['budget'],
            "deadline" => $request['deadline'],
            "status" => 'open',
            "freelancer_id" => null,
            "category_id" => $request['category_id'],
            "customer_id" => $request['customer_id'],
        ]);
        $project->save();

         return response()->json([
            'success' => true,
            'message' => 'проект успешно создан'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
         Project::find($request->id)->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'проект успешно обновлен'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json([
            'success' => true,
            'message' => 'проект удален'
        ]);
    }
}
