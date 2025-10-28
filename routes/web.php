<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomAuthController;
use App\Http\Controllers\DrugGroupController;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\Pharmacy\SearchController;
use App\Http\Controllers\Pharmacy\CartController;
use App\Http\Controllers\Company\OrderController as CompanyOrderController;
use App\Http\Controllers\Pharmacy\OrderController as PharmacyOrderController;
use App\Http\Controllers\Delivery\DashboardController as DeliveryDashboard;
use App\Http\Controllers\Company\DeliveryAssignController;

Route::get('/', function () {
    return view('landing', ['title' => 'مرحباً بك']);
})->name('landing');

// GET /login
Route::get('/ping', fn() => 'pong');

Route::middleware('guest')->get('/login', [CustomAuthController::class, 'showLogin'])
    ->name('login');

// POST /login
Route::middleware('guest')->post('/login', [CustomAuthController::class, 'login'])
    ->name('login.post');

// POST /logout
Route::middleware('auth')->post('/logout', [CustomAuthController::class, 'logout'])
    ->name('logout');

// توجيه حسب الدور
Route::get('/redirect', function () {
    $role = auth()->user()->role ?? null;
    return match ($role) {
        'admin'    => redirect('/admin/dashboard'),
        'company'  => redirect('/company/groups'),
        default    => redirect('/pharmacy/search'),
    };
})->middleware('auth');

Route::middleware(['auth','role:company'])->prefix('company')->name('company.')->group(function () {
    Route::get('groups',        [DrugGroupController::class, 'index'])->name('groups.index');
    Route::get('groups/create', [DrugGroupController::class, 'create'])->name('groups.create');
    Route::post('groups',       [DrugGroupController::class, 'store'])->name('groups.store');

    Route::get('groups/{group}',           [DrugGroupController::class, 'show'])->name('groups.show');
    Route::post('groups/{group}/submit',   [DrugGroupController::class, 'submit'])->name('groups.submit');
    Route::post('groups/{group}/publish',  [DrugGroupController::class, 'publish'])->name('groups.publish');
    Route::post('groups/{group}/archive',  [DrugGroupController::class, 'archive'])->name('groups.archive');

    Route::get('groups/{group}/drugs/create', [DrugController::class, 'create'])->name('drugs.create');
    Route::post('groups/{group}/drugs',       [DrugController::class, 'store'])->name('drugs.store');
     // إدارة الطلبيات
        Route::get('orders',               [CompanyOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}',       [CompanyOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [CompanyOrderController::class, 'updateStatus'])->name('orders.updateStatus');
            // صفحة أو ازرار الإسناد تكون داخل عرض تفاصيل الطلب الحالي
    Route::post('orders/{order}/assign', [DeliveryAssignController::class, 'assign'])->name('orders.assign');
    Route::delete('orders/{order}/unassign', [DeliveryAssignController::class, 'unassign'])->name('orders.unassign');
});

Route::middleware(['auth','role:pharmacy'])->prefix('pharmacy')->name('pharmacy.')->group(function () {
    // صفحة البحث
    Route::get('search', [SearchController::class, 'index'])->name('search');

    // السلة
    Route::get('cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('cart/add/{drug}', [CartController::class, 'add'])->name('cart.add');
    Route::post('cart/update/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/remove/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('reports', function () {
            return view('company.reports.index', ['title' => 'تقارير الشركة']);
        })->name('reports.index');
    // إرسال طلبية إلى شركة معينة (من العناصر التابعة لها فقط)
    Route::post('orders/checkout/{company}', [PharmacyOrderController::class, 'checkoutCompany'])->name('orders.checkout.company');

    // (اختياري) صفحة لائحة الطلبيات
     Route::get('orders', [PharmacyOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [PharmacyOrderController::class, 'show'])->name('orders.show'); 
});


Route::middleware(['auth','role:delivery'])
->prefix('delivery')->name('delivery.')
->group(function () {
    Route::get('/tasks', [DeliveryDashboard::class, 'index'])->name('dashboard');
    Route::get('orders/{order}', [DeliveryDashboard::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}/status', [DeliveryDashboard::class, 'updateStatus'])->name('orders.updateStatus');
});


Route::get('/force-logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return 'logged-out';
});
