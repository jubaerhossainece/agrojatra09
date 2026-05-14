<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DepositController as AdminDeposit;
use App\Http\Controllers\Admin\MemberController as AdminMember;
use App\Http\Controllers\Admin\OpinionController as AdminOpinion;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Member\DashboardController as MemberDashboard;
use App\Http\Controllers\Member\DepositController as MemberDeposit;
use App\Http\Controllers\Member\ProfileController as MemberProfile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

require __DIR__.'/auth.php';

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('members', AdminMember::class);
    Route::resource('deposits', AdminDeposit::class)->except(['edit', 'update']);
    Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');
    Route::get('/opinions', [AdminOpinion::class, 'index'])->name('opinions.index');
    Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [AdminUser::class, 'update'])->name('users.update');
});

// Member routes
Route::prefix('member')->name('member.')->middleware(['auth', 'member'])->group(function () {
    Route::get('/dashboard', [MemberDashboard::class, 'index'])->name('dashboard');
    Route::get('/profile', [MemberProfile::class, 'show'])->name('profile');
    Route::resource('deposits', MemberDeposit::class)->except(['show']);
    Route::post('/deposits/toggle-permission', [MemberDeposit::class, 'togglePermission'])->name('deposits.toggle-permission');
});

// Redirect /dashboard to role-based dashboard
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('member.dashboard');
})->middleware('auth')->name('dashboard');
