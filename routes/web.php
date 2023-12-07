<?php

use Illuminate\Support\Facades\Route;

// Admin Controllers
use App\Http\Controllers\Joystick\AdminController;
use App\Http\Controllers\Joystick\PageController;
use App\Http\Controllers\Joystick\PostController;
use App\Http\Controllers\Joystick\SectionController;
use App\Http\Controllers\Joystick\CategoryController;
use App\Http\Controllers\Joystick\ProductController;
use App\Http\Controllers\Joystick\ProductExtensionController;
use App\Http\Controllers\Joystick\BannerController;
use App\Http\Controllers\Joystick\AppController;
use App\Http\Controllers\Joystick\OrderController;
use App\Http\Controllers\Joystick\OptionController;
use App\Http\Controllers\Joystick\ModeController;
use App\Http\Controllers\Joystick\ProjectController;
use App\Http\Controllers\Joystick\ProjectIndexController;
use App\Http\Controllers\Joystick\CompanyController;
use App\Http\Controllers\Joystick\CurrencyController;
use App\Http\Controllers\Joystick\RegionController;
use App\Http\Controllers\Joystick\UserController;
use App\Http\Controllers\Joystick\RoleController;
use App\Http\Controllers\Joystick\PermissionController;
use App\Http\Controllers\Joystick\LanguageController;

// Site Controllers
use App\Http\Controllers\InputController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController as BlogController;
use App\Http\Controllers\PageController as SiteController;

// Cargo Controllers
use App\Http\Controllers\Cargo\TrackController;
use App\Http\Controllers\Cargo\StatusController;
use App\Http\Controllers\Cargo\TrackExtensionController;

use App\Http\Livewire\Client\Index as Client;
use App\Http\Livewire\Client\Archive;

use App\Http\Livewire\Storage\Tracks;
use App\Http\Livewire\Storage\Reception;
use App\Http\Livewire\Storage\Sending;
use App\Http\Livewire\Storage\Sorting;
use App\Http\Livewire\Storage\SendLocally;
use App\Http\Livewire\Storage\Arrival;
use App\Http\Livewire\Storage\Giving;

use Illuminate\Support\Facades\Mail;
// use App\Jobs\SendMailNotification;
use App\Mail\SendMailNotification;

Route::get('testm', function() {

    Mail::to('issa.adilet@gmail.com')->send(new SendMailNotification());
});

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
    Route::get('tracks', Tracks::class);
    Route::get('/', Reception::class);
    Route::get('reception', Reception::class);
    Route::get('sending', Sending::class);
    Route::get('sorting', Sorting::class);
    Route::get('send-locally', SendLocally::class);
    Route::get('arrival', Arrival::class);
    Route::get('giving', Giving::class);
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
        'categories' => CategoryController::class,
        // 'projects' => ProjectController::class,
        // 'projects-index' => ProjectIndexController::class,
        'products' => ProductController::class,
        // 'banners' => BannerController::class,
        'orders' => OrderController::class,
        'options' => OptionController::class,
        'modes' => ModeController::class,
        'apps' => AppController::class,

        // Resources
        'companies' => CompanyController::class,
        'currencies' => CurrencyController::class,
        'regions' => RegionController::class,
        'users' => UserController::class,
        'roles' => RoleController::class,
        'permissions' => PermissionController::class,
        'languages' => LanguageController::class,
    ]);

    // Cargo
    Route::get('tracks/search/tracks', [TrackController::class, 'search']);
    Route::get('tracks/{id}/search/users', [TrackController::class, 'searchUsers']);
    Route::get('tracks/{id}/pin-user/{userId}', [TrackController::class, 'pinUser']);
    Route::get('tracks/{id}/unpin-user', [TrackController::class, 'unpinUser']);
    Route::get('tracks/user/{id}', [TrackController::class, 'tracksUser']);

    Route::get('reception-tracks', [TrackExtensionController::class, 'receptionTracks']);
    Route::get('arrival-tracks', [TrackExtensionController::class, 'arrivalTracks']);
    Route::post('upload-tracks', [TrackExtensionController::class, 'uploadTracks']);
    Route::post('export-tracks', [TrackExtensionController::class, 'exportTracks']);

    // Content
    Route::get('categories-actions', [CategoryController::class, 'actionCategories']);
    Route::get('products/{id}/copy', [ProductController::class, 'copy']);
    Route::get('products-search', [ProductExtensionController::class, 'search']);
    Route::get('products-search-ajax', [ProductExtensionController::class, 'searchAjax']);
    Route::get('products-actions', [ProductExtensionController::class, 'actionProducts']);
    Route::get('products-category/{id}', [ProductExtensionController::class, 'categoryProducts']);
    Route::get('joytable', [ProductExtensionController::class, 'joytable']);
    Route::post('joytable-update', [ProductExtensionController::class, 'joytableUpdate']);
    Route::get('products-export', [ProductExtensionController::class, 'export']);
    Route::get('products-import', [ProductExtensionController::class, 'importView']);
    Route::post('products-import', [ProductExtensionController::class, 'import']);
    Route::get('products-price/edit', [ProductExtensionController::class, 'calcForm']);
    Route::post('products-price/update', [ProductExtensionController::class, 'priceUpdate']);

    // Resources
    Route::get('companies-actions', [CompanyController::class, 'actionCompanies']);
    Route::get('users/search/user', [UserController::class, 'search']);
    // Route::get('users/search-ajax', [UserController::class, 'searchAjax']);
    Route::get('users/password/{id}/edit', [UserController::class, 'passwordEdit']);
    Route::put('users/password/{id}', [UserController::class, 'passwordUpdate']);
    Route::get('users/correction/tels', [UserController::class, 'correctionTels']);
});


// Input Actions
// Route::get('search', [InputController::class, 'search']);
// Route::get('search-ajax', [InputController::class, 'searchAjax']);
Route::get('search-track', [InputController::class, 'searchTrack']);
Route::post('send-app', [InputController::class, 'sendApp']);


// Market
Route::redirect('market', '/'.app()->getLocale().'/market');
Route::group(['prefix' => '{lang}/market'], function() {

    Route::get('/', [MarketController::class, 'index']);
    Route::get('search-ajax', [MarketController::class, 'searchAjax']);
    Route::get('search', [MarketController::class, 'search']);
    Route::get('{category}/{id}', [MarketController::class, 'categoryProducts']);
    Route::get('{id}-{product}', [MarketController::class, 'product']);

    // Cart Actions
    Route::get('cart', [CartController::class, 'cart']);
    Route::get('checkout', [CartController::class, 'checkout']);
    Route::get('add-to-cart/{id}', [CartController::class, 'addToCart']);
    Route::get('remove-from-cart/{id}', [CartController::class, 'removeFromCart']);
    Route::get('clear-cart', [CartController::class, 'clearCart']);
    Route::post('store-order', [CartController::class, 'storeOrder']);
    Route::get('destroy-from-cart/{id}', [CartController::class, 'destroy']);

    // Favourite Actions
    Route::get('favorite', [FavouriteController::class, 'getFavorite']);
    Route::get('toggle-favourite/{id}', [FavouriteController::class, 'toggleFavourite']);
});


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
