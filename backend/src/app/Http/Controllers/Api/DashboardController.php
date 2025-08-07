<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard overview statistics
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_projects' => Project::count(),
            'total_clients' => Client::count(),
            'in_progress_projects' => Project::where('status', 'in_progress')->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'paid_projects' => Project::where('status', 'paid')->count(),
            'total_revenue' => Project::where('status', 'paid')->sum('amount'),
            'pending_revenue' => Project::whereIn('status', ['completed'])->sum('amount'),
            'potential_revenue' => Project::whereIn('status', ['contacted', 'in_progress'])->sum('amount'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get revenue statistics by period
     */
    public function revenue(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        $now = Carbon::now();
        
        $query = Project::where('status', 'paid');
        
        switch ($period) {
            case 'week':
                $query->where('payment_date', '>=', $now->startOfWeek());
                break;
            case 'month':
                $query->where('payment_date', '>=', $now->startOfMonth());
                break;
            case 'year':
                $query->where('payment_date', '>=', $now->startOfYear());
                break;
        }
        
        $revenue = $query->sum('amount');
        $count = $query->count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'revenue' => $revenue,
                'project_count' => $count,
                'average_project_value' => $count > 0 ? $revenue / $count : 0
            ]
        ]);
    }

    /**
     * Get project timeline data
     */
    public function projectsTimeline(): JsonResponse
    {
        $projects = Project::with(['client'])
            ->orderBy('contact_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'client_name' => $project->client->name ?? 'Unknown Client',
                    'status' => $project->status,
                    'status_label' => $project->getStatusLabel(),
                    'amount' => $project->amount,
                    'contact_date' => $project->contact_date,
                    'start_date' => $project->start_date,
                    'completion_date' => $project->completion_date,
                    'payment_date' => $project->payment_date,
                    'progress_percentage' => $project->getProgressPercentage(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get recent activities (mock implementation for now)
     */
    public function activities(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);
        
        // Get recent projects for activity feed
        $recentProjects = Project::with(['client'])
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
        
        $activities = $recentProjects->map(function ($project) {
            $timeAgo = $project->updated_at->diffForHumans();
            
            // Generate activity message based on status
            switch ($project->status) {
                case 'contacted':
                    $description = "接洽「{$project->name}」專案";
                    break;
                case 'in_progress':
                    $description = "開始執行「{$project->name}」專案";
                    break;
                case 'completed':
                    $description = "完成「{$project->name}」專案";
                    break;
                case 'paid':
                    $description = "收到「{$project->name}」專案款項";
                    break;
                default:
                    $description = "更新「{$project->name}」專案";
            }
            
            return [
                'id' => $project->id,
                'description' => $description,
                'time' => $timeAgo,
                'project_id' => $project->id,
                'type' => 'project_update'
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    /**
     * Get project status distribution
     */
    public function statusDistribution(): JsonResponse
    {
        $distribution = Project::groupBy('status')
            ->selectRaw('status, count(*) as count, sum(amount) as total_amount')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => [
                    'count' => $item->count,
                    'total_amount' => $item->total_amount,
                    'label' => (new Project(['status' => $item->status]))->getStatusLabel()
                ]];
            });

        return response()->json([
            'success' => true,
            'data' => $distribution
        ]);
    }

    /**
     * Get category breakdown
     */
    public function categoryBreakdown(): JsonResponse
    {
        $breakdown = Project::groupBy('category')
            ->selectRaw('category, count(*) as count, sum(amount) as total_amount')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category => [
                    'count' => $item->count,
                    'total_amount' => $item->total_amount,
                    'label' => (new Project(['category' => $item->category]))->getCategoryLabel()
                ]];
            });

        return response()->json([
            'success' => true,
            'data' => $breakdown
        ]);
    }

    /**
     * Get monthly revenue trend
     */
    public function revenueTrend(Request $request): JsonResponse
    {
        $months = min($request->get('months', 12), 24);
        
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $revenue = Project::where('status', 'paid')
                ->whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount');
                
            $data[] = [
                'month' => $date->format('Y-m'),
                'month_name' => $date->format('Y年n月'),
                'revenue' => $revenue
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
