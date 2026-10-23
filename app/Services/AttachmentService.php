<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class AttachmentService
{
    /**
     * Simpan file dan kaitkan ke model (Ticket atau TicketComment).
     */
    public function store(Model $model, array $files): void
    {
        foreach ($files as $file) {
            $model->attachments()->create([
                'path'          => $file->store('attachments', 'public'),
                'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
            ]);
        }
    }
}