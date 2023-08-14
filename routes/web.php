<?php

use Illuminate\Support\Facades\Route;

// Admin Controllers
use App\Http\Controllers\Joystick\AdminController;
use App\Http\Controllers\Joystick\PageController;
use App\Http\Controllers\Joystick\PostController;
use App\Http\Controllers\Joystick\SectionController;
use App\Http\Controllers\Joystick\AppController;

use App\Http\Controllers\Joystick\ModeController;
use App\Http\Controllers\Joystick\CompanyController;
use App\Http\Controllers\Joystick\RegionController;
use App\Http\Controllers\Joystick\UserController;
use App\Http\Controllers\Joystick\RoleController;
use App\Http\Controllers\Joystick\PermissionController;
use App\Http\Controllers\Joystick\LanguageController;

// Site Controllers
use App\Http\Controllers\InputController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController as BlogController;
use App\Http\Controllers\PageController as SiteController;

// Cargo Controllers
use App\Http\Controllers\Cargo\TrackController;
use App\Http\Controllers\Cargo\StatusController;
use App\Http\Controllers\Cargo\TrackExtensionController;

use App\Http\Livewire\Client\Index as Client;
use App\Http\Livewire\Client\Archive;
use App\Http\Livewire\Storage\Reception;
use App\Http\Livewire\Storage\Sending;
use App\Http\Livewire\Storage\Sorting;
use App\Http\Livewire\Storage\Arrival;
use App\Http\Livewire\Storage\Giving;
use App\Http\Livewire\Storage\Tracks;

// Client Livewire Routes
Route::redirect('client', '/'.app()->getLocale().'/client');
Route::group(['prefix' => '/{lang}/client', 'middleware' => ['auth']], function () {
    Route::get('/', Client::class);
    Route::get('tracks', Client::class);
    Route::get('archive', Archive::class);
});

// Storage Livewire Routes
Route::redirect('storage', '/'.app()->getLocale().'/storage');
Route::group(['prefix' => '/{lang}/storage', 'middleware' => ['auth', 'roles:admin|storekeeper-first|storekeeper-sorter|storekeeper-last']], function () {
    Route::get('/', Reception::class);
    Route::get('reception', Reception::class);
    Route::get('sending', Sending::class);
    Route::get('sorting', Sorting::class);
    Route::get('arrival', Arrival::class);
    Route::get('giving', Giving::class);
    Route::get('tracks', Tracks::class);
});

// Joystick Administration
Route::redirect('admin', '/'.app()->getLocale().'/admin');
Route::group(['prefix' => '{lang}/admin', 'middleware' => ['auth', 'roles:admin|manager|partner']], function () {

    Route::get('/', [AdminController::class, 'index']);
    Route::get('filemanager', [AdminController::class, 'filemanager']);
    Route::get('frame-filemanager', [AdminController::class, 'frameFilemanager']);

    Route::resources([
        // Cargo
        'tracks' => TrackController::class,
        'statuses' => StatusController::class,

        // Content
        'pages' => PageController::class,
        'posts' => PostController::class,
        'sections' => SectionController::class,
        'modes' => ModeController::class,
        'apps' => AppController::class,

        // Resources
        'companies' => CompanyController::class,
        'regions' => RegionController::class,
        'users' => UserController::class,
        'roles' => RoleController::class,
        'permissions' => PermissionController::class,
        'languages' => LanguageController::class,
    ]);

    Route::get('reception-tracks', [TrackExtensionController::class, 'receptionTracks']);
    Route::get('arrival-tracks', [TrackExtensionController::class, 'arrivalTracks']);
    Route::post('upload-tracks', [TrackExtensionController::class, 'uploadTracks']);

    Route::get('companies-actions', [CompanyController::class, 'actionCompanies']);
    Route::get('users/search/user', [UserController::class, 'search']);
    // Route::get('users/search-ajax', [UserController::class, 'searchAjax']);
    Route::get('users/password/{id}/edit', [UserController::class, 'passwordEdit']);
    Route::put('users/password/{id}', [UserController::class, 'passwordUpdate']);
});

// Input Actions
Route::get('search', [InputController::class, 'search']);
Route::get('search-track', [InputController::class, 'searchTrack']);
Route::get('search-ajax', [InputController::class, 'searchAjax']);
Route::post('send-app', [InputController::class, 'sendApp']);

// User Profile
Route::group(['prefix' => '{lang}', 'middleware' => 'auth'], function() {

    Route::get('profile', [ProfileController::class, 'profile']);
    Route::get('profile/edit', [ProfileController::class, 'editProfile']);
    Route::put('profile', [ProfileController::class, 'updateProfile']);
    Route::get('profile/password/edit', [ProfileController::class, 'passwordEdit']);
    Route::put('profile/password', [ProfileController::class, 'passwordUpdate']);
});

// News
Route::get('i/news', [BlogController::class, 'posts']);
Route::get('i/news/{page}', [BlogController::class, 'postSingle']);

// Pages
Route::get('i/contacts', [SiteController::class, 'contacts']);
Route::get('i/{page}', [SiteController::class, 'page']);
Route::get('/', [SiteController::class, 'index']);

require __DIR__.'/auth.php';
