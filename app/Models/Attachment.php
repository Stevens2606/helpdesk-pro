<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    protected $fillable = ['path', 'original_name', 'size'];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }
}