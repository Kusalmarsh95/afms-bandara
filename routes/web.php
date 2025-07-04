<?php

use App\Http\Controllers\AbsentHistoryController;
use App\Http\Controllers\ContributionInterestController;
use App\Http\Controllers\InterestCalculationController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\LoanProductController;
use App\Http\Controllers\RejectReasonController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BankBranchController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MemberStatusController;
use App\Http\Controllers\MonthlyDeductionController;
use App\Http\Controllers\NomineeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\RegimentController;
use App\Http\Controllers\RelationshipController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SuwasahanaController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\WithdrawalProductController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function() {
    Route::view('about', 'about')->name('about');

    Route::get('users', [UserController::class, 'index'])->name('users.index');

    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('units', UnitController::class);
    Route::resource('regiments', RegimentController::class);
    Route::resource('ranks', RankController::class);
    Route::resource('relationships', RelationshipController::class);
    Route::resource('districts', DistrictController::class);
    Route::resource('banks', BankController::class);
    Route::resource('bank-branches', BankBranchController::class);
    Route::resource('reject-reasons', RejectReasonController::class);
    Route::resource('member-status', MemberStatusController::class);
    Route::resource('withdrawal-products', WithdrawalProductController::class);
    Route::resource('contribution-interests', ContributionInterestController::class);
    Route::resource('loan-products', LoanProductController::class);

    Route::resource('memberships', MembershipController::class);
    Route::get('membership-assigns', [MembershipController::class, 'indexAssigns'])->name('membership-assigns');
    Route::get('membership-changes', [MembershipController::class, 'indexChanges'])->name('membership-changes');
    Route::get('membership-rejects', [MembershipController::class, 'indexRejects'])->name('membership-rejects');
    Route::get('membership-approval/{id}', [MembershipController::class, 'apporvalView'])->name('membership-approval');
    Route::put('/membership-approval/{membership}/approval', [MembershipController::class, 'approveReject'])->name('approval');
    Route::get('changes-upload', [MembershipController::class, 'uploadView'])->name('changes-upload');
    Route::post('/changes-xml', [MembershipController::class, 'upload'])->name('changes-xml');

    Route::get('/nominees/create/{membership_id}', [NomineeController::class, 'create'])->name('nominees.create');
    Route::get('/nominees/create/{membership_id}', [NomineeController::class, 'create'])->name('nominees.create');
    Route::post('/nominees/{membership_id}', [NomineeController::class, 'store'])->name('nominees.store');
    Route::get('/nominees/edit/{id}', [NomineeController::class, 'edit'])->name('nominees.edit');
    Route::put('/nominees/{id}', [NomineeController::class, 'update'])->name('nominees.update');
    Route::get('/nominees', [NomineeController::class, 'newNominees'])->name('nominees.newNominees');
    Route::get('nominee-changes', [NomineeController::class, 'newChanges'])->name('nominee-changes');
    Route::get('nominee-rejects', [NomineeController::class, 'newRejects'])->name('nominee-rejects');
    Route::delete('/nominees/{nominee}', [NomineeController::class, 'destroy'])->name('nominees.destroy');
    Route::get('nominee-approval/{id}', [NomineeController::class, 'apporvalView'])->name('nominee-approval');
    Route::put('/nominee-approval/{id}/approval', [NomineeController::class, 'approveReject'])->name('n-approval');

    Route::get('/absents/create/{membership_id}', [AbsentHistoryController::class, 'create'])->name('absents.create');
    Route::post('/absents/{membership_id}', [AbsentHistoryController::class, 'store'])->name('absents.store');
    Route::get('/absents/edit/{id}', [AbsentHistoryController::class, 'edit'])->name('absents.edit');
    Route::put('/absents/{id}', [AbsentHistoryController::class, 'update'])->name('absents.update');
    Route::delete('/absents/{id}', [AbsentHistoryController::class, 'destroy'])->name('absents.destroy');

    Route::get('/monthlyDeductions', [MonthlyDeductionController::class, 'index'])->name('monthlyDeductions.index');
    Route::get('/corrections', [MonthlyDeductionController::class, 'corrections'])->name('corrections');
    Route::get('/additional-contribution', [MonthlyDeductionController::class, 'newContributions'])->name('additional-contribution');
    Route::get('/additional-contribution/create/{id}', [MonthlyDeductionController::class, 'create'])->name('monthlyDeductions.create');
    Route::post('/additional-contribution/{id}', [MonthlyDeductionController::class, 'store'])->name('monthlyDeductions.store');
    Route::get('/additional-contribution/edit/{id}', [MonthlyDeductionController::class, 'edit'])->name('monthlyDeductions.edit');
    Route::get('/corrections/edit/{id}', [MonthlyDeductionController::class, 'correctionsEdit'])->name('corrections.edit');
    Route::put('/additional-contribution/{contribution_id}', [MonthlyDeductionController::class, 'update'])->name('monthlyDeductions.update');
    Route::put('/corrections/{id}', [MonthlyDeductionController::class, 'correctionsUpdate'])->name('corrections.update');
    Route::get('contribution-upload', [MonthlyDeductionController::class, 'uploadView'])->name('contribution-upload');
    Route::post('/upload-xml', [MonthlyDeductionController::class, 'upload'])->name('upload-xml');
    Route::get('/upload-report', [MonthlyDeductionController::class, 'upload'])->name('upload-report');
    Route::get('/failures/{year}/{month}/{category}', [MonthlyDeductionController::class, 'downloadCSV'])->name('download.failures');
    Route::get('contribution-approval/{id}', [MonthlyDeductionController::class, 'apporvalView'])->name('contribution-approval');
    Route::get('correction-approval/{id}', [MonthlyDeductionController::class, 'correctionApporval'])->name('correction-approval');
    Route::put('/contribution-approval/{id}/approval', [MonthlyDeductionController::class, 'approveStore'])->name('c-approval');
    Route::put('/correction-approval/{id}/approval', [MonthlyDeductionController::class, 'correctionApproveStore'])->name('correction-approvalstore');
    Route::delete('/additional-contribution/{id}', [MonthlyDeductionController::class, 'destroy'])->name('monthlyDeductions.destroy');
    Route::delete('/contribution-correction/{id}', [MonthlyDeductionController::class, 'correctionDestroy'])->name('correction-destroy');
    Route::get('repayment-upload', [MonthlyDeductionController::class, 'repaymentView'])->name('repayment-upload');
    Route::post('/repayment-xml', [MonthlyDeductionController::class, 'uploadRepayment'])->name('repayment-xml');
//    Route::get('/repayment-failures', [MonthlyDeductionController::class, 'repaymentCSV'])->name('repayment-failures');
    Route::get('/repayment-failures/{year}/{month}/{category}', [MonthlyDeductionController::class, 'repaymentCSV'])->name('repayment-failures');
    Route::get('/correction-create/{id}', [MonthlyDeductionController::class, 'correctionCreate'])->name('correctionCreate');
    Route::post('/correction/{id}', [MonthlyDeductionController::class, 'correctionStore'])->name('correctionStore');
    Route::get('repayment-create', [MonthlyDeductionController::class, 'repaymentCreate'])->name('repayment-create');
    Route::post('/repayment-batch', [MonthlyDeductionController::class, 'repaymentBatch'])->name('repayment-batch');
    Route::post('export-partials', [MonthlyDeductionController::class, 'partialsCSV'])->name('partial-csv');
    Route::post('export-full', [MonthlyDeductionController::class, 'fullCSV'])->name('full-csv');

    Route::get('/withdrawals/create/{id}', [WithdrawalController::class, 'create'])->name('withdrawals.create');
    Route::get('/withdrawals/create-full/{id}', [WithdrawalController::class, 'createFull'])->name('withdrawals.createFull');
    Route::get('/withdrawals/create-special/{id}', [WithdrawalController::class, 'createSpecial'])->name('withdrawals.createSpecial');
    Route::post('/withdrawals/{id}', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::post('/withdrawalsFull/{id}', [WithdrawalController::class, 'storeFull'])->name('withdrawals.storeFull');
    Route::post('/withdrawalSpecial/{id}', [WithdrawalController::class, 'storeSpecial'])->name('withdrawals.storeSpecial');
    Route::get('/withdrawals/edit-partial/{id}', [WithdrawalController::class, 'editPartial'])->name('withdrawals.editPartial');
    Route::put('/withdrawals-partial/{id}', [WithdrawalController::class, 'updatePartial'])->name('withdrawals.updatePartial');
    Route::get('/withdrawals/edit-full/{id}', [WithdrawalController::class, 'editFull'])->name('withdrawals.editFull');
    Route::put('/withdrawals-full/{id}', [WithdrawalController::class, 'updateFull'])->name('withdrawals.updateFull');
    Route::get('/withdrawals/{id}', [WithdrawalController::class, 'show'])->name('withdrawals.show');
    Route::get('/withdrawalsFull/{id}', [WithdrawalController::class, 'showFull'])->name('withdrawals.showFull');
    Route::get('/withdrawals', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::get('/partial-withdrawals', [WithdrawalController::class, 'indexPartial'])->name('withdrawals.indexPartial');
    Route::get('/full-withdrawals', [WithdrawalController::class, 'indexFull'])->name('withdrawals.indexFull');
    Route::get('/partial-view/{id}', [WithdrawalController::class, 'viewPartial'])->name('partial-view');
    Route::get('/partial-voucher/{id}', [WithdrawalController::class, 'partialVoucher'])->name('partial-voucher');
    Route::get('/full-view/{id}', [WithdrawalController::class, 'viewFull'])->name('full-view');
    Route::get('/full-voucher/{id}', [WithdrawalController::class, 'fullVoucher'])->name('full-voucher');
    Route::get('/partial-approved/{id}', [WithdrawalController::class, 'approvedPartial'])->name('partial-approved');
    Route::get('/full-approved/{id}', [WithdrawalController::class, 'approvedFull'])->name('full-approved');
    Route::put('/partial-approval/{id}/processing', [WithdrawalController::class, 'approveReject'])->name('partial-approval');
    Route::put('/full-approval/{id}/processing', [WithdrawalController::class, 'fullApproveReject'])->name('full-approval');
    Route::put('/partial-disburse/{id}/disbursing', [WithdrawalController::class, 'disbursePartial'])->name('partial-disburse');
    Route::put('/full-disburse/{id}/disbursing', [WithdrawalController::class, 'disburseFull'])->name('full-disburse');
    Route::delete('/partial-withdrawal/{id}', [WithdrawalController::class, 'destroyPartial'])->name('withdrawals.destroyPartial');
    Route::delete('/full-withdrawal/{id}', [WithdrawalController::class, 'destroyFull'])->name('withdrawals.destroyFull');
    Route::get('/partial-bulk', [WithdrawalController::class, 'indexPartialApproved'])->name('partial.bulk');
    Route::put('partial-banked', [WithdrawalController::class, 'partialBanked'])->name('partial.banked');
    Route::put('partial-payment', [WithdrawalController::class, 'releasePartialPayment'])->name('partial.pay');
    Route::post('/partial-send-to-bulk', [WithdrawalController::class, 'partialToBulk'])->name('partials.sendToBulk');
    Route::get('/full-bulk', [WithdrawalController::class, 'indexFullApproved'])->name('full.bulk');
    Route::put('full-payment', [WithdrawalController::class, 'releaseFullPayment'])->name('full.pay');
    Route::post('/full-send-to-bulk', [WithdrawalController::class, 'fullToBulk'])->name('full.sendToBulk');
    Route::put('full-banked', [WithdrawalController::class, 'fullBanked'])->name('full.banked');

    Route::get('/interest-calculation/create', [InterestCalculationController::class, 'create'])->name('create-calculation');
    Route::get('/yearly-contribution/{id}', [InterestCalculationController::class, 'createYCS'])->name('create-yearly-contribution');
    Route::post('/interest-calculation', [InterestCalculationController::class, 'store'])->name('store-calculation');
    Route::post('/store-yearly-contribution/{id}', [InterestCalculationController::class, 'storeYCS'])->name('store-yearly-contribution');
    Route::get('/interest-calculation/edit/{id}', [InterestCalculationController::class, 'editCalculation'])->name('edit-calculation');
    Route::put('/interest-calculation/{id}', [InterestCalculationController::class, 'updateCalculation'])->name('update-calculation');
    Route::delete('/interest-delete/{id}', [InterestCalculationController::class, 'destroy'])->name('interest-destroy');

    Route::get('/loan', [LoanController::class, 'index'])->name('loan.index');
    Route::get('/loan-bulk', [LoanController::class, 'indexApproved'])->name('loan.bulk');
    Route::get('/loan/create/{id}', [LoanController::class, 'create'])->name('loan.create');
    Route::post('/loan/{id}', [LoanController::class, 'store'])->name('loan.store');
    Route::get('/loan/edit/{id}', [LoanController::class, 'edit'])->name('loan.edit');
    Route::put('/loan/{id}', [LoanController::class, 'update'])->name('loan.update');
    Route::get('/loan/{id}', [LoanController::class, 'view'])->name('loan.view');
    Route::get('/loan-show/{id}', [LoanController::class, 'show'])->name('loan.show');
    Route::get('/loan-approved/{id}', [LoanController::class, 'approved'])->name('loan.approved');
    Route::put('/loan-approval/{id}', [LoanController::class, 'approveReject'])->name('loan.approval');
    Route::put('/loan-disburse/{id}', [LoanController::class, 'disburse'])->name('loan.disburse');
    Route::post('/loans-send-to-bulk', [LoanController::class, 'sendToBulk'])->name('loan.sendToBulk');
    Route::delete('/loan/{id}', [LoanController::class, 'destroy'])->name('loan.destroy');
    Route::get('/loan-settlement', [LoanController::class, 'indexSettlement'])->name('loan.indexSettlement');
    Route::get('/absent-settlement', [LoanController::class, 'absentSettlement'])->name('absent-settlement');
    Route::get('/loan-settlement/{id}', [LoanController::class, 'editSettlement'])->name('loan.editSettlement');
    Route::get('/absent-settlement/{id}', [LoanController::class, 'absentSettlementView'])->name('absent-settlement-view');
    Route::put('/loan-settlement/{id}', [LoanController::class, 'updateSettlement'])->name('loan.updateSettlement');
    Route::put('/absent-settlement/{id}', [LoanController::class, 'absentSettlementUpdate'])->name('absent-settlement-update');
    Route::delete('/loan-settlement/{id}', [LoanController::class, 'destroySettlement'])->name('settlement.destroy');
    Route::put('loans-banked', [LoanController::class, 'banked'])->name('loan.banked');
    Route::put('loans-payment', [LoanController::class, 'releasePayment'])->name('loan.pay');

    Route::get('/loan-settlement-pdf/{id}', [LoanController::class, 'settlementPDF'])->name('loan-settlement-pdf');
    Route::get('/loan-voucher/{id}', [LoanController::class, 'loanVoucher'])->name('loan-voucher');
    Route::get('/show-pdf/{id}', [LoanController::class, 'showPDF'])->name('show-pdf');

    Route::get('/suwasahana', [SuwasahanaController::class, 'index'])->name('suwasahana.index');
    Route::get('/suwasahana-create/{id}', [SuwasahanaController::class, 'create'])->name('suwasahana.create');
    Route::post('/suwasahana/{id}', [SuwasahanaController::class, 'store'])->name('suwasahana.store');
    Route::get('/suwasahana-edit/{id}', [SuwasahanaController::class, 'edit'])->name('suwasahana.edit');
    Route::put('/suwasahana/{id}', [SuwasahanaController::class, 'update'])->name('suwasahana.update');
    Route::get('/suwasahana-show/{id}', [SuwasahanaController::class, 'show'])->name('suwasahana.show');
    Route::put('/suwasahana-approval/{id}', [SuwasahanaController::class, 'approveReject'])->name('suwasahana.approval');
    Route::get('suwasahana-repayment', [SuwasahanaController::class, 'repaymentView'])->name('suwasahana-repayment');
    Route::post('suwasahana-upload', [SuwasahanaController::class, 'uploadRepayment'])->name('suwasahana-upload');
    Route::get('suwasahana-failures/{year}/{month}', [SuwasahanaController::class, 'repaymentCSV'])->name('suwasahana-failures');
    Route::delete('suwasahana/{id}', [LoanController::class, 'destroy'])->name('suwasahana.destroy');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('closing-balance', [ReportController::class, 'closingBalanceView'])->name('closing-balance');
    Route::get('member-contribution', [ReportController::class, 'contributionView'])->name('member-contribution');
    Route::get('closing-balance-csv', [ReportController::class, 'closingBalanceCSV'])->name('closing-balance-csv');
    Route::get('loan-custout', [ReportController::class, 'custOut'])->name('loan-custout');
    Route::get('withdrawal-custout', [ReportController::class, 'withdrawalCustOut'])->name('withdrawal-custout');
    Route::get('full-custout', [ReportController::class, 'fullCustOut'])->name('full-custout');
    Route::get('member-contribution-pdf', [ReportController::class, 'contributionCSV'])->name('member-contribution-csv');
    Route::get('loan-installment', [ReportController::class, 'loanInstallmentView'])->name('loan-installment');
    Route::get('outstanding', [ReportController::class, 'outstanding'])->name('outstanding');
    Route::get('outstanding-details', [ReportController::class, 'outstandingDetails'])->name('outstanding-details');
    Route::get('pdf-outstanding-details', [ReportController::class, 'pdfOutstandingDetails'])->name('pdf-outstanding-details');
    Route::get('pdf-outstanding-summary', [ReportController::class, 'pdfOutstandingSummary'])->name('pdf-outstanding-summary');
    Route::get('pdf-outstanding-weekly', [ReportController::class, 'pdfOutstandingWeekly'])->name('pdf-outstanding-weekly');
    Route::get('disburse-loan', [ReportController::class, 'loanDisburseView'])->name('disburse-loan');
    Route::get('disburse-partial', [ReportController::class, 'partialDisburseView'])->name('disburse-partial');
    Route::get('disburse-full', [ReportController::class, 'fullDisburseView'])->name('disburse-full');
    Route::get('pdf-disburse-loan', [ReportController::class, 'loanDisbursePDF'])->name('pdf-disburse-loan');
    Route::get('pdf-disburse-partial', [ReportController::class, 'partialDisbursePDF'])->name('pdf-disburse-partial');
    Route::get('pdf-disburse-full', [ReportController::class, 'fullDisbursePDF'])->name('pdf-disburse-full');
    Route::get('export-loans', [ReportController::class, 'installmentCSV'])->name('installment-csv');
    Route::get('ledger-sheet/{id}', [ReportController::class, 'ledgerSheet'])->name('ledger-sheet');
    Route::get('final-payment', [ReportController::class, 'finalPayment'])->name('final-payment');
    Route::get('fund-balance', [ReportController::class, 'fundBalance'])->name('fund-balance');
    Route::get('loan-ledger/{id}', [ReportController::class, 'loanLedger'])->name('loan-ledger');


});
