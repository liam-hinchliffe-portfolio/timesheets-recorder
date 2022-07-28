<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'minutes',
        'notes',
        'date',
        'user_id',
        'project_id',
        'project_stage_id',
    ];

    public function user () {
        return $this->belongsTo(User::class);
    }

    public function project () {
        return $this->belongsTo(Project::class);
    }

    public function projectStage () {
        return $this->belongsTo(ProjectStage::class);
    }
}
