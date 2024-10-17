<?php

namespace App\Services;

use Exception;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Attachment; 
// use CodingPartners\AutoController\Traits\ApiResponseTrait;
// use CodingPartners\AutoController\Traits\FileStorageTrait;
use App\Models\TaskStatusUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    // use ApiResponseTrait, FileStorageTrait;

    /**
     * list all Tasks information
     */
    public function listTask(int $perPage)
    {
        try {
            $Task=Cache::remember('Task', 3600, function () {
                return Task::all();
            });
            return $Task;
        } catch (Exception $e) {
            Log::error('Error Listing Task ' . $e->getMessage());
            return response()->json( 'error listing Task',500);
        }
    }

    /**
     * Create a new Task.
     * @param array $fieldInputs
     * @return \App\Models\Task
     */
    public function createTask(array $fieldInputs)
    {
        try {
            DB::beginTransaction();

            $Task = Task::create([
                'title'  => $fieldInputs["title"],
                'description' => $fieldInputs["description"],
                'type'       => $fieldInputs["type"],
                'status' => $fieldInputs["status"],
                'priority'    => $fieldInputs["priority"],
                'due_date'    => $fieldInputs["due_date"],
                'assigned_to'    => $fieldInputs["assigned_to"],
            ]);
            if (isset($fieldInputs['dependencies'])) {
                $Task->dependencies()->attach($fieldInputs['dependencies']);
            }
            $this->updateDependentTasks($Task);
            DB::commit();
            Cache::forget('Task');
            return $Task;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating Task: ' . $e->getMessage());      
            return response()->json([
                'message' => 'There was an error creating the task',
                'error' => $e->getMessage()
            ], 500);
        }  }
    


    /**
     * Update a specific Task.
     *
     * @param array $fieldInputs
     * @param Task $Task
     * @return \App\Models\Task
     */
    public function updateTask(array $fieldInputs, $Task)
    {
        try {
            DB::beginTransaction();
            $Task->update(array_filter($fieldInputs));
            DB::commit();
            return $Task;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating Task: ' . $e->getMessage());
            return response()->json( 'error updating Task',500);
        }
    }

    /**
     * Delete a specific Task.
     *
     * @param Task $Task
     * @return void
     */
    public function deleteTask($Task)
    {
        try {
            $Task->delete();
        } catch (Exception $e) {
            Log::error('Error deleting Task ' . $e->getMessage());
            return response()->json( 'error deleting Task',500);
        }
    }



/**
     * Change the status of a task and update the user's last activity.
     *
     * This method updates the specified task with the provided data. It also updates
     * the authenticated user's last activity in the pivot table of the related project.
     * If an error occurs during the process, it logs the error and rolls back the transaction.
     *
     * @param array $fieldInputs The validated input data for updating the task.
     * @param Task $Task The task whose status will be changed.
     * @return Task The updated task instance.
     * @throws HttpResponseException If an error occurs during the update process.
     */
    public function changeStatusTask(Task $task, string $newStatus)
    {
        $oldStatus = $task->status;
        $task->status = $newStatus;
        $task->save();

        TaskStatusUpdate::create([
            'task_id' => $task->id,
            'old_status' => $oldStatus,
            'new_status' => $task->status,
            'updated_by' => Auth::id(),
        ]);
        if ($task->status === 'Completed') {
            $this->updateDependentTasks($task);
        }
    

        return $task;
    }
    public function reassignTask(array $fieldInputs,Task $Task)
    {
        try {
            DB::beginTransaction();
            $Task->update($fieldInputs);
            DB::commit();
            return $Task;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error changing reassign Task: ' . $e->getMessage());
            return response()->json( 'error reassign task');
        }
    }
    public function assingedToTask(array $fieldInputs,Task $Task)
    {
        try {
            DB::beginTransaction();
            $Task->update($fieldInputs);
            DB::commit();
            return $Task;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error changing reassign Task: ' . $e->getMessage());
            return response()->json( 'error assign task');
        }
    }
    

    /**
     * Restore a trashed (soft deleted) resource by its ID.
     *
     * @param  int  $id  The ID of the trashed Task to be restored.
     * @return \App\Models\Task
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the Task with the given ID is not found.
     * @throws \Exception If there is an error during the restore process.
     */
    public function restoreTask($id)
    {
        try {
            $Task = Task::onlyTrashed()->findOrFail($id);
            $Task->restore();
            return $Task;
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new Exception('Task not found.');
        } catch (Exception $e) {
            Log::error('Error restoring Task: ' . $e->getMessage());
            return response()->json( 'error restoring Task', 500);
        }
    }

    /**
     * Permanently delete a trashed (soft deleted) resource by its ID.
     *
     * @param  int  $id  The ID of the trashed Task to be permanently deleted.
     * @return void
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the Task with the given ID is not found.
     * @throws \Exception If there is an error during the force delete process.
     */
    public function forceDeleteTask($id)
    {
        try {
            $Task = Task::onlyTrashed()->findOrFail($id);

            $Task->forceDelete();
        } catch (ModelNotFoundException $e) {
            Log::error('Task not found: ' . $e->getMessage());
            throw new Exception('Task not found.');
        } catch (Exception $e) {
            Log::error('Error force deleting Task ' . $e->getMessage());
            return response()->json( 'error deleting Task');
        }
    }

    

    /**
     * Add or update notes for a task and update the user's last activity.
     *
     * This method updates the specified task with the provided notes. It also updates
     * the authenticated user's last activity in the pivot table of the related project.
     * If an error occurs during the process, it logs the error and rolls back the transaction.
     *
     * @param array $fieldInputs The validated input data for updating the task notes.
     * @param Task $Task The task to which notes will be added or updated.
     * @return Task The updated task instance.
     * @throws HttpResponseException If an error occurs during the update process.
     */
    public function addCommentToTask(array $validatedData, Task $task)
    {  try {
        $comment = new Comment([
            'body' => $validatedData['body'],
            'user_id' => Auth::id(),
        ]);
        $task->comments()->save($comment);
        return $comment;
    }
    catch (Exception $e) {
        Log::error('Error force deleting Task ' . $e->getMessage());
        return response()->json( 'error add comment');
    }
    }

    /**
     *  
     * @param array $fieldInputs The validated input data for updating the task notes.
     * @param file $file The file
     * @param Task $Task The task
     * @return Task The updated task instance.
     * 
     */


    public function addAttachmentToTask($file, Task $task)
    {
        try{
        $path = $file->store('attachments');
        $attachment = new Attachment([
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'user_id' => Auth::id(),
        ]);
        $task->attachments()->save($attachment);
        return $attachment;
    }Catch(Exception $e){
        Log::error('Error force deleting Task ' . $e->getMessage());
        return response()->json( 'attachments error');
    }

    }
/**
 * 
 * @param array $priority,$status,$type, $assigned_to, $due_date
 * @return Task The filtet task instance.
 */
    public function getAllTAsks($priority,$status,$type, $assigned_to, $due_date){
        try {
            return Task::filter($priority,$status,$type,$assigned_to,$due_date)->get();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json( 'Something went wrong with fetche tasks');

        }
    }

    /**
     * @return task blocked
     * 
     */

    public function getBlockedTasks()
    {
        try {
        return Task::where('status', 'Blocked')
            ->whereHas('dependencies', function ($query) {
                $query->where('status', '!=', 'Completed'); 
            })
            ->get();
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json( 'no blocked task');


    }
    }


public function updateDependentTasks(Task $completedTask)
{
    $dependentTasks = Task::whereHas('dependencies', function ($query) use ($completedTask) {
        $query->where('dependent_task_id', $completedTask->id);
    })->get();
    foreach ($dependentTasks as $task) {
        $hasPendingDependencies = $task->dependencies()
            ->where('status', '!=', 'Completed')
            ->exists();

        if (!$hasPendingDependencies) {
            $task->status = 'Open'; 
            $task->save();  
        }
    }
}

    
}
