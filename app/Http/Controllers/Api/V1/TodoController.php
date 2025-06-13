<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TodoStatus;
use App\Exports\TodoExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TodoExportRequest;
use App\Http\Requests\Api\V1\TodoRequest;
use App\Models\Todo;
use Maatwebsite\Excel\Facades\Excel;
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

    public function export(TodoExportRequest $request)
    {
        $todo = Todo::select([
            'title',
            'assignee',
            'due_date',
            'time_tracked',
            'status',
            'priority',
        ]);

        // Filter
        if ($request->title) {
            $todo->where('title', 'like', "%$request->title%");
        }
        if ($request->assignee) {
            $assignees = explode(',', $request->assignee);
            $todo->whereIn('assignee', $assignees);
        }
        if ($request->start && $request->end) {
            $todo->whereBetween('due_date', [$request->start, $request->end]);
        }
        if (isset($request->min) && isset($request->max)) {
            $todo->whereBetween('time_tracked', [$request->min, $request->max]);
        }
        if ($request->status) {
            $statuses = explode(',', $request->status);
            $todo->whereIn('status', $statuses);
        }
        if ($request->priority) {
            $priorities = explode(',', $request->priority);
            $todo->whereIn('priority', $priorities);
        }

        $data = $todo->get();
        $total = $data->count();
        $totalTimeTracked = $data->sum('time_tracked');

        return Excel::download(new TodoExport($data, $total, $totalTimeTracked), 'todos.xlsx');
    }
}
