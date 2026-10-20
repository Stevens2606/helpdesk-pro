<?php

namespace App\Http\Controllers;

use App\Exports\TicketsExport;
use App\Models\Category;
use App\Models\Ticket;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return view('reports.index', [
            'byStatus'   => Ticket::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'byCategory' => Category::withCount('tickets')->get(),
            'breached'   => Ticket::whereNotNull('sla_breached_at')->count(),
            'total'      => Ticket::count(),
        ]);
    }

    public function export()
    {
        abort_unless(auth()->user()->role === 'admin', 403);

        return Excel::download(new TicketsExport, 'tiket-' . now()->format('Ymd') . '.xlsx');
    }
}