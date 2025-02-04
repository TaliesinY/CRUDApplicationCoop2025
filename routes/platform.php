<?php

declare(strict_types=1);

use App\Orchid\Screens\CoursesScreen;
use App\Orchid\Screens\HomeScreen;
use App\Orchid\Screens\CourseDetailsScreen;
use App\Orchid\Screens\AssignmentEditScreen;
use App\Orchid\Screens\MaterialEditScreen;
use App\Orchid\Screens\CourseCreateScreen;
use App\Orchid\Screens\CourseEditScreen;
use App\Orchid\Screens\AssignmentDetailsScreen;
use App\Orchid\Screens\MaterialDetailsScreen;
use App\Orchid\Screens\Role\RoleEditScreen;
use App\Orchid\Screens\Role\RoleListScreen;
use App\Orchid\Screens\User\UserEditScreen;
use App\Orchid\Screens\User\UserListScreen;
use App\Orchid\Screens\User\UserProfileScreen;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AIController;
use Tabuna\Breadcrumbs\Trail;

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the need "dashboard" middleware group. Now create something great!
|
*/

// Main
Route::screen('/main', HomeScreen::class)
    ->name('platform.main');

// Courses
Route::screen('/courses', CoursesScreen::class)
    ->name('platform.courses')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Courses'), route('platform.courses')));

// Create Course
Route::screen('/courses/create', CourseCreateScreen::class)
    ->name('platform.course.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.courses')
        ->push(__('Create'), route('platform.course.create')));

// Post Announcement
Route::post('/courses/{course}/postAnnouncement', [CourseDetailsScreen::class, 'postAnnouncement'])
    ->name('platform.course.postAnnouncement');


// Course Details
Route::screen('/courses/{course}', CourseDetailsScreen::class)
    ->name('platform.course.details')
    ->breadcrumbs(fn (Trail $trail, $course) => $trail
        ->parent('platform.courses')
        ->push($course->name, route('platform.course.details', $course)));

// Edit Assignment
Route::screen('/courses/{course}/assignments/{index}/edit', AssignmentEditScreen::class)
        ->name('platform.assignment.edit');

// Edit Material
Route::screen('/courses/{course}/materials/{index}/edit', MaterialEditScreen::class)
    ->name('platform.material.edit');

// Edit Course
Route::screen('/courses/{course}/edit', CourseEditScreen::class)
        ->name('platform.course.edit');

// Assignment Details
Route::screen('course/{course}/assignment/{index}', AssignmentDetailsScreen::class)
    ->name('platform.assignment.details');

// Material Details
Route::screen('course/{course}/material/{index}', MaterialDetailsScreen::class)
    ->name('platform.material.details');

// Course Details (Deleting Material POST)
Route::post('/courses/{course}/deleteMaterial', [\App\Orchid\Screens\CourseDetailsScreen::class, 'deleteMaterial'])
->name('platform.material.delete');

// Course Details (Deleting Assignment POST)
Route::post('/courses/{course}/deleteAssignment', [\App\Orchid\Screens\CourseDetailsScreen::class, 'deleteAssignment'])
->name('platform.assignment.delete');


// Platform > Profile
Route::screen('profile', UserProfileScreen::class)
    ->name('platform.profile')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Profile'), route('platform.profile')));

// Platform > System > Users > User
Route::screen('users/{user}/edit', UserEditScreen::class)
    ->name('platform.systems.users.edit')
    ->breadcrumbs(fn (Trail $trail, $user) => $trail
        ->parent('platform.systems.users')
        ->push($user->name, route('platform.systems.users.edit', $user)));

// Platform > System > Users > Create
Route::screen('users/create', UserEditScreen::class)
    ->name('platform.systems.users.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.users')
        ->push(__('Create'), route('platform.systems.users.create')));

// Platform > System > Users
Route::screen('users', UserListScreen::class)
    ->name('platform.systems.users')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Users'), route('platform.systems.users')));

// Platform > System > Roles > Role
Route::screen('roles/{role}/edit', RoleEditScreen::class)
    ->name('platform.systems.roles.edit')
    ->breadcrumbs(fn (Trail $trail, $role) => $trail
        ->parent('platform.systems.roles')
        ->push($role->name, route('platform.systems.roles.edit', $role)));

// Platform > System > Roles > Create
Route::screen('roles/create', RoleEditScreen::class)
    ->name('platform.systems.roles.create')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.systems.roles')
        ->push(__('Create'), route('platform.systems.roles.create')));

// Platform > System > Roles
Route::screen('roles', RoleListScreen::class)
    ->name('platform.systems.roles')
    ->breadcrumbs(fn (Trail $trail) => $trail
        ->parent('platform.index')
        ->push(__('Roles'), route('platform.systems.roles')));

