<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reference'
    ];

    public function projects () {
        return $this->hasMany(Project::class);
    }

    public function department () {
        return $this->belongsTo(Department::Class);
    }
}
