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
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ScheduleSubjectsController;
use App\Http\Controllers\AttendanceLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FaqItemController;

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
    $router->get('url', [AuthController::class, 'getAzureAuthorizationUrl']);
    $router->post('login_kpi', [AuthController::class, 'loginViaCabinet']);
    $router->group(['middleware' => 'login'], function ($router) {
        $router->get('callback', [AuthController::class, 'azureCallback']);
        $router->post('login', [AuthController::class, 'loginViaAzure']);
    });
    $router->group(['middleware' => 'auth:api'], function ($router) {
        $router->post('logout', [AuthController::class, 'logout']);
        $router->post('refresh', [AuthController::class, 'refresh']);
    });
});

Route::group(['middleware' => 'auth:api'], function ($router) {
    $router->group(['prefix' => 'users'], function ($router) {
        $router->get('/{user}/profile', [UserController::class, 'getProfile']);
        $router->get('/{user}/record-book/{semester}', [UserController::class, 'getRecordBook']);
        $router->get('/{user}/rating/{semester}', [UserController::class, 'getRating']);
        $router->get('/{user}/syllabus/{semester}', [UserController::class, 'getSyllabus']);
        $router->get('/{user}/azure-schedule', [ScheduleController::class, 'getAzureSchedule']);
        $router->get('/{user}/attendance', [AttendanceLogController::class, 'getUserAttendanceLogs']);
        $router->get('/{user}/notifications', [NotificationController::class, 'getUserNotifications']);
        $router->post('/{user}/photo', [UserController::class, 'syncPhoto']);
        $router->post('/{user}/avatar', [UserController::class, 'uploadAvatar']);
        $router->post('/{user}/cover', [UserController::class, 'uploadCover']);
        $router->post('/{user}/attendance/{scheduleSubject}', [AttendanceLogController::class, 'switchAttendanceLog']);
        $router->post('/{user}/notifications', [NotificationController::class, 'createUserNotification']);
    });

    $router->group(['prefix' => 'groups'], function ($router) {
        $router->get('/{group}/users', [UserController::class, 'getGroupUsers']);
        $router->get('/{group}/schedules', [ScheduleController::class, 'getGroupSchedules']);
        $router->get('/{group}/subjects/{semester}', [SubjectController::class, 'getGroupSubjects']);
        $router->get('/{group}/attendance', [AttendanceLogController::class, 'getGroupAttendanceLogs']);
        $router->post('/{group}/schedules', [ScheduleController::class, 'createGroupSchedule']);
    });

    $router->group(['prefix' => 'schedules'], function ($router) {
        $router->post('/{schedule}/subjects/{subject}', [ScheduleSubjectsController::class, 'createScheduleSubject']);
    });

    $router->group(['prefix' => 'schedule-subjects'], function ($router) {
        $router->get('/{scheduleSubject}/attendance', [AttendanceLogController::class, 'getScheduleSubjectAttendanceLogs']);
    });

    $router->group(['prefix' => 'notifications'], function ($router) {
        $router->post('/{notification}/read', [NotificationController::class, 'markAsReadNotification']);
    });

    $router->group(['prefix' => 'faq-items'], function ($router) {
        $router->get('/{lang}', [FaqItemController::class, 'getFaqsByLang']);
    });

    $router->group(['prefix' => 'specializations'], function ($router) {
        $router->get('/{specialization}/users', [UserController::class, 'getSpecializationUsers']);
        $router->get('/{specialization}/groups', [GroupController::class, 'getSpecializationGroups']);
    });

    $router->group(['prefix' => 'departments'], function ($router) {
        $router->get('/{department}/users', [UserController::class, 'getDepartmentUsers']);
        $router->get('/{department}/groups', [GroupController::class, 'getDepartmentGroups']);
        $router->get('/{department}/specializations', [SpecializationController::class, 'getDepartmentSpecializations']);
    });

    $router->group(['prefix' => 'programs'], function ($router) {
        $router->get('/{program}/users', [UserController::class, 'getProgramUsers']);
        $router->get('/{program}/groups', [GroupController::class, 'getProgramGroups']);
        $router->get('/{program}/specializations', [SpecializationController::class, 'getProgramSpecializations']);
        $router->get('/{program}/departments', [DepartmentController::class, 'getProgramDepartments']);
    });

    $router->group(['prefix' => 'specialities'], function ($router) {
        $router->get('/{speciality}/users', [UserController::class, 'getSpecialityUsers']);
        $router->get('/{speciality}/groups', [GroupController::class, 'getSpecialityGroups']);
        $router->get('/{speciality}/specializations', [SpecializationController::class, 'getSpecialitySpecializations']);
        $router->get('/{speciality}/departments', [DepartmentController::class, 'getSpecialityDepartments']);
        $router->get('/{speciality}/programs', [ProgramController::class, 'getSpecialityPrograms']);
    });

    $router->group(['prefix' => 'faculties'], function ($router) {
        $router->get('/{faculty}/users', [UserController::class, 'getFacultyUsers']);
        $router->get('/{faculty}/groups', [GroupController::class, 'getFacultyGroups']);
        $router->get('/{faculty}/specializations', [SpecializationController::class, 'getFacultySpecializations']);
        $router->get('/{faculty}/departments', [DepartmentController::class, 'getFacultyDepartments']);
        $router->get('/{faculty}/programs', [ProgramController::class, 'getFacultyPrograms']);
        $router->get('/{faculty}/specialities', [SpecialityController::class, 'getFacultySpecialities']);
    });

    $router->apiResource('users', UserController::class);
    $router->apiResource('groups', GroupController::class);
    $router->apiResource('specializations', SpecializationController::class);
    $router->apiResource('departments', DepartmentController::class);
    $router->apiResource('programs', ProgramController::class);
    $router->apiResource('specialities', SpecialityController::class);
    $router->apiResource('faculties', FacultyController::class);
    $router->apiResource('schedules', ScheduleController::class);
    $router->apiResource('subjects', SubjectController::class);
    $router->apiResource('schedule-subjects', ScheduleSubjectsController::class);
    $router->apiResource('attendance-logs', AttendanceLogController::class);
    $router->apiResource('notifications', NotificationController::class);
    $router->apiResource('faq-items', FaqItemController::class);
});
