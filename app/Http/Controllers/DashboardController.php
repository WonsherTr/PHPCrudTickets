<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $query = Ticket::query();

        if (! $user->isAdmin()) {
            $query->where('created_by', $user->id);
        }

        $stats = [
            'total'       => (clone $query)->count(),
            'open'        => (clone $query)->where('status', 'OPEN')->count(),
            'in_progress' => (clone $query)->where('status', 'IN_PROGRESS')->count(),
            'resolved'    => (clone $query)->where('status', 'RESOLVED')->count(),
            'closed'      => (clone $query)->where('status', 'CLOSED')->count(),
        ];

        $recent = (clone $query)->with('creator')->latest()->take(5)->get();

        return view('dashboard', compact('stats', 'recent'));
    }
}
