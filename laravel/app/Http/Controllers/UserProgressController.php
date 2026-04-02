<?php

namespace App\Http\Controllers;

use App\Models\CompletedLesson;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserProgressController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $enrollmentIds = Enrollment::where('user_id', $user->id)->select('course_id')->pluck('course_id');
        $completedLessonIds = CompletedLesson::where('user_id', $user->id)->select('lesson_id')->pluck('lesson_id');

        $progress = Course::whereIn('id', $enrollmentIds)->get();


        $results = [];

        foreach ($progress as $progres) {
            $completedLesson = [];

            $setIds = Set::where('course_id', $progres->id)->select('id')->pluck('id');

            Log::info("set id: {$setIds}");

            $lessons = Lesson::whereIn('set_id', $setIds)->get();

            Log::info($completedLessonIds);
            foreach ($lessons as $lesson) {
                if (in_array($lesson->id, $completedLessonIds->toArray())) {
                    Log::info("Ada Completed {$lesson}");

                    $completedLesson[] = [
                        "id" => $lesson->id,
                        "name" => $lesson->name,
                        "order" => $lesson->order
                    ];
                }
            }

            $results[] = [
                "course" => $progres,
                "completed_lessons" => $completedLesson
            ];
        }

        return response()->json([
            "status" => "success",
            "message" => "User progress retrieved successfully",
            "data" => [
                "progress" => $results
            ]
        ], 200);
    }
}
