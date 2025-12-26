<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
// Thay vì hiển thị text, ta gọi hàm testQR từ MenuController
Route::get('/', [MenuController::class, 'testQR'])->name('home');

// Giữ nguyên các route khác
Route::get('/menu', [MenuController::class, 'index'])->name('menu');
Route::post('/order', [MenuController::class, 'storeOrder'])->name('order.store');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// Lưu ý: route này phải nằm trong group có middleware 'auth' (nếu ông có đăng nhập)
Route::patch('/admin/orders/{id}/pay', [App\Http\Controllers\Admin\OrderController::class, 'markAsPaid'])
     ->name('admin.orders.pay');

use App\Http\Controllers\Admin\OrderController;

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders');
});
require __DIR__.'/auth.php';
