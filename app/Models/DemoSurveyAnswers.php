<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemoSurveyAnswers extends Model
{
    use HasFactory;
    // The attributes that are mass assignable.
    protected $fillable = [
        'AnsweredBy',
        'QuestionID',
        'AnswerValue',
    ];
    // belongsTo relationship with DemoUsers
    public function demoUsers()
    {
        return $this->belongsTo(DemoUsers::class, 'AnsweredBy', 'id');
    }
}
