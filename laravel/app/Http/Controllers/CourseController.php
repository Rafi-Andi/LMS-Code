<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $course = Course::all();

        return response()->json([
            "status" => "success",
            "message" => "Courses retrieved successfully",
            "data" => $course
        ], 200);
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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "description" => "required",
            "slug" => "required|unique:courses,slug"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid field(s) in reques",
                "errors" => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $courses = Course::create($validated);

        return response()->json([
            'status' => "success",
            "message" => "course successful added",
            "data" => $courses
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $course = Course::where('slug', $slug)->with('sets', 'sets.lessons', 'sets.lessons.contents', 'sets.lessons.contents.options')->first();
        if (!$course) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "message" => "Course details retrieved successfully",
            "data" => $course
        ], 200);
    }

    public function edit(Course $course)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        $course = Course::where('slug', $slug)->first();

        if (!$course) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            "name" => "nullable|string",
            "description" => "nullable|string",
            "slug" => "nullable|string|unique:courses,slug,{$slug}"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid field(s) in reques",
                "errors" => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $course->update($validated);

        return response()->json([
            "status" => "success",
            "message" => "Course successfully updated",
            "data" => $course
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $course = Course::where('slug', $slug)->first();

        if (!$course) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $course->delete();
    }

    public function registerCourse($course_slug, Request $request)
    {
        $user = $request->user();

        $course = Course::where('slug', $course_slug)->first();

        if (!$course) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $enrollment = $user->enrollments()->create([
            "course_id" => $course->id
        ]);

        return response()->json([
            "status" => "success",
            "message" => "Success registered user course"
        ], 201);
    }
}
