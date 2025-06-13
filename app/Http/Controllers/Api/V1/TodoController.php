<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TodoStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TodoRequest;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function store(TodoRequest $request)
    {
        $todo = Todo::create([
            'title'        => $request->title,
            'assignee'     => $request->assignee,
            'due_date'     => $request->due_date,
            'time_tracked' => $request->time_tracked ?? 0,
            'status'       => $request->status ?? TodoStatus::Pending,
            'priority'     => $request->priority,
        ]);

        return response()->json([
            'data' => $todo,
        ]);
    }
}
