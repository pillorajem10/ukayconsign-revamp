<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UscReturnController;
use App\Http\Controllers\StoreInventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\PosSaleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CxInfoController;
use App\Http\Controllers\TallyController;
use App\Http\Controllers\InstantBuyProductController;
use App\Http\Controllers\StaticPagesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleBreakdownController;
use App\Http\Controllers\BillingController;

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
Route::get('/', [ProductController::class, 'index'])->name('home');

// Auth Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); // Show login form
Route::post('/login', [LoginController::class, 'login']); // Handle login submission

// Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
// Route::post('/register', [LoginController::class, 'register']);
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
Route::post('/cart/add-quantity/{cartId}', [CartController::class, 'addQuantity'])->name('cart.addQuantity');
Route::post('/cart/sub-quantity/{cartId}', [CartController::class, 'subQuantity'])->name('cart.subQuantity');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');


// STORE ROUTES
Route::get('/stores', [StoreController::class, 'index'])->name('stores.index');

// STORE INVENTORY ROUTES
Route::get('/store-inventory', [StoreInventoryController::class, 'index'])->name('store-inventory.index');
Route::put('/store-inventory/{id}', [StoreInventoryController::class, 'update'])->name('store-inventory.update');

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// ORDERS (transactions) ROUTES
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::put('/orders/{id}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

// POS
Route::match(['get', 'post'], '/pos', [PosController::class, 'index'])->name('pos.index');
Route::post('/pos/sale', [PosController::class, 'completeSale'])->name('sales.store');
Route::get('/pos/choose', [PosController::class, 'chooseStore'])->name('pos.choose');
Route::post('/pos/void', [PosController::class, 'voidItem'])->name('pos.void');
Route::post('/pos/apply-discount', [PosController::class, 'applyDiscount'])->name('pos.applyDiscount'); // Add this line

Route::match(['get', 'post'], '/pos-preloved', [PosSaleController::class, 'index'])->name('posSale.index');
Route::post('/pos-preloved/sale', [PosSaleController::class, 'completeSale'])->name('salesPrelove.store');
Route::post('/pos-preloved/void', [PosSaleController::class, 'voidItem'])->name('posSale.void');
Route::post('/pos-preloved/apply-discount', [PosSaleController::class, 'applyDiscount'])->name('posSale.applyDiscount'); // Add this line


// SALES
Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
Route::put('/sales/{sale_id}/void', [SaleController::class, 'voidSale'])->name('sales.void');


// PROMOS
// Route::get('/promos', [PromoController::class, 'index'])->name('promos.index');

// DASHBOARD
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// TALLIES
Route::get('/tallies', [TallyController::class, 'index'])->name('tallies.index');

// INSTANT BUY 
Route::get('/instant-buy', [InstantBuyProductController::class, 'index'])->name('instantBuy.index');
Route::get('/instant-buy/create', [InstantBuyProductController::class, 'create'])->name('instant_buy_products.create');
Route::post('/instant-buy', [InstantBuyProductController::class, 'store'])->name('instant_buy_products.store');

// REPORTS
// Route::get('/reports', [ReportController::class, 'index'])->name('reports');

// STATIC PAGES
Route::get('/how-to-use-usc', [StaticPagesController::class, 'howToUseUSC'])->name('how.to.use.usc');

// RETURN REQUEST
Route::get('/usc-returns/create', [UscReturnController::class, 'create'])->name('usc-returns.create');
Route::post('/usc-returns', [UscReturnController::class, 'store'])->name('usc-returns.store');
Route::get('/usc-returns', [UscReturnController::class, 'index'])->name('usc-returns.index');
Route::put('/returns/{returnId}/received-back', [UscReturnController::class, 'receivedBackItems'])->name('usc-returns.receivedBack');

// CX INFOS
Route::get('/cx-infos', [CxInfoController::class, 'index'])->name('cxInfos.index');
Route::post('/send-blast-emails', [CxInfoController::class, 'sendBlastEmails'])->name('sendBlastEmails');

// SALE BREAKDOWN
Route::get('/sale-breakdown', [SaleBreakdownController::class, 'index'])->name('saleBreakdown.index');


// BILLING
Route::get('/billings', [BillingController::class, 'index'])->name('billings.index');
Route::get('/billings/{id}', [BillingController::class, 'show'])->name('billings.show');
Route::get('/billings/{id}/upload-proof', [BillingController::class, 'showUploadProofOfPayment'])->name('billings.showUploadProofOfPayment');
Route::put('/billings/{id}/update-payment', [BillingController::class, 'updatePayment'])->name('billings.updatePayment');




