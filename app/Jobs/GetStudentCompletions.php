<?php

namespace App\Jobs;

use App\Events\GotStudentCompletions;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Avanderbergh\Schoology\SchoologyApi;
use Storage;
use App\School;

/**
 * Class GetStudentCompletions
 * @package App\Jobs
 */
class GetStudentCompletions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $school_id;
    private $user_id;
    private $realm_id;
    private $enrollment;

    /**
     * Create a new job instance.
     * @param $user_id
     * @param $realm_id
     * @param $enrollment
     */
    public function __construct($school_id, $user_id, $realm_id, $enrollment)
    {

        $this->school_id = $school_id;
        $this->user_id = $user_id;
        $this->realm_id = $realm_id;
        $this->enrollment = $enrollment;
    }

    /**
     * Execute the job.
     * @return void
     * @internal param $enrollment
     */
    public function handle()
    {
        $this->enrollment->sections = [];
        $school = School::findOrFail($this->school_id);
        $schoology = new SchoologyApi($school->api_key,$school->api_secret,null,null,null, true);

//        Get the API user
        $api_user = $schoology->apiResult('users/me');

        $user = $schoology->apiResult(sprintf('users/%s', $this->enrollment->uid));
        $sections = $schoology->apiResult(sprintf('users/%s/sections', $this->enrollment->uid))->section;
        $total_sections = sizeof($sections);
        $completed_sections = 0;
        $student = (object) [
            'id' => $this->enrollment->uid,
            'name' => htmlspecialchars_decode($this->enrollment->name_display, ENT_QUOTES),
            'picture_url' => $this->enrollment->picture_url,
            'grad_year' => $user->grad_year,
            'sections' => []
        ];
        foreach ($sections as $section) {
            $enrollments = $schoology->apiResult(sprintf('sections/%s/enrollments?uid=%s', $section->id, $api_user->uid));
            if (count($enrollments->enrollment)) {
                // The API user is enrolled in the course.
                if ($enrollments->enrollment[0]->admin != 1 || $enrollments->enrollment[0]->status > 1) {
                    // Observer is enrolled, but not an admin or inactive
                    $body = ['uid' => $api_user->uid, 'admin' => 1, 'status' => 1];
                    try {
                        $schoology->apiResult(sprintf('sections/%s/enrollments/%s', $section->id,$enrollments->enrollment[0]->id), 'PUT', $body);
                    } catch (\Exception $e){
                    }
                }
            } else {
                $body = ['uid' => $api_user->uid, 'admin' => 1, 'status' => 1];
                try {
                    $schoology->apiResult(sprintf('sections/%s/enrollments', $section->id), 'POST', $body);
                } catch (\Exception $e){
                }
            }
//                get the completion rules
            $completion_success = true;
            try {
                $completion = $schoology->apiResult(sprintf('sections/%s/completion/user/%s', $section->id, $this->enrollment->uid));
            } catch (\Exception $e){
                $completion_success = false;
            }
            // Could not fetch completions, so set to 0
            if (!$completion_success) {
                $completion = (object) [
                    "total_rules" => 0,
                    "completed_rules" => 0,
                    "completed" => 0,
                ];
            }

//            Get the enrollment ID
            try {
                $enrollment_id = $schoology->apiResult(sprintf('sections/%s/enrollments?uid=%s', $section->id, $this->enrollment->uid))->enrollment[0]->id;
            } catch (\Exception $e){
            }
//            Get the grades
            try {
                $grades = $schoology->apiResult(sprintf('sections/%s/grades?enrollment_id=%s',$section->id, $enrollment_id))->grades;
            } catch (\Exception $e){
            }
            $total_grades = 0;
            $completed_grades = 0;
            foreach ($grades->grade as $grade){
                if ($grade->category_id) {$total_grades++;}
                if ($grade->grade) {$completed_grades++;}
            }

            $student->sections[] = (object) [
                'course_title' => $section->course_title,
                'section_title' => $section->section_title,
                'completions' => [
                    'total_rules' => $completion->total_rules,
                    'completed_rules' => $completion->completed_rules,
                ],
                'grades' => [
                    'total_grades' => $total_grades,
                    'completed_grades' => $completed_grades
                ],
                'completed' => ($completion->completed && $completed_grades == $total_grades),
            ];
            if ($completion->completed && $completed_grades == $total_grades){
                $completed_sections++;
            }
        }
        $student->total_sections = $total_sections;
        $student->completed_sections = $completed_sections;
        event(new GotStudentCompletions($this->user_id, $this->realm_id, $student));
    }
}