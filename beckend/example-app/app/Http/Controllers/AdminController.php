<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Project;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function users()
    {
        return response()->json(User::query()->orderByDesc('id')->get());
    }

    public function setUserBanned(Request $request, User $user)
    {
        $data = $request->validate([
            'banned' => 'required|boolean',
        ]);

        if ($data['banned'] && (int) $request->user()->id === (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Нельзя заблокировать собственную учётную запись',
            ], 422);
        }

        if (Schema::hasColumn($user->getTable(), 'isBanned')) {
            $user->forceFill(['isBanned' => $data['banned'] ? 1 : 0]);
        } else {
            $user->is_banned = $data['banned'];
        }
        $user->save();

        if ($data['banned']) {
            $user->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => $data['banned'] ? 'Пользователь заблокирован' : 'Блокировка снята',
            'user' => $user->fresh(),
        ]);
    }

    public function getCommissionSettings()
    {
        return response()->json([
            'success' => true,
            'deposit_commission_percent' => SiteSetting::getFloatPercent('deposit_commission_percent', 5.0),
            'withdraw_commission_percent' => SiteSetting::getFloatPercent('withdraw_commission_percent', 5.0),
        ]);
    }

    public function updateCommissionSettings(Request $request)
    {
        $data = $request->validate([
            'deposit_commission_percent' => 'required|numeric|min:0|max:100',
            'withdraw_commission_percent' => 'required|numeric|min:0|max:100',
        ]);

        SiteSetting::setValue('deposit_commission_percent', (string) round((float) $data['deposit_commission_percent'], 2));
        SiteSetting::setValue('withdraw_commission_percent', (string) round((float) $data['withdraw_commission_percent'], 2));

        return response()->json([
            'success' => true,
            'message' => 'Комиссии обновлены',
            'deposit_commission_percent' => SiteSetting::getFloatPercent('deposit_commission_percent', 5.0),
            'withdraw_commission_percent' => SiteSetting::getFloatPercent('withdraw_commission_percent', 5.0),
        ]);
    }

    public function statistics()
    {
        $commissionTotal = (float) (Payment::query()
            ->where('status', 'paid')
            ->whereIn('type', ['input', 'output', 'transfer'])
            ->sum('commission') ?? 0);

        $categoryStats = Project::query()
            ->selectRaw('category_id, COUNT(*) as projects_count')
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->orderByDesc('projects_count')
            ->get();

        $categoryIds = $categoryStats->pluck('category_id')->filter()->unique()->values();
        $categories = Category::query()->whereIn('id', $categoryIds)->get()->keyBy('id');

        $projectsByCategory = $categoryStats->map(function ($row) use ($categories) {
            $cat = $categories->get($row->category_id);

            return [
                'category_id' => (int) $row->category_id,
                'category_name' => $cat?->name ?? ('#'.$row->category_id),
                'projects_count' => (int) $row->projects_count,
            ];
        })->values();

        $freelancerRows = Payment::query()
            ->selectRaw('freelancer_id, COUNT(DISTINCT project_id) as paid_projects_count, SUM(amount) as total_volume')
            ->where('status', 'paid')
            ->where('type', 'transfer')
            ->whereColumn('customer_id', '!=', 'freelancer_id')
            ->whereNotNull('project_id')
            ->groupBy('freelancer_id')
            ->orderByDesc('paid_projects_count')
            ->limit(25)
            ->get();

        $freelancerIds = $freelancerRows->pluck('freelancer_id')->filter()->unique()->values();
        $freelancers = User::query()->whereIn('id', $freelancerIds)->get()->keyBy('id');

        $topFreelancers = $freelancerRows->map(function ($row) use ($freelancers) {
            $u = $freelancers->get($row->freelancer_id);

            return [
                'user_id' => (int) $row->freelancer_id,
                'full_name' => $u?->full_name,
                'login' => $u?->login,
                'paid_projects_count' => (int) $row->paid_projects_count,
                'total_volume' => round((float) $row->total_volume, 2),
            ];
        })->values();

        $projectsTotal = (int) Project::query()->count();
        $paymentsCommissionByType = Payment::query()
            ->selectRaw('type, SUM(commission) as commission_sum')
            ->where('status', 'paid')
            ->whereIn('type', ['input', 'output', 'transfer'])
            ->groupBy('type')
            ->get()
            ->mapWithKeys(fn ($r) => [$r->type => round((float) $r->commission_sum, 2)]);

        return response()->json([
            'success' => true,
            'commission_total' => round($commissionTotal, 2),
            'commission_by_type' => $paymentsCommissionByType,
            'projects_total' => $projectsTotal,
            'projects_by_category' => $projectsByCategory,
            'top_freelancers' => $topFreelancers,
        ]);
    }
}
