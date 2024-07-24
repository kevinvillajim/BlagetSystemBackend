<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $progress = Progress::all(); // Get all progress records
        return response()->json($progress);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id', // Validate user existence
            'course_id' => 'required|integer',
            'unit_id' => 'nullable|integer',
            'progress' => 'required|numeric|between:0,1', // Validate progress within 0-1 range
            'completed' => 'required|boolean',
            'finishDate' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $progress = Progress::create($request->all());
        return response()->json($progress, 201); // Created response with resource
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $progress = Progress::find($id);

        if (!$progress) {
            return response()->json(['message' => 'Progress not found'], 404);
        }

        return response()->json($progress);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $progress = Progress::find($id);


        if (!$progress) {
            return response()->json(['message' => 'Progress not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'course_id' => 'required|integer',
            'unit_id' => 'nullable|integer',
            'progress' => 'required|numeric|between:0,1',
            'completed' => 'required|boolean',
            'finishDate' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $progress->update($request->all());
        return response()->json($progress);
    }
    public function destroy($id, $course)
    {
        $progress = Progress::where('user_id', $id)->where('course_id', $course)->get();

        if ($progress->isEmpty()) {
            return response()->json(['message' => 'Progress not found'], 404);
        }

        foreach ($progress as $entry) {
            $entry->delete();
        }

        return response()->json(['message' => 'Progress deleted successfully']);
    }

    public function upsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'course_id' => 'required|integer',
            'unit_id' => 'nullable|integer',
            'progress' => 'required|numeric|between:0,1',
            'completed' => 'required|boolean',
            'finishDate' => 'nullable|date',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $progress = Progress::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'course_id' => $request->course_id,
                'unit_id' => $request->unit_id
            ],
            [
                'progress' => $request->progress,
                'completed' => $request->completed, 'finishDate' => $request->finishDate
            ]
        );

        return response()->json($progress, 200); // OK response with resource
    }
    public function getUserProgress(Request $request)
    {
        $userId = $request->user()->id;

        $progressData = Progress::where('user_id', $userId)->get();

        return response()->json($progressData);
    }
}