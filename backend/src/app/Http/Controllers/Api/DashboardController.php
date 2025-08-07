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
            
            $revenue = Project::where('status', 'completed')
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

    /**
     * Get clients statistics
     */
    public function clients(Request $request): JsonResponse
    {
        $stats = [
            'total_clients' => Client::count(),
            'active_clients' => Client::where('is_active', true)->count(),
            'clients_with_projects' => Client::has('projects')->count(),
            'new_clients_this_month' => Client::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'top_clients' => Client::withCount('projects')
                ->orderBy('projects_count', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'projects_count']),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get top clients by revenue
     */
    public function topClients(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);
        $period = $request->get('period', 'year');

        $query = Client::withSum(['projects as total_revenue' => function($query) use ($period) {
            $query->where('status', 'completed');
            
            if ($period === 'month') {
                $query->whereMonth('payment_date', now()->month)
                      ->whereYear('payment_date', now()->year);
            } elseif ($period === 'year') {
                $query->whereYear('payment_date', now()->year);
            }
        }], 'amount')
        ->having('total_revenue', '>', 0)
        ->orderBy('total_revenue', 'desc')
        ->limit($limit);

        $clients = $query->get(['id', 'name', 'total_revenue']);

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'clients' => $clients
            ]
        ]);
    }

    /**
     * Get upcoming deadlines
     */
    public function deadlines(Request $request): JsonResponse
    {
        $days = min($request->get('days', 30), 90);
        $endDate = Carbon::now()->addDays($days);

        $projects = Project::with(['client'])
            ->whereIn('status', ['pending', 'in_progress'])
            ->where(function($query) use ($endDate) {
                $query->whereBetween('start_date', [now(), $endDate])
                      ->orWhereBetween('completion_date', [now(), $endDate]);
            })
            ->orderBy('start_date')
            ->get()
            ->map(function($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'client' => $project->client->name,
                    'status' => $project->status,
                    'start_date' => $project->start_date,
                    'completion_date' => $project->completion_date,
                    'days_until_start' => $project->start_date ? Carbon::parse($project->start_date)->diffInDays(now(), false) : null,
                    'days_until_completion' => $project->completion_date ? Carbon::parse($project->completion_date)->diffInDays(now(), false) : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $projects
        ]);
    }

    /**
     * Get revenue by period
     */
    public function revenueByPeriod(string $period, Request $request): JsonResponse
    {
        $data = [];
        
        switch($period) {
            case 'day':
                $data = $this->getDailyRevenue($request);
                break;
            case 'week':
                $data = $this->getWeeklyRevenue($request);
                break;
            case 'month':
                $data = $this->getMonthlyRevenue($request);
                break;
            case 'year':
                $data = $this->getYearlyRevenue($request);
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid period. Use: day, week, month, year'
                ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => $period
        ]);
    }

    /**
     * Get projects by period
     */
    public function projectsByPeriod(string $period, Request $request): JsonResponse
    {
        $data = [];
        
        switch($period) {
            case 'day':
                $data = $this->getDailyProjects($request);
                break;
            case 'week':
                $data = $this->getWeeklyProjects($request);
                break;
            case 'month':
                $data = $this->getMonthlyProjects($request);
                break;
            case 'year':
                $data = $this->getYearlyProjects($request);
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid period. Use: day, week, month, year'
                ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => $period
        ]);
    }

    /**
     * Get daily statistics
     */
    public function dailyStats(Request $request): JsonResponse
    {
        $startDate = Carbon::parse($request->get('start_date', now()->subDays(30)));
        $endDate = Carbon::parse($request->get('end_date', now()));

        $data = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $dayStart = $currentDate->copy()->startOfDay();
            $dayEnd = $currentDate->copy()->endOfDay();

            $stats = [
                'date' => $currentDate->format('Y-m-d'),
                'projects_created' => Project::whereBetween('created_at', [$dayStart, $dayEnd])->count(),
                'projects_completed' => Project::where('status', 'completed')
                    ->whereBetween('completion_date', [$dayStart, $dayEnd])->count(),
                'revenue' => Project::where('status', 'completed')
                    ->whereBetween('payment_date', [$dayStart, $dayEnd])->sum('amount'),
            ];

            $data[] = $stats;
            $currentDate->addDay();
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get weekly statistics
     */
    public function weeklyStats(Request $request): JsonResponse
    {
        $startDate = Carbon::parse($request->get('start_date', now()->subWeeks(12)));
        $endDate = Carbon::parse($request->get('end_date', now()));

        $data = [];
        $currentWeek = $startDate->copy()->startOfWeek();

        while ($currentWeek <= $endDate) {
            $weekStart = $currentWeek->copy()->startOfWeek();
            $weekEnd = $currentWeek->copy()->endOfWeek();

            $stats = [
                'week' => $currentWeek->format('Y-\WW'),
                'start_date' => $weekStart->format('Y-m-d'),
                'end_date' => $weekEnd->format('Y-m-d'),
                'projects_created' => Project::whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                'projects_completed' => Project::where('status', 'completed')
                    ->whereBetween('completion_date', [$weekStart, $weekEnd])->count(),
                'revenue' => Project::where('status', 'completed')
                    ->whereBetween('payment_date', [$weekStart, $weekEnd])->sum('amount'),
            ];

            $data[] = $stats;
            $currentWeek->addWeek();
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get yearly statistics
     */
    public function yearlyStats(Request $request): JsonResponse
    {
        $year = $request->get('year', now()->year);
        $startDate = Carbon::createFromDate($year, 1, 1)->startOfYear();
        $endDate = Carbon::createFromDate($year, 12, 31)->endOfYear();

        $stats = [
            'year' => $year,
            'total_projects' => Project::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completed_projects' => Project::where('status', 'completed')
                ->whereBetween('completion_date', [$startDate, $endDate])->count(),
            'total_revenue' => Project::where('status', 'completed')
                ->whereBetween('payment_date', [$startDate, $endDate])->sum('amount'),
            'new_clients' => Client::whereBetween('created_at', [$startDate, $endDate])->count(),
            'monthly_breakdown' => $this->getMonthlyBreakdown($year),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    // Helper methods for revenue/project statistics
    private function getDailyRevenue(Request $request): array
    {
        return ['message' => 'Daily revenue data - implement based on requirements'];
    }

    private function getWeeklyRevenue(Request $request): array
    {
        return ['message' => 'Weekly revenue data - implement based on requirements'];
    }

    private function getMonthlyRevenue(Request $request): array
    {
        return ['message' => 'Monthly revenue data - implement based on requirements'];
    }

    private function getYearlyRevenue(Request $request): array
    {
        return ['message' => 'Yearly revenue data - implement based on requirements'];
    }

    private function getDailyProjects(Request $request): array
    {
        return ['message' => 'Daily projects data - implement based on requirements'];
    }

    private function getWeeklyProjects(Request $request): array
    {
        return ['message' => 'Weekly projects data - implement based on requirements'];
    }

    private function getMonthlyProjects(Request $request): array
    {
        return ['message' => 'Monthly projects data - implement based on requirements'];
    }

    private function getYearlyProjects(Request $request): array
    {
        return ['message' => 'Yearly projects data - implement based on requirements'];
    }

    private function getMonthlyBreakdown(int $year): array
    {
        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth();

            $data[] = [
                'month' => $month,
                'month_name' => $monthStart->format('n月'),
                'projects_created' => Project::whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'projects_completed' => Project::where('status', 'completed')
                    ->whereBetween('completion_date', [$monthStart, $monthEnd])->count(),
                'revenue' => Project::where('status', 'completed')
                    ->whereBetween('payment_date', [$monthStart, $monthEnd])->sum('amount'),
            ];
        }
        return $data;
    }
}
