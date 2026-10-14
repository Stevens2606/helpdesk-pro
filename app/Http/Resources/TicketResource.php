<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
   public function toArray(Request $request): array
{
    return [
        'id'              => $this->id,
        'code'            => $this->code,
        'subject'         => $this->subject,
        'description'     => $this->description,
        'status'          => $this->status->value,
        'priority'        => $this->priority,
        'category'        => $this->category?->name,
        'customer'        => $this->customer?->name,
        'assignee'        => $this->assignee?->name,
        'due_at'          => $this->due_at?->toIso8601String(),
        'sla_breached_at' => $this->sla_breached_at?->toIso8601String(),
        'created_at'      => $this->created_at->toIso8601String(),
        'comments'        => CommentResource::collection($this->whenLoaded('comments')),
    ];
}
}
