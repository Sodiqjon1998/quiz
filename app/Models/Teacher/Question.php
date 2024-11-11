<?php

namespace App\Models\Teacher;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends \App\Models\Question
{
    use HasFactory;

    public static function find(){
        return static::query()->where('created_by', '=', \Auth::user()->id);
    }

    public static function getQuizList(){
        $quiz = Quiz::find()->get();
        return $quiz;
    }

    public static function getQuizById($id){
        $quiz = Quiz::find()->findOrFail($id);
        return $quiz;
    }

}
