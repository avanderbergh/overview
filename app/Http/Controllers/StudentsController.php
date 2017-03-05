<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;
use App\Jobs\GetStudentCompletions;
use Maatwebsite\Excel\Facades\Excel;
use Avanderbergh\Schoology\SchoologyApi;
use Avanderbergh\Schoology\Facades\Schoology;
use App\School;

class StudentsController extends Controller
{
    public function index($realm_id)
    {
        Schoology::authorize();
        $user_id = auth()->user()->id;
        $school_id = session('schoology')['school_nid'];
        $school = School::findOrFail($school_id);
        $schoology = new SchoologyApi($school->api_key,$school->api_secret,null,null,null, true);
        $api_user = $schoology->apiResult('users/me');
        $api_user_enrollments = $schoology->apiResult(sprintf('users/%s/sections', $api_user->uid));
        $enrollments_array = [];
        foreach ($api_user_enrollments->section as $enrollment){
            $enrollments_array[] = $enrollment->id;
        }
        $courses = $schoology->apiResult('courses')->course;
        foreach ($courses as $course){
            $sections = $schoology->apiResult(sprintf('courses/%s/sections', $course->id))->section;
            foreach ($sections as $section){
                if (!in_array($section->id, $enrollments_array)){
                    $body = ['uid' => $api_user->uid, 'admin' => 1, 'status' => 1];
                    if ($body){
                        $schoology->apiResult(sprintf('sections/%s/enrollments', $section->id), 'POST', $body);
                    }
                }
            }
        }

        $result = $schoology->apiResult(sprintf('groups/%s/enrollments', $realm_id));
        $enrollments = [];
        while(property_exists($result->links, 'next')){
            $enrollments = array_merge($enrollments, $result->enrollment);
            $result = $schoology->apiResult(sprintf('groups/%s/enrollments', $realm_id) . substr($result->links->next, strpos($result->links->next, '?')));
        }
        $enrollments = array_merge($enrollments, $result->enrollment);
        $total_students = 0;
        foreach ($enrollments as $enrollment){
            if (!$enrollment->admin && $enrollment->status == 1) {
                Student::firstOrCreate([
                    'id' => $enrollment->uid,
                    'school_id' => $school_id,
                    'name_display' => $enrollment->name_display
                ]);
                $school_students = Student::where('school_id', $school_id)->count();
                if ($school_students <= $school->user_quota) {
                    $job = (new GetStudentCompletions($school_id, $user_id, $realm_id, $enrollment))->onQueue('students');
                    dispatch($job);
                    $total_students++;
                } else {
                    return -1;
                }
            }
        }
        return $total_students;
    }

    public function export($realm_id)
    {
        Schoology::authorize();
        $school = School::findOrFail(session('schoology')['school_nid']);
        $schoology = new SchoologyApi($school->api_key,$school->api_secret,null,null,null, true);
        $api_user = $schoology->apiResult('users/me');
        $api_user_enrollments = $schoology->apiResult(sprintf('users/%s/sections', $api_user->uid));
        $enrollments_array = [];
        foreach ($api_user_enrollments->section as $enrollment){
            $enrollments_array[] = $enrollment->id;
        }
        $courses = $schoology->apiResult('courses')->course;
        foreach ($courses as $course){
            $sections = $schoology->apiResult(sprintf('courses/%s/sections', $course->id))->section;
            foreach ($sections as $section){
                if (!in_array($section->id, $enrollments_array)){
                    $body = ['uid' => $api_user->uid, 'admin' => 1, 'status' => 1];
                    if ($body){
                        $schoology->apiResult(sprintf('sections/%s/enrollments', $section->id), 'POST', $body);
                    }
                }
            }
        }
        $students = [];
        $student_details = [];
        $enrollments = $schoology->apiResult(sprintf('groups/%s/enrollments', $realm_id))->enrollment;
        foreach ($enrollments as $enrollment){
            if (!$enrollment->admin && $enrollment->status == 1) {
                $enrollment->sections = [];
                $user = $schoology->apiResult(sprintf('users/%s', $enrollment->uid));
                $sections = $schoology->apiResult(sprintf('users/%s/sections', $enrollment->uid))->section;

                $student = [
                    'id' => $enrollment->uid,
                    'name' => htmlspecialchars_decode($enrollment->name_display, ENT_QUOTES),
                    'grad_year' => $user->grad_year,
                    'total_sections' => sizeof($sections),
                    'completed_sections' => 0,
                    'total_grades' => 0,
                    'completed_grades' => 0,
                    'total_rules' => 0,
                    'completed_rules' =>0
                ];
                foreach ($sections as $section) {
//                get the completion rules
                    try {
                        $completion = $schoology->apiResult(sprintf('sections/%s/completion/user/%s', $section->id, $enrollment->uid));
                    } catch (\Exception $e){
                    }
                    $student['total_rules'] += $completion->total_rules;
                    $student['completed_rules'] += $completion->completed_rules;
//            Get the enrollment ID
                    try {
                        $enrollment_id = $schoology->apiResult(sprintf('sections/%s/enrollments?uid=%s', $section->id, $enrollment->uid))->enrollment[0]->id;
                    } catch (\Exception $e){
                    }
//            Get the grades
                    try {
                        $grades = $schoology->apiResult(sprintf('sections/%s/grades?enrollment_id=%s',$section->id, $enrollment_id))->grades;
                    } catch (\Exception $e){
                    }
                    $section_grades_total = 0;
                    $section_grades_completed = 0;
                    foreach ($grades->grade as $grade){
                        if ($grade->category_id) {
                            $student['total_grades']++;
                            $section_grades_total++;
                        }
                        if ($grade->grade) {
                            $student['completed_grades']++;
                            $section_grades_completed++;
                        }
                    }

                    if ($completion->completed && $section_grades_total == $section_grades_completed){
                        $student['completed_sections']++;
                    }
                    $student_details[]=[
                        'Student Name' =>  htmlspecialchars_decode($enrollment->name_display, ENT_QUOTES),
                        'Course' => $section->course_title,
                        'Section' => $section->section_title,
                        'Total Rules' => $completion->total_rules,
                        'Completed Rules' => $completion->completed_rules,
                        'Total Grades' => $section_grades_total,
                        'Completed Grades' => $section_grades_completed,
                    ];
                }
                $students[] = $student;
            }
        }

        Excel::create('Overview', function($excel) use ($students, $student_details){
            $excel->sheet('Completions', function($sheet) use ($students){
                $sheet->fromArray($students, null, 'A1', true);
            });
            $excel->sheet('Details', function($sheet) use ($student_details){
                $sheet->fromArray($student_details, null, 'A1', true);
            });
        })->download('xlsx');
    }
}
