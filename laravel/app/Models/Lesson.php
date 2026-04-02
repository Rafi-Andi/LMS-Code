<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $guarded = ['id'];
    public function contents(){
        return $this->hasMany(LessonContent::class, 'lesson_id');
    }

    public function set(){
        return $this->belongsTo(Set::class);
    }
}
