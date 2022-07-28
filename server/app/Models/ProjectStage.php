<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'project_id'
    ];

    public function timeRecords () {
        return $this->hasMany(TimeRecord::class);
    }

    public function project () {
        return $this->belongsTo(Project::class);
    }
}
