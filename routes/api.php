<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Middleware\SecurityMiddleware;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\TaskReportController;
use App\Http\Controllers\Admin\PermissionsController;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
   ], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
   });
   Route::middleware(['auth:api',SecurityMiddleware::class])->group(function () {
   
       Route::get('/users', [UserController::class, 'index']); 
       Route::post('/users', [UserController::class, 'store']); 
       Route::put('/users/{id}', [UserController::class, 'update']); 
       Route::delete('/users/{id}', [UserController::class, 'destroy']); 
   
       // مسارات الأدوار
       Route::get('/roles', [RoleController::class, 'index']); 
       Route::post('/roles', [RoleController::class, 'store']);  
       Route::post('/roles/{id}', [RoleController::class, 'assignPermissions']); 
       Route::delete('/roles/{id}', [RoleController::class, 'destroy']); 
       Route::get('/roles/{id}', [RoleController::class, 'show']); 
   
       // مسارات الصلاحيات
       Route::get('/permissions', [PermissionsController::class, 'index']);  
       Route::post('/permissions', [PermissionsController::class, 'store']); 
       Route::get('/permissions/{id}', [PermissionsController::class, 'show']); 
       Route::put('/permissions/{id}', [PermissionsController::class, 'update']); 
       Route::delete('/permissions/{id}', [PermissionsController::class, 'delete']); 

       //  مسارات المهام
       Route::apiResource('Tasks', TaskController::class);
       Route::put('tasks/change-status-task/{Task}', [TaskController::class, 'changeStatus']);
       Route::put('tasks/change-reassign-task/{Task}', [TaskController::class, 'reassignUser']);
       Route::put('tasks/change-assinged-task/{Task}', [TaskController::class, 'assingedUser']);
       Route::post('/tasks/{id}/attachments', [TaskController::class, 'storeImage']);
       Route::post('/tasks/{id}/comments', [TaskController::class, 'addComment']);
       Route::get('/tasks/filter', [TaskController::class, 'filter']);
       Route::get('/tasks-blocked', [TaskController::class, 'getBlockedTasks']);
       Route::get('/tasks/report', [TaskReportController::class, 'Report']);
       Route::get('Tasks/trashed', [TaskController::class, 'trashed']);
       Route::post('Tasks/{id}/restore', [TaskController::class, 'restore']);
       Route::delete('Tasks/{id}/forceDelete', [TaskController::class, 'forceDelete']);





    });
