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

/**
 * Class GetStudentCompletions
 * @package App\Jobs
 */
class GetStudentCompletions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user_id;
    private $realm_id;
    private $enrollment;

    /**
     * Create a new job instance.
     * @param $user_id
     * @param $realm_id
     * @param $enrollment
     */
    public function __construct($user_id, $realm_id, $enrollment)
    {

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
        $schoology = new SchoologyApi('48bb142fedc4be0b27e459fd41f59438058930366','b3d5cb7b044875606f48f70918d240a7',null,null,null, true);
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
//                get the completion rules
            try {
                $completion = $schoology->apiResult(sprintf('sections/%s/completion/user/%s', $section->id, $this->enrollment->uid));
            } catch (\Exception $e){
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