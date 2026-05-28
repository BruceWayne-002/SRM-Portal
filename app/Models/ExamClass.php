<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamClass extends Model
{
    protected $fillable = [
        'exam_id',
        'class_name'
    ];


    public function classes()
{
    return $this->hasMany(\App\Models\ExamClass::class);
}


}