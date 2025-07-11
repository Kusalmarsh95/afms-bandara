<?php

namespace App\Http\Controllers;

use App\Models\FailureLoanApi;
use App\Models\Membership;
use App\Models\RejectReason;
use App\Models\Suwasahana;
use App\Models\SuwasahanaAssign;
use App\Models\SuwasahanaFailures;
use App\Models\SuwasahanaRecovery;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SuwasahanaController extends Controller
{

    private $apiBaseUrl = 'http://192.168.1.67:5222';
    private $apiToken = null;
    private function authenticateApi()
    {
        try {
            $response = Http::post($this->apiBaseUrl . '/api/Auth/login', [
                'username' => 'SLArmy',
                'password' => 'SLArmy@2025'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->apiToken = $data['token'] ?? null;

                if ($this->apiToken) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    public function index(){
        $suwasahana = Suwasahana::with(['membership'])
            ->where('accepted', '!=', 1)->get();

        return view('suwasahana.index',compact('suwasahana'));

    }
    public function create($id)
    {
        $membership = Membership::with('ranks')->find($id);
        $users = User::all();

        return view('suwasahana.create', compact('membership', 'users'));
    }
    public function store(Request $request, $id)
    {
        $membership = Membership::find($id);

        $validated = $request->validate([
            'ABFvoucherno' => 'required',
            'PNRVoucherno' => 'nullable',
            'no_of_installments' => 'required',
            'Issue_Date' => 'required',
            'total_capital' => 'required',
            'total_interest' => 'required',
            'monthly_capital' => 'required',
            'LoanType' => 'required',

        ]);
        $validated['RegNo'] = $membership->regimental_number;
        $validated['member_id'] = $id;
        $validated['accepted'] = 0;
        $validated['settled'] = 0;
        $validated['created_system'] = 'AFMS';
        $validated['user'] = Auth::user()->name;
        $suwasahana = Suwasahana::create($validated);

        $validatedAssign = $request->validate([
            'fwd_to' => 'required',
            'fwd_to_reason' => 'required',
        ]);
        $validatedAssign['suwasahana_id'] = $suwasahana->id;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = 'Add suwasahana details';

        SuwasahanaAssign::create($validatedAssign);
        return redirect()->route('memberships.show', ['membership' => $id])
            ->with('success', 'Suwasahana loan added successfully and send for approval');
    }
    public function show($id)
    {
        $suwasahana = Suwasahana::with('membership')->find($id);
        $users = User::all();
        $rejectReasons = RejectReason::all();

        return view('suwasahana.show',compact('suwasahana', 'users', 'rejectReasons'));
    }
    public function approveReject(Request $request, $id)
    {
        $suwasahana = Suwasahana::find($id);

        if (!$suwasahana) {
            return redirect()->route('suwasahana.index')->with('error', 'Suwasahana application not found');
        }

        $action = $request->input('approval');

        if ($action === 'approve') {
            $suwasahana->accepted = 1;
            $suwasahana->user = Auth::user()->name;
            $suwasahana->save();

            return redirect()->route('suwasahana.index')->with('error', 'Suwasahana application registered successfully');

        } elseif ($action === 'reject') {
            $suwasahana->accepted = 2;
            $suwasahana->user = Auth::user()->name;
            $suwasahana->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['suwasahana'] = $suwasahana->id;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Rejected';

            SuwasahanaAssign::create($validatedAssign);

            return redirect()->route('suwasahana.index')->with('error', 'Suwasahana application rejected');

        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function edit($id)
    {
        $suwasahana = Suwasahana::with('membership')->find($id);
        return view('suwasahana.edit',compact('suwasahana'));
    }
    public function update(Request $request, $id)
    {
        if (!$this->authenticateApi()) {
            return response()->json(['error' => 'Failed to authenticate with external API'], 500);
        }
        $validatedData = $request->validate([
            'ABFvoucherno' => 'required',
            'Issue_Date' => 'required',
            'total_capital' => 'required',
            'total_interest' => 'required',
            'total_recovered_capital' => 'required',
            'total_recovered_interest' => 'required',
            'settled' => 'required',
            'settled_date' => 'nullable',
        ]);

        $suwasahana = Suwasahana::with('membership:id,name,enumber,regimental_number')
            ->find($id);

        $suwasahana->settled_date = $validatedData['settled_date'] ?? '';
        $suwasahana->settled = $validatedData['settled'] ?? '';
        $suwasahana->settled_type = 'Direct';
        $suwasahana->total_recovered_capital += $request->settle_amount ?? '0';
        $suwasahana->total_recovered_interest += $request->settle_interest ?? '0';
        $suwasahana->user = Auth::user()->name;
        $suwasahana->created_system = 'AFMS-Update';
        $suwasahana->save();

        $arPayloads[] = [
            "aRbatchId" => 'ARB008',
            "amount" => $request->settle_amount ?? 0,
//                        "credit" => $fullWithdrawal->fullWithdrawal->suwasahana_amount ?? 0,
//                        "debit" => 0,
            "transactionDate" => now()->toIso8601String(),
            "customer" => $suwasahana->membership->regimental_number . '-' . $suwasahana->membership->enumber ?? '000000000',
            "description" => 'Suwasahana Settlement '.date('n').'-'.date('Y'),
            "reference" => $suwasahana->membership->regimental_number .' Suwasahana Settlement',
            "comments" => 'Suwasahana Settlement',
            "transactioncCodeID" => 'LoanRecAss',
            "taxTypeID" => 1,
            "ar" => true,
            "gl" => false,
        ];
        if ($request->settle_interest>0){
            $arPayloads[] = [
                "aRbatchId" => 'ARB008',
                "amount" => $request->settle_interest ?? 0,
//                        "credit" => $fullWithdrawal->fullWithdrawal->suwasahana_arreas ?? 0,
//                        "debit" => 0,
                "transactionDate" => now()->toIso8601String(),
                "customer" => $suwasahana->membership->regimental_number . '-' . $suwasahana->membership->enumber ?? '000000000',
                "description" => 'Suwasahana Interest Settlement '.date('n').'-'.date('Y'),
                "reference" => $suwasahana->membership->regimental_number .' Suwasahana Interest Settlement',
                "comments" => 'Suwasahana Interest Settlement',
                "transactioncCodeID" => 'LoanRecInt',
                "taxTypeID" => 1,
                "ar" => true,
                "gl" => false,
            ];
        }
        $this->sendARBatchUpdate($arPayloads);


        return redirect()->route('memberships.show', ['membership' => $suwasahana->member_id])
            ->with('success', 'Suwasahana loan edited successfully');
    }
    private function sendARBatchUpdate(array $payloads)
    {
        $now = now();
        $filename = 'ar_batch_update_' . $now->format('Ymd_His') . '_' . uniqid() . '.log';
        $logPath = storage_path('logs/adjustments/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($logPath))) {
            mkdir(dirname($logPath), 0755, true);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ])->post($this->apiBaseUrl . '/api/Transaction/ARBatchUpdate', $payloads);

            $logData = [
                'timestamp' => $now->toDateTimeString(),
                'endpoint' => '/api/Transaction/ARBatchUpdate',
                'payload' => $payloads,
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response_body' => json_decode($response->body(), true) ?? $response->body(),
            ];

            file_put_contents($logPath, print_r($logData, true));

//            if (!$response->successful()) {
//                foreach ($payloads as $payload) {
//                    FailureLoanApi::create([
//                        'enumber' => explode('-', $payload['customer'])[0]?? $payload['transactioncCodeID'],
//                        'amount' => $payload['amount'],
//                        'reference' => $payload['reference'] ?? '',
//                        'reason' => 'API response failed: ' . $response->body(),
//                    ]);
//                }
//                return false;
//            }

            return true;

        } catch (\Exception $e) {
            $logData = [
                'timestamp' => $now->toDateTimeString(),
                'endpoint' => '/api/Transaction/ARBatchUpdate',
                'payload' => $payloads,
                'success' => false,
                'exception' => $e->getMessage(),
            ];

            file_put_contents($logPath, print_r($logData, true));

            foreach ($payloads as $payload) {
                FailureLoanApi::create([
                    'enumber' => explode('-', $payload['customer'])[0]?? $payload['transactioncCodeID'],
                    'amount' => $payload['amount'],
                    'reference' => $payload['reference'] ?? '',
                    'reason' => 'Exception: ' . $e->getMessage(),
                ]);
            }
            return false;
        }
    }
    public function repaymentView()
    {
        return view('suwasahana.repayment');
    }
//    public function uploadRepayment(Request $request)
//    {
//        $request->validate([
//            'xml_file' => 'required|file|mimes:xml',
//            'deposit_year' => 'required',
//            'deposit_month' => 'required',
//        ]);
//
//        $xmlFile = $request->file('xml_file');
//        $depositYear = $request->input('deposit_year');
//        $depositMonth = $request->input('deposit_month');
//        $xmlData = simplexml_load_file($xmlFile);
//
//        $successes = [];
//        $failures = [];
//
//        foreach ($xmlData->LIST_G_1->G_1->LIST_G_LOAN_TYPE->G_LOAN_TYPE->LIST_G_EMP_NO_OTHER->G_EMP_NO_OTHER as $repayment) {
//            $regNo = (string)$repayment->EMP_NO_OTHER;
//            $totalInstallment = (float)$repayment->TOTINST;
//            $installment = (float)$repayment->INSTAMT;
//            $interest = (float)$repayment->INTAMT;
//            $loanAmount = (float)$repayment->LOAN_AMOUNT;
//            $balance = (float)$repayment->BALANCE;
//
//            try {
//                $suwasahana = Suwasahana::with(['membership', 'recovery'])
//                    ->where('settled', 0)
//                    ->whereHas('membership', function ($query) use ($regNo) {
//                        $query->where('regimental_number', $regNo);
//                    })->firstOrFail();
//                $repaymentData = [
//                    'suwasahana_id' => $suwasahana->id,
//                    'payment_no' => $suwasahana->Rec_installments + 1,
//                    'capital' => $installment,
//                    'interest' => $interest,
//                    'month' => $depositMonth,
//                    'year' => $depositYear,
//                    'loan_balance' => $balance,
//                    'version' => 0,
//                    'payment_date' => now()->format('Y-m-d'),
//                    'currentuser' => Auth::user()->name,
//                    'created_system' => 'AFMS'
//                ];
//
//                $existingRepayment = SuwasahanaRecovery::where('suwasahana_id', $suwasahana->id)
//                    ->where('year', $depositYear)
//                    ->where('month', $depositMonth)
//                    ->first();
//
//                if ($existingRepayment) {
//                    $failures[] = [
//                        'suwasahanaId' => $suwasahana->id,
//                        'regimentalNo' => $regNo,
//                        'rank' => (string)$repayment->RANK,
//                        'name' => (string)$repayment->NAME,
//                        'capital' => $installment,
//                        'interest' => $interest,
//                        'error' => 'Duplicate entry for the month',
//                    ];
//                } else {
//                    SuwasahanaRecovery::create($repaymentData);
//
//                    if ($suwasahana->no_of_installments == $suwasahana->Rec_installments + 1){
//                        $suwasahana->total_recovered_capital += $installment;
//                        $suwasahana->total_recovered_interest += $interest;
//                        $suwasahana->Rec_installments += 1;
//                        $suwasahana->settled = 1;
//                        $suwasahana->settled_date = now()->format('Y-m-d');
//                        $suwasahana->settled_type  = 'Complete Installments';
//                        $suwasahana->created_system  = 'AFMS';
//                        $suwasahana->save();
//
//                    } else {
//                        $suwasahana->total_recovered_capital += $installment;
//                        $suwasahana->total_recovered_interest += $interest;
//                        $suwasahana->Rec_installments += 1;
//                        $suwasahana->created_system  = 'AFMS';
//                        $suwasahana->save();
//                    }
//
//                    $successes[] = [
//                        'suwasahanaId' => $suwasahana->id,
//                        'regimentalNo' => $regNo,
//                        'rank' => (string)$repayment->RANK,
//                        'name' => (string)$repayment->NAME,
//                        'capital' => $installment,
//                        'interest' => $interest,
////                        'currentuser' => Auth::user()->name,
////                        'created_system' => 'AFMS'
//                    ];
//                }
//            } catch (ModelNotFoundException $e) {
//                $failures[] = [
//                    'suwasahanaId' => 'NA',
//                    'regimentalNo' => $regNo,
//                    'rank' => (string)$repayment->RANK,
//                    'name' => (string)$repayment->NAME,
//                    'capital' => $installment,
//                    'interest' => $interest,
//                    'error' => 'No matching Suwasahana record found',
//                ];
//            }
//        }
//
//        $failuresArray = json_decode(json_encode($failures), true);
//
//        session(['failures' => $failuresArray]);
//
//        return view('suwasahana.repayment-report', compact('successes', 'failures'));
//    }
    public function uploadRepayment(Request $request)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml',
            'deposit_year' => 'required|integer',
            'deposit_month' => 'required|integer',
        ]);

        $depositYear = $request->input('deposit_year');
        $depositMonth = $request->input('deposit_month');

        $upload = 0;
        $failures = [];
        $arPayloads = [];
        $batch = [];

        $reader = new \XMLReader();
        $reader->open($request->file('xml_file')->getRealPath());
        if (!$this->authenticateApi()) {
            return response()->json(['error' => 'Failed to authenticate with external API'], 500);
        }
        while ($reader->read()) {
            if ($reader->nodeType === \XMLReader::ELEMENT && $reader->localName === 'G_EMP_NO_OTHER') {
                $node = simplexml_load_string($reader->readOuterXML());

                $batch[] = [
                    'regimentalNo' => (string) $node->EMP_NO_OTHER,
                    'installment' => (float) $node->INSTAMT,
                    'interest' => (float) $node->INTAMT,
                    'balance' => (float) $node->BALANCE,
                    'rank' => (string) $node->RANK,
                    'name' => (string) $node->NAME,
                ];

                if (count($batch) >= 1000) {
                    $this->processSuwasahanaBatch($batch, $depositYear, $depositMonth, $upload, $failures, $arPayloads);
                    $batch = [];
                }
            }
        }
        $reader->close();

        if (!empty($batch)) {
            $this->processSuwasahanaBatch($batch, $depositYear, $depositMonth, $upload, $failures, $arPayloads);
        }

        $this->sendARBatchUpdate($arPayloads);

        if (!empty($failures)) {
            SuwasahanaFailures::insert($failures);
        }

        $getFailures = SuwasahanaFailures::where('year', $depositYear)
            ->where('month', $depositMonth)
            ->orderByDesc('id')
            ->get();
        $totalFailed = count($failures);

        return view('suwasahana.repayment-report', compact('upload', 'getFailures',
            'totalFailed', 'depositYear', 'depositMonth'));
    }
    private function processSuwasahanaBatch(array $batch, int $year, int $month, int &$upload, array &$failures, array &$arPayloads)
    {
        $now = now();

        foreach ($batch as $item) {
            try {
                $suwasahana = Suwasahana::with('membership')
                    ->where('settled', 0)
                    ->whereHas('membership', fn($q) => $q->where('regimental_number', $item['regimentalNo']))
                    ->firstOrFail();
                $existing = SuwasahanaRecovery::where('Lid', $item['regimentalNo'])
                    ->where('year', $year)->where('month', $month)->first();

                if ($existing) {
                    $failures[] = [
                        'regimental_number' => $item['regimentalNo'],
                        'rank' => $item['rank'],
                        'name' => $item['name'],
                        'capital' => $item['installment'],
                        'interest' => $item['interest'],
                        'error' => 'Duplicate repayment entry',
                        'year' => $year,
                        'month' => $month,
                    ];
                    continue;
                }

                // Insert repayment
                SuwasahanaRecovery::create([
                    'Lid' => $item['regimentalNo'],
                    'payment_no' => $suwasahana->Rec_installments + 1,
                    'capital' => $item['installment'],
                    'interest' => $item['interest'],
                    'month' => $month,
                    'year' => $year,
                    'loan_balance' => $item['balance'],
                    'version' => 0,
                    'payment_date' => $now->format('Y-m-d'),
                    'currentuser' => Auth::user()->name,
                ]);

                // Update loan master
                $suwasahana->total_recovered_capital += $item['installment'];
                $suwasahana->total_recovered_interest += $item['interest'];
                $suwasahana->Rec_installments += 1;
                $suwasahana->created_system = 'AFMS';

                if ($suwasahana->no_of_installments == $suwasahana->Rec_installments) {
                    $suwasahana->settled = 1;
                    $suwasahana->settled_date = $now->format('Y-m-d');
                    $suwasahana->settled_type = 'Complete Installments';
                }
                $suwasahana->save();

                // Cashbook payloads
                $customer = $suwasahana->membership->regimental_number . '-' . $suwasahana->membership->enumber;

                $arPayloads[] = [
                    "aRbatchId" => 'ARB008',
                    "amount" => $item['installment'] ?? 0,
//                        "credit" => $fullWithdrawal->fullWithdrawal->suwasahana_amount ?? 0,
//                        "debit" => 0,
                    "transactionDate" => now()->toIso8601String(),
                    "customer" => $customer,
                    "description" => 'P&R Monthly Suwasahana Recovery '.$month.'-'.$year,
                    "reference" => $suwasahana->membership->regimental_number .' Suwasahana Recovery',
                    "comments" => 'P&R Monthly Suwasahana Recovery',
                    "transactioncCodeID" => 'LoanRecAss',
                    "taxTypeID" => 1,
                    "ar" => true,
                    "gl" => false,
                ];
                $arPayloads[] = [
                    "aRbatchId" => 'ARB008',
                    "amount" => $item['interest'] ?? 0,
//                        "credit" => $fullWithdrawal->fullWithdrawal->suwasahana_arreas ?? 0,
//                        "debit" => 0,
                    "transactionDate" => now()->toIso8601String(),
                    "customer" => $customer,
                    "description" => 'P&R Monthly Suwasahana Interest '.$month.'-'.$year,
                    "reference" => $suwasahana->membership->regimental_number .' Suwasahana Interest',
                    "comments" => 'P&R Monthly Suwasahana Interest',
                    "transactioncCodeID" => 'LoanRecInt',
                    "taxTypeID" => 1,
                    "ar" => true,
                    "gl" => false,
                ];
            } catch (ModelNotFoundException $e) {
                $failures[] = [
                    'regimental_number' => $item['regimentalNo'],
                    'rank' => $item['rank'],
                    'name' => $item['name'],
                    'capital' => $item['installment'],
                    'interest' => $item['interest'],
                    'error' => 'No matching Suwasahana loan record',
                    'year' => $year,
                    'month' => $month,
                ];
            } catch (\Exception $e) {
                $failures[] = [
                    'regimental_number' => $item['regimentalNo'],
                    'rank' => $item['rank'],
                    'name' => $item['name'],
                    'capital' => $item['installment'],
                    'interest' => $item['interest'],
                    'error' => 'Error: ' . $e->getMessage(),
                    'year' => $year,
                    'month' => $month,
                ];
            }
            $upload++;
        }
    }

    public function repaymentCSV($depositYear, $depositMonth)
    {
        $failures = SuwasahanaFailures::where('year', $depositYear)
            ->where('month', $depositMonth)
            ->orderByDesc('id')
            ->get();
        if (empty($failures)) {
            return redirect()->route('suwasahana-repayment')
                ->with('success', 'No failures data recorded');
        }

        $filename = "suwasahana_failures_{$depositYear}_{$depositMonth}_" . time() . ".csv";
        $filePath = storage_path("app/{$filename}");

        $file = fopen($filePath, 'w');
        fputcsv($file, ['reg_no', 'rank', 'name', 'capital', 'interest', 'error']);

        foreach ($failures as $failure) {
            fputcsv($file, [
                $failure->regimental_number,
                $failure->rank,
                $failure->name,
                $failure->capital,
                $failure->interest,
                $failure->error,
            ]);
        }

        fclose($file);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function destroy($id)
    {
        Suwasahana::find($id)->delete();

        return redirect()->route('suwasahana.index')
            ->with('success','Suwasahana details removed successfully');
    }

}
