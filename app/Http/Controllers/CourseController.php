<?php

namespace App\Http\Controllers;

use App\Course;
use App\Exercise;
use App\Teacher;
use Illuminate\Http\Request;

class CourseController extends Controller
{

    public function index(){
        $teacherId = session('teacher_id');

        //All teachers courses
        $teachersCourses = Course::Where('organization_id', $teacherId)->get();

        //Data view reply
        $viewData =[
            'teacherCourses' =>  $teachersCourses
        ];

        return $viewData;
    }

    public function display(Course $course){
        $viewData = $course->toArray();
        $session_courses = session('courses');
        $teachers_name = session('teachers_name');
        $courses = collect($session_courses);
        return view("home", ["data"=>$viewData,"courses"=>$courses, "teacher"=>$teachers_name, "current"=>$course->id]);

    }
}
