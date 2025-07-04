<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankBranchController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MemberStatusController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\RegimentController;
use App\Http\Controllers\RejectReasonController;
use App\Http\Controllers\RelationshipController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*Route::group(['middleware' => 'api',], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('user-profile', [AuthController::class, 'userProfile']);
});*/

//function customRoutes($controllerClass, $routeName)
//{
//    Route::group(['prefix' => $routeName], function () use ($controllerClass, $routeName) {
//        Route::get('/', [$controllerClass, 'getAll'])->name("{$routeName}.getAll");
//        Route::post('/', [$controllerClass, 'store'])->name("{$routeName}.store");
//        Route::get('{id}', [$controllerClass, 'view'])->name("{$routeName}.view");
//        Route::put('{id}', [$controllerClass, 'update'])->name("{$routeName}.update");
//        Route::delete('{id}', [$controllerClass, 'destroy'])->name("{$routeName}.destroy");
//    });
//}
//
//customRoutes(RankController::class, 'ranks');
//customRoutes(MemberStatusController::class, 'member-status');
//customRoutes(RegimentController::class, 'regiments');
//customRoutes(UnitController::class, 'units');
//customRoutes(DistrictController::class, 'districts');
//customRoutes(BankController::class, 'banks');
//customRoutes(BankBranchController::class, 'bank-branches');
//customRoutes(RelationshipController::class, 'relationships');
//customRoutes(RejectReasonController::class, 'reject-reasons');
//customRoutes(MembershipController::class, 'memberships');
//customRoutes(UserController::class, 'users');
//customRoutes(RoleController::class, 'roles');
//
//// Route for officer approval
//Route::put('/memberships/approve/{id}', [MembershipController::class, 'approveMember']);
//Route::put('/memberships/reject/{id}', [MembershipController::class, 'rejectMember']);
//
