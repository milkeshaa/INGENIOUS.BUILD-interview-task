<?php

//use App\Modules\Invoices\Presentation\Controllers\Http\ApproveInvoice as ApproveInvoiceController;
use App\Modules\Invoices\Presentation\Controllers\Http\GetInvoice as GetInvoiceController;
//use App\Modules\Invoices\Presentation\Controllers\Http\RejectInvoice as RejectInvoiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('invoices/{invoice_id}')->group(function () {
    Route::get('', GetInvoiceController::class);
//    Route::patch('approve', ApproveInvoiceController::class);
//    Route::patch('reject', RejectInvoiceController::class);
});
