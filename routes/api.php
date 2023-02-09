<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SpecialityController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\ScheduleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth'], function ($router) {
    Route::get('url', [AuthController::class, 'getAzureAuthorizationUrl']);
    Route::post('login_kpi', [AuthController::class, 'loginViaCabinet']);
    Route::group(['middleware' => 'login'], function ($router) {
        Route::get('callback', [AuthController::class, 'azureCallback']);
        Route::post('login', [AuthController::class, 'loginViaAzure']);
    });
    Route::group(['middleware' => 'auth:api'], function ($router) {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

Route::group(['prefix' => 'users'], function ($router) {
    Route::get('/{user}/profile', [UserController::class, 'getProfile']);
    Route::get('/{user}/photo', [UserController::class, 'getPhoto']);
    Route::get('/{user}/record-book/{semester}', [UserController::class, 'getRecordBook']);
    Route::get('/{user}/rating/{semester}', [UserController::class, 'getRating']);
    Route::get('/{user}/syllabus/{semester}', [UserController::class, 'getSyllabus']);
    Route::post('/{user}/avatar', [UserController::class, 'uploadAvatar']);
    Route::post('/{user}/cover', [UserController::class, 'uploadCover']);
});

Route::group(['prefix' => 'groups'], function ($router) {
    Route::get('/{group}/schedules', [GroupController::class, 'getSchedules']);
    Route::get('/{group}/users', [UserController::class, 'getGroupUsers']);
});

Route::group(['prefix' => 'specializations'], function ($router) {
    Route::get('/{specialization}/users', [UserController::class, 'getSpecializationUsers']);
    Route::get('/{specialization}/groups', [GroupController::class, 'getSpecializationGroups']);
});

Route::group(['prefix' => 'departments'], function ($router) {
    Route::get('/{department}/users', [UserController::class, 'getDepartmentUsers']);
    Route::get('/{department}/groups', [GroupController::class, 'getDepartmentGroups']);
    Route::get('/{department}/specializations', [SpecializationController::class, 'getDepartmentSpecializations']);
});

Route::group(['prefix' => 'programs'], function ($router) {
    Route::get('/{program}/users', [UserController::class, 'getProgramUsers']);
    Route::get('/{program}/groups', [GroupController::class, 'getProgramGroups']);
    Route::get('/{program}/specializations', [SpecializationController::class, 'getProgramSpecializations']);
    Route::get('/{program}/departments', [DepartmentController::class, 'getProgramDepartments']);
});

Route::group(['prefix' => 'specialities'], function ($router) {
    Route::get('/{speciality}/users', [UserController::class, 'getSpecialityUsers']);
    Route::get('/{speciality}/groups', [GroupController::class, 'getSpecialityGroups']);
    Route::get('/{speciality}/specializations', [SpecializationController::class, 'getSpecialitySpecializations']);
    Route::get('/{speciality}/departments', [DepartmentController::class, 'getSpecialityDepartments']);
    Route::get('/{speciality}/programs', [ProgramController::class, 'getSpecialityPrograms']);
});

Route::group(['prefix' => 'faculties'], function ($router) {
    Route::get('/{faculty}/users', [UserController::class, 'getFacultyUsers']);
    Route::get('/{faculty}/groups', [GroupController::class, 'getFacultyGroups']);
    Route::get('/{faculty}/specializations', [SpecializationController::class, 'getFacultySpecializations']);
    Route::get('/{faculty}/departments', [DepartmentController::class, 'getFacultyDepartments']);
    Route::get('/{faculty}/programs', [ProgramController::class, 'getFacultyPrograms']);
    Route::get('/{faculty}/specialities', [SpecialityController::class, 'getFacultySpecialities']);
});

Route::apiResource('users', UserController::class);
Route::apiResource('groups', GroupController::class);
Route::apiResource('schedules', ScheduleController::class);
Route::apiResource('specializations', SpecializationController::class);
Route::apiResource('departments', DepartmentController::class);
Route::apiResource('programs', ProgramController::class);
Route::apiResource('specialities', SpecialityController::class);
Route::apiResource('faculties', FacultyController::class);
