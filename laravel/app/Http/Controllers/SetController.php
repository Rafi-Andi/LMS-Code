<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class SetController extends Controller
{
    public function store(Request $request, $course)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid field(s) in reques",
                "errors" => $validator->errors()
            ], 422);
        }


        $course = Course::where('slug', $course)->first();

        if (!$course) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }
        $newOrder = $course->sets()->orderBy('created_at', 'asc')->pluck('order')->last() + 1;

        $validated = $validator->validated();
        $set = $course->sets()->create(["name" => $validated['name'], "order" => $newOrder]);
        return response()->json([
            "status" => "success",
            "message" => "Set successfully added",
            "data" => $set
        ], 201);
    }

    public function destroy($courseSlug, $setId)
    {
        $course = Course::where('slug', $courseSlug)->first();

        if (!$course) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $set = $course->sets()->find($setId);

        if (!$set) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $set->delete();

        return response()->json([
            "status" => "success",
            "message" => "Set successfully deleted"
        ], 200);
    }
}
