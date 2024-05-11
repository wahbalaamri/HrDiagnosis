<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class DemoUsers extends Model
{
    use HasFactory, Uuids, SoftDeletes;
    // The attributes that are mass assignable.
    protected $fillable = [
        'email',
        'mobile',
        'name',
        'country',
    ];
    // hasMany relationship with DemoPrioritiesAnswers
    public function demoPrioritiesAnswers()
    {
        return $this->hasMany(DemoPrioritiesAnswers::class, 'AnsweredBy', 'id');
    }
    // hasMany relationship with DemoSurveyAnswers
    public function demoSurveyAnswers()
    {
        return $this->hasMany(DemoSurveyAnswers::class, 'AnsweredBy', 'id');
    }
    //delete demoPrioritiesAnswers and demoSurveyAnswers permanently when soft deleting a user
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
        static::deleting(function ($user) {
            $user->demoPrioritiesAnswers()->delete();
            $user->demoSurveyAnswers()->delete();
        });
    }

}
