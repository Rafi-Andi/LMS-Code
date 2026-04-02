<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonContent;
use App\Models\Option;
use App\Models\Set;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
            "set_id" => "required|exists:sets,id",
            "contents" => "required|array",
            "contents.*" => "required",
            "contents.*.type" => "required|in:learn,quiz",
            "contents.*.content" => "required|string",
            "content.*.options" => "required|array"
        ]);

        $validator->after(function ($validator) use ($request) {
            foreach ($request->contents as $i => $content) {
                if ($content['type'] === 'quiz') {
                    if (!isset($content['options'])) {
                        $validator->errors()->add(
                            "contents.$i.options",
                            "Options wajib jika type quiz"
                        );
                    }
                }
            }
        });

        if ($validator->fails()) {
            return response()->json([
                "status" => "error",
                "message" => "invalid fields",
                "errors" => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $set = Set::find($validated['set_id']);

        if (!$set) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }
        $newOrder = $set->lessons()->orderBy('created_at', 'asc')->pluck('order')->last() + 1;

        $lesson = $set->lessons()->create([
            "name" => $validated['name'],
            "order" => $newOrder
        ]);

        foreach ($validated['contents'] as $content) {
            $lessoncontent =  $lesson->contents()->create([
                "type" => $content['type'],
                "content" => $content['content'],
            ]);

            Log::info($content['type']);

            if ($content['type'] == 'quiz') {
                foreach ($content['options'] as $option) {
                    $option = $lessoncontent->options()->create([
                        "option_text" => $option['option_text'],
                        "is_correct" => $option['is_correct'],
                    ]);
                }
            }
        }
        return response()->json([
            "status" => "success",
            "message" => "Success create lessons",
            "data" => $lesson->contents,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lesson = Lesson::find($id);

        if (!$lesson) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource not found"
            ], 404);
        }

        $lesson->delete();

        return response()->json([
            "status" => "success",
            "message" => "Lesson successfully deleted"
        ], 200);
    }

    public function checkAnswer($lesson_id, $content_id, Request $request)
    {
        $lesson = Lesson::find($lesson_id);

        if (!$lesson) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource Lesson not found"
            ], 404);
        }

        $lessoncontent = LessonContent::find($content_id);
        if (!$lessoncontent) {
            return response()->json([
                "status" => "not_found",
                "message" => "Resource Content not found"
            ], 404);
        }

        if ($lessoncontent->type === 'learn') {
            return response()->json([
                "status" => "error",
                "message" => "Only for quiz content"
            ], 422);
        }
        $validated = $request->validate([
            "option_id" => ['required', 'exists:options,id,lesson_content_id' . ',' . $lessoncontent->id]
        ]);

        $option = $lessoncontent->options()->find($validated['option_id']);

        return response()->json([
            "status" => "success",
            "message" => "Check answer success",
            "data" => $option
        ], 200);
    }
}
