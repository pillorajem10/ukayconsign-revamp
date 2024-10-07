<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreInventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PromoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Define a route for the home page


// Route for the home page
Route::get('/shop', [ProductController::class, 'index'])->name('home');

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);
Route::get('/verify/{token}', [LoginController::class, 'verify'])->name('verify');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login'); // Redirect to the login page
})->name('logout'); // Naming the route

// PRODUCT ROUTES
Route::get('/products', function () {
    return redirect()->route('home'); // Redirect to the home route
})->name('products.index');


// CART
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/delete-selected', [CartController::class, 'deleteSelected'])->name('cart.deleteSelected');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');


// STORE ROUTES
Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');

// STORE INVENTORY ROUTES
Route::get('/store-inventory', [StoreInventoryController::class, 'index'])->name('store-inventory.index');

// Checkout Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
});

// ORDERS (transactions) ROUTES
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::put('/orders/{id}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

// POS
Route::match(['get', 'post'], '/pos', [PosController::class, 'index'])->name('pos.index');
Route::post('/pos/sale', [PosController::class, 'completeSale'])->name('sales.store');
Route::get('/pos/choose', [PosController::class, 'chooseStore'])->name('pos.choose');

// SALES
Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');

// PROMOS
Route::get('/', [PromoController::class, 'index']);






