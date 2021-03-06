<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

//osea students...
class User extends Model
{
    protected $visible = ['id', 'login', 'average', 'number_submissions', 'risk_tag', 'name'];
    protected $appends = array('user_id', 'average', 'number_submissions', 'risk_tag', 'name');

    public function submissions() {
        return $this->hasMany('App\Submission');
    }
    public function teacherships(){
        return $this->hasOne('App\Teachership');
    }
    public function userFieldValue(){
        return $this->hasOne('App\UserFieldValue');
    }


    public function exercises() {
        return $this->hasManyThrough(
            'App\Exercise',
            'App\Submission',
            'user_id',
            'name',
            'user_id',
            'exercise_name'
        );
    }

    public function getUserIdAttribute() {
        return $this->id;
    }

    public function getNameAttribute() {
        if( $this->userFieldValue != null ){
            return $this->userFieldValue->field_name;
        }
        return null;
    }

    public function getMaxSubmissionsPerExerciseAttribute() {
        return $this->submissions
            ->groupBy('exercise_name')->max()->count();
    }

    public function getMinSubmissionsPerExerciseAttribute() {
        return $this->submissions
            ->groupBy('exercise_name')->min()->count();
    }

    public function getAvgSubmissionsPerExerciseAttribute() {
        $groups = $this->submissions->groupBy('exercise_name');

        if ($groups->count() < 1) return 0;

        $sum = 0;
        foreach ($groups as $group) {
            $sum += count($group);
        }
        return $sum / $groups->count();
    }

    public function points(){

    }
    public function exercisePoints($exercise){

    }

    public function getAverageAttribute() {
        $average = 0;
        $submissions = $this->submissions;

        if (count($submissions) > 0) {
            foreach ($submissions as $submission) {
                $average = $average + $submission->points;
            }
            $average = $average/ $submissions->count();

            return number_format($average,2);
        } else {
            return 0;
        }
    }

    public function getNumberSubmissionsAttribute(){
        return $this->submissions()->count();
    }

    public function getRiskTagAttribute(){
        $riskTag = '';

        if ($this->average < 5) {
            $riskTag = 'En riesgo';
        } elseif ($this->average < 7) {
            $riskTag = 'Regular';
        } elseif ($this->average < 9) {
            $riskTag = 'Apropiado';
        } else {
            $riskTag = 'Sobresaliente';
        }

        return $riskTag;
    }

}
