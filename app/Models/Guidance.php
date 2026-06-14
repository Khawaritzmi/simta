<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guidance extends Model
{
    protected $fillable = ['student_id', 'type', 'completed_at', 'notes'];

    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }
}
