<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThesisUpload extends Model
{
    protected $fillable = ['student_id', 'thesis_guidance_id', 'category', 'path', 'original_name'];
}
