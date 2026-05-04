<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryRequestController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $name = trim($validated['name']);

        $exists = Category::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Такая категория уже существует',
            ], 422);
        }

        $alreadyPending = CategoryRequest::query()
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->exists();

        if ($alreadyPending) {
            return response()->json([
                'success' => false,
                'message' => 'Заявка на такую категорию уже отправлена и ожидает модерации',
            ], 422);
        }

        $req = CategoryRequest::create([
            'user_id' => $user->id,
            'name' => $name,
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Заявка отправлена на модерацию',
            'request' => $req,
        ]);
    }

    public function pending()
    {
        $items = CategoryRequest::query()
            ->where('status', 'pending')
            ->latest()
            ->with(['user:id,login,full_name'])
            ->get();

        return response()->json([
            'success' => true,
            'items' => $items,
        ]);
    }

    public function approve(Request $request, CategoryRequest $categoryRequest)
    {
        if ($categoryRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Заявка уже обработана',
            ], 422);
        }

        return DB::transaction(function () use ($request, $categoryRequest) {
            $name = trim((string) $categoryRequest->name);

            $exists = Category::query()
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                ->exists();

            if ($exists) {
                $categoryRequest->update([
                    'status' => 'rejected',
                    'reviewed_by' => $request->user()->id,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Категория уже существует (заявка отклонена)',
                ], 422);
            }

            $category = Category::create([
                'name' => $name,
                'description' => $categoryRequest->description,
            ]);

            $categoryRequest->update([
                'status' => 'approved',
                'category_id' => $category->id,
                'reviewed_by' => $request->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Заявка утверждена, категория добавлена',
                'category' => $category,
            ]);
        });
    }

    public function reject(Request $request, CategoryRequest $categoryRequest)
    {
        if ($categoryRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Заявка уже обработана',
            ], 422);
        }

        $categoryRequest->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Заявка отклонена',
        ]);
    }
}

