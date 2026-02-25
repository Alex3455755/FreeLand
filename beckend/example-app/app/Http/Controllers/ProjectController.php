<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Log;
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
       public function myProjects(Request $request,User $user)
    {
        try {
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Пользователь не авторизован'
                ], 401);
            }
            
            Log::info('Загрузка проектов для пользователя: ' . $user->id . ', роль: ' . $user->role);
            
            // Для заказчика
            if ($user->role === 'customer' || $user->role === 'заказчик') {
                $projects = Project::where('customer_id', $user->id)
                    ->with(['customer', 'freelancer', 'category'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                    
                return response()->json([
                    'success' => true,
                    'projects' => $projects
                ]);
            }
            
            // Для фрилансера
            if ($user->role === 'freelancer' || $user->role === 'фрилансер') {
                $projects = Project::where('freelancer_id', $user->id)
                    ->with(['customer', 'freelancer', 'category'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                    
                return response()->json([
                    'success' => true,
                    'projects' => $projects
                ]);
            }
            
            // Для админа
            if ($user->role === 'admin') {
                $projects = Project::with(['customer', 'freelancer', 'category'])
                    ->orderBy('created_at', 'desc')
                    ->get();
                    
                return response()->json([
                    'success' => true,
                    'projects' => $projects
                ]);
            }
            
            return response()->json([
                'success' => true,
                'projects' => []
            ]);
            
        } catch (\Exception $e) {
            Log::error('Ошибка при загрузке моих проектов: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке проектов: ' . $e->getMessage()
            ], 500);
        }
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
        return response()->json([
            'project' => $project
        ]);
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
