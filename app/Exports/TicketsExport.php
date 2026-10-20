<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TicketsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return Ticket::with(['customer:id,name', 'assignee:id,name', 'category'])->latest();
    }

    public function headings(): array
    {
        return ['Kode', 'Subjek', 'Kategori', 'Status', 'Prioritas', 'Pelanggan', 'Ditugaskan', 'Batas SLA', 'SLA Lewat'];
    }

    public function map($ticket): array
    {
        return [
            $ticket->code,
            $ticket->subject,
            $ticket->category->name,
            $ticket->status->value,
            $ticket->priority,
            $ticket->customer->name,
            $ticket->assignee?->name ?? '-',
            $ticket->due_at?->format('Y-m-d H:i'),
            $ticket->sla_breached_at ? 'Ya' : 'Tidak',
        ];
    }
}