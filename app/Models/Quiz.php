<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'duration',
        'passing_grade',
        'material_id'
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }
}
