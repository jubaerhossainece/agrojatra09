<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DepositController as AdminDeposit;
use App\Http\Controllers\Admin\MemberController as AdminMember;
use App\Http\Controllers\Admin\MonthlyPaymentController as AdminMonthlyPayment;
use App\Http\Controllers\Admin\OpinionController as AdminOpinion;
use App\Http\Controllers\Admin\ReportController as AdminReport;
use App\Http\Controllers\Admin\ShareChangeController as AdminShareChange;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Member\DashboardController as MemberDashboard;
use App\Http\Controllers\Member\DepositController as MemberDeposit;
use App\Http\Controllers\Member\MembersController as MemberMembers;
use App\Http\Controllers\Member\PaymentScheduleController as MemberPaymentSchedule;
use App\Http\Controllers\Member\ProfileController as MemberProfile;
use App\Http\Controllers\Member\ShareChangeController as MemberShareChange;
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
    Route::post('/deposits/{deposit}/approve', [AdminDeposit::class, 'approve'])->name('deposits.approve');
    Route::post('/deposits/{deposit}/reject', [AdminDeposit::class, 'reject'])->name('deposits.reject');
    Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');
    Route::get('/opinions', [AdminOpinion::class, 'index'])->name('opinions.index');
    Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [AdminUser::class, 'update'])->name('users.update');
    // Monthly payments
    Route::get('/monthly-payments', [AdminMonthlyPayment::class, 'index'])->name('monthly-payments.index');
    Route::post('/monthly-payments/generate', [AdminMonthlyPayment::class, 'generate'])->name('monthly-payments.generate');
    Route::get('/monthly-payments/{year}/{month}', [AdminMonthlyPayment::class, 'show'])->name('monthly-payments.show');
    Route::patch('/monthly-payments/{year}/{month}/due-date', [AdminMonthlyPayment::class, 'updateDueDate'])->name('monthly-payments.update-due-date');
    Route::delete('/monthly-payments/{year}/{month}', [AdminMonthlyPayment::class, 'deleteMonth'])->name('monthly-payments.delete-month');
    Route::patch('/monthly-payments/{monthlyPayment}/override-late', [AdminMonthlyPayment::class, 'overrideLate'])->name('monthly-payments.override-late');
    // Share change requests
    Route::post('/members/{member}/share-change', [AdminShareChange::class, 'store'])->name('members.share-change.store');
});

// Member routes
Route::prefix('member')->name('member.')->middleware(['auth', 'member'])->group(function () {
    Route::get('/dashboard', [MemberDashboard::class, 'index'])->name('dashboard');
    Route::get('/profile', [MemberProfile::class, 'show'])->name('profile');
    Route::resource('deposits', MemberDeposit::class)->except(['show']);
    Route::post('/deposits/toggle-permission', [MemberDeposit::class, 'togglePermission'])->name('deposits.toggle-permission');
    Route::get('/payment-schedule', [MemberPaymentSchedule::class, 'index'])->name('payment-schedule');
    Route::get('/members', [MemberMembers::class, 'index'])->name('members.index');
    Route::get('/members/{member}', [MemberMembers::class, 'show'])->name('members.show');
    // Share change requests
    Route::post('/share-changes/{shareChangeRequest}/approve', [MemberShareChange::class, 'approve'])->name('share-change.approve');
    Route::post('/share-changes/{shareChangeRequest}/reject', [MemberShareChange::class, 'reject'])->name('share-change.reject');
});

// Redirect /dashboard to role-based dashboard
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('member.dashboard');
})->middleware('auth')->name('dashboard');
