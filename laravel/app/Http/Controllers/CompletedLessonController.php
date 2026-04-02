<?php

namespace App\Http\Controllers;

use App\Models\CompletedLesson;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompletedLessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $lesson_id)
    {
        $user = $request->user();

        $lesson = Lesson::find($lesson_id);

        if (!$lesson) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $completedLesson = CompletedLesson::create([
            "user_id" => $user->id,
            "lesson_id" => $lesson_id
        ]);

        Log::info($completedLesson);

        return response()->json([
            "status" => "success",
            "message" => "Lesson successfully completed"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CompletedLesson $completedLesson)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CompletedLesson $completedLesson)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CompletedLesson $completedLesson)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CompletedLesson $completedLesson)
    {
        //
    }
}
