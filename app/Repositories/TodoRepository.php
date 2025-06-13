<?php

namespace App\Repositories;

use App\Enums\TodoPriority;
use App\Enums\TodoStatus;
use App\Models\Todo;

class TodoRepository {
    function export($request) {
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

        return [
            'data'  => $data,
            'total' => $data->count(),
            'total_time_tracked' => $data->sum('time_tracked'),
        ];
    }

    public function getStatusSummary()
    {
        $data = [];
        // Init all statuses with 0 value
        foreach (TodoStatus::cases() as $item) {
            $data[$item->value] = 0;
        }

        $todo = Todo::selectRaw('count(*) as total, status')
            ->groupBy('status')
            ->get();

        // Assign status with total value
        foreach ($todo as $item) {
            $data[$item->status] = $item->total;
        }

        return $data;
    }

    public function getPrioritySummary()
    {
        $data = [];
        // Init all priorities with 0 value
        foreach (TodoPriority::cases() as $item) {
            $data[$item->value] = 0;
        }

        $todo = Todo::selectRaw('count(*) as total, priority')
            ->groupBy('priority')
            ->get();

        // Assign priority with total value
        foreach ($todo as $item) {
            $data[$item->priority] = $item->total;
        }

        return $data;
    }

    public function getAssigneeSummary()
    {
        $todo = Todo::selectRaw("
            assignee,
            COUNT(*) as total_todos,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as total_pending_todos,
            SUM(CASE WHEN status = 'completed' THEN time_tracked ELSE 0 END) as total_timetracked_completed_todos
        ")
        ->groupBy('assignee')
        ->get();

        $data = [];
        foreach ($todo as $item) {
            $data[$item->assignee] = [
                'total_todos' => (int) $item->total_todos,
                'total_pending_todos' => (int) $item->total_pending_todos,
                'total_timetracked_completed_todos' => (int) $item->total_timetracked_completed_todos,
            ];
        }

        return $data;
    }
}
