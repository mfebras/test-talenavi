<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TodoStatus;
use App\Exports\TodoExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\TodoChartRequest;
use App\Http\Requests\Api\V1\TodoExportRequest;
use App\Http\Requests\Api\V1\TodoRequest;
use App\Models\Todo;
use App\Repositories\TodoRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TodoController extends Controller
{
    public function __construct(protected TodoRepository $repository)
    {}

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
        $export = $this->repository->export($request);

        return Excel::download(
            new TodoExport(
                $export['data'],
                $export['total'],
                $export['total_time_tracked']
            ),
            'todos.xlsx'
        );
    }

    public function chart(TodoChartRequest $request)
    {
        $data =[];

        switch ($request->type) {
            case 'priority':
                $data['priority_summary'] = $this->repository->getPrioritySummary();
                break;

            case 'assignee':
                $data['assignee_summary'] = $this->repository->getAssigneeSummary();
                break;
            
            default:
                $data['status_summary'] = $this->repository->getStatusSummary();
                break;
        }

        return response()->json($data);
    }
}
