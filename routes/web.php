<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/order', function () {
    return view('orders.purchase');
});

Route::get('/order/pdf', function () {
    ini_set('max_execution_time', '1200');
    $pdf = Pdf::loadView('orders.purchase');
    $pdf->setPaper('letter', 'landscape');
    return $pdf->download('purchase_order.pdf');
});

Route::get('/foo', function () {
    Artisan::call('storage:link');
});
