<?php

namespace App\Models;

use App\Enums\TicketStatus;
use App\Observers\TicketObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(TicketObserver::class)]
class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'user_id',
        'assigned_to',
        'category_id',
        'subject',
        'description',
        'status',
        'priority',
        'due_at',
        'resolved_at',
    ];

    protected $casts = [
        'status'      => TicketStatus::class,
        'due_at'      => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TicketLog::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
                TicketStatus::Resolved->value,
                TicketStatus::Closed->value,
            ])
            ->where('due_at', '<', now());
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            $ticket->code = 'TKT-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));

            if (! $ticket->due_at) {
                $ticket->due_at = now()->addHours(
                    Category::findOrFail($ticket->category_id)->sla_hours
                );
            }
        });
    }
}