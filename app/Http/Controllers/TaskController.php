<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Services\assetsService;
use App\Models\TaskStatusUpdate;
use App\Http\Controllers\Controller;
use App\Http\Trait\ApiResponseTrait;
// use App\Http\Requests\TaskRequest\assingedRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\assingedRequest;
use App\Http\Requests\attachmentRequest;
use App\Http\Requests\TaskRequest\AddNotesRequest;
use App\Http\Requests\TaskRequest\reassignRequest;
use App\Http\Requests\TaskRequest\StoreTaskRequest;
use App\Http\Requests\TaskRequest\UpdateTaskRequest;
use App\Http\Requests\TaskRequest\ChangeStatusTaskRequest;

class TaskController extends Controller
{
      /**
     * @var TaskService
     */
    protected $TaskService;
    use ApiResponseTrait;


    /**
     *  TaskController constructor
     * @param TaskService $TaskService
     */
    public function __construct(TaskService $TaskService,assetsService $assetsService)
    {
        $this->TaskService = $TaskService;
        $this->assetsService = $assetsService;
        $this->middleware('permission:view_tasks')->only(['index', 'show']);
        $this->middleware('permission:create_tasks')->only(['create', 'store']);
        $this->middleware('permission:edit_tasks')->only(['edit', 'update']);
        $this->middleware('permission:delete_tasks')->only('destroy');
        $this->middleware('permission:assign_tasks')->only('assingedUser');
        $this->middleware('permission:resign_tasks')->only('reassignUser');
        $this->middleware('permission:softdelete_tasks')->only('forceDelete');
    }
   
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Default to 10 if not provided
        $Tasks = $this->TaskService->listTask($perPage);
        return $this->success_Response($Task, "All tasks fetched successfully", 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $fieldInputs = $request->validated();
        $Task        = $this->TaskService->createTask($fieldInputs);
        return $this->success_Response($Task, "Task created successfully.", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $task = Cache::remember('Task' . $id, 150, function () use ($id) {
            $tasks = Task::with(['comments', 'attachments', 'statusUpdates', 'user', 'dependencies', 'dependents'])
                ->findOrFail($id);
            
            return $tasks;
        });
    
        return $this->success_Response($task, "task viewed successfully", 200);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $Task)
    {
        $fieldInputs = $request->validated();
        $Task    = $this->TaskService->updateTask($fieldInputs, $Task);
        return $this->success_Response($Task, "task updated successfully", 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $Task)
    {
        $this->TaskService->deleteTask($Task);
        Cache::forget('Task');

        return $this->success_Response(null, "task soft deleted successfully", 200);
    }
    
   
  
/**
     * Restore a trashed (soft deleted) resource by its ID.
     */
    public function restore($id)
    {
        $Task = $this->TaskService->restoreTask($id);
        Cache::forget('Task');

        return $this->success_Response(null, "task restored successfully", 200);
    }

    /**
     * Permanently delete a trashed (soft deleted) resource by its ID.
     */
    public function forceDelete($id)
    {
        $this->TaskService->forceDeleteTask($id);
        return $this->success_Response(null, "task force deleted successfully", 200);
    }
    
    /**
     * Change the status of a specific task.
     *
     * This method validates the incoming request data using
     * the ChangeStatusTaskRequest. It then calls the TaskService to
     * update the status of the specified task. A successful response
     * is returned with the updated task resource.
     *
     * @param ChangeStatusTaskRequest $request The validated request containing the new status for the task.
     * @param Task $Task The task whose status will be updated.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating success with the updated task resource.
     */
    public function changeStatus(ChangeStatusTaskRequest $request, $id)
    {
        $task = Task::findOrFail($id);
        $task = $this->TaskService->changeStatusTask($task, $request->validated()['status']);
        if ($task->status == 'Completed') {
            $this->TaskService->updateDependentTasks($task);
        }
        return response()->json($task);
    }


   /** 
    * @param reassignRequest $request The validated request containing the new status for the task.
    * @param Task $Task The task whose status will be updated.
    * @return $Task .

*/
    public function reassignUser(reassignRequest $request, Task $Task)
    {
        $fieldInputs = $request->validated();
        $Task        = $this->TaskService->reassignTask($fieldInputs, $Task);
        return response()->json($Task);
    }

    /** 
    * @param assingedRequest $request The validated request containing the new status for the task.
    * @param Task $Task The task whose status will be updated.
    * @return $Task .

*/
    public function assingedUser(assingedRequest $request, Task $Task)
    {
        $fieldInputs = $request->validated();
        $Task        = $this->TaskService->assingedToTask($fieldInputs, $Task);
        return response()->json($Task);
    }


  /** 
    * @param attachmentRequest $request The validated request containing the new status for the task.
    * @param id $Task The task whose status will be updated.
    * @return $attachment .

*/
    // public function addAttachment(attachmentRequest $request, $id)
    // {
    //     $task = Task::findOrFail($id);
    //     $attachment = $this->TaskService->addAttachmentToTask($request->file('file'), $task);
    //     return response()->json($attachment, 201);
    // }

    public function storeImage(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'No file uploaded'], 400);
        }
    
        try {
            $image = $this->assetsService->storeImage($request);
            return response()->json(['message' => 'Image uploaded successfully', 'data' => $image], 201);
        } catch (\Exception $e) {
            // Log the error message for debugging
            \Log::error('Error uploading image: ' . $e->getMessage());
    
            // Return detailed error message for debugging (in development)
            return response()->json(['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

 /** 
    * @param AddNotesRequest $request The validated request containing the new status for the task.
    * @param id $Task The task whose status will be updated.
    * @return $comment .

*/

    public function addComment(AddNotesRequest $request, $id)
    {
        $task = Task::findOrFail($id);
        $comment = $this->TaskService->addCommentToTask($request->validated(), $task);
        return response()->json($comment, 201);
    }


 /** 
    * @param Request $request The validated request containing the new status for the task.
    * @return $tasks .

*/
    public function filter(Request $request)
    {  
        $tasks = $this->TaskService->getAllTAsks($request->input('priority'),$request->input('status'),$request->input('type'),$request->input('assigned_to'),$request->input('due_date'));
        return $tasks;
    }

 /** 
    * @param Request $request The validated request containing the new status for the task.
    * @return $tasks .

*/
    public function getBlockedTasks(Request $request)
    {
        $tasks = $this->TaskService->getBlockedTasks(); 
        return response()->json($tasks);
    }
    
  
}