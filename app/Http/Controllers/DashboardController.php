<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Card;
use App\Models\AiCredit;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get user's boards
        $boards = $user->boards()->withCount('cards')->latest()->take(5)->get();
        
        // Get assigned cards with due dates
        $upcomingCards = $user->assignedCards()
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->take(10)
            ->get();
        
        // Get AI credits status
        $aiCredits = AiCredit::latest('updated_at')->get()->map(function ($credit) {
            return [
                'id' => $credit->id,
                'service' => $credit->service,
                'total_credits' => $credit->total_credits,
                'used_credits' => $credit->used_credits,
                'remaining_credits' => $credit->remaining_credits,
                'percentage_used' => round($credit->percentage_used, 0),
                'percentage_remaining' => round($credit->percentage_remaining, 0),
                'billing_period_start' => $credit->billing_period_start,
                'billing_period_end' => $credit->billing_period_end,
            ];
        });
        
        // Get recent activity
        $recentActivity = ActivityLog::with(['user', 'card', 'board'])
            ->whereIn('board_id', $user->boards()->pluck('id'))
            ->latest()
            ->take(15)
            ->get();
        
        // Statistics
        $stats = [
            'total_boards' => $user->boards()->count(),
            'total_cards' => Card::whereIn('board_id', $user->boards()->pluck('id'))->count(),
            'cards_in_progress' => $user->assignedCards()->count(),
            'overdue_cards' => $user->assignedCards()
                ->where('due_date', '<', now())
                ->count(),
        ];
        
        return Inertia::render('Dashboard', [
            'boards' => $boards,
            'upcomingCards' => $upcomingCards,
            'aiCredits' => $aiCredits,
            'recentActivity' => $recentActivity,
            'stats' => $stats,
        ]);
    }
}
