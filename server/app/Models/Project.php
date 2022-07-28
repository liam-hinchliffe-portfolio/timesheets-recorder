<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reference',
        'client_id',
    ];

    public function timeRecords () {
        return $this->hasMany(TimeRecord::class);
    }

    public function projectStages () {
        return $this->hasMany(ProjectStage::class);
    }

    public function client () {
        return $this->belongsTo(Client::class);
    }
}
