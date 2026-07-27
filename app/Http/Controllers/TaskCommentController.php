<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskCommentRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskCommentController extends Controller
{
    public function store(StoreTaskCommentRequest $request, Task $task): JsonResponse
    {
        $comment = $task->comments()->create($request->validated() + [
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'comment' => $comment->load('author:id,name'),
        ], 201);
    }
}
