@extends('layouts.app')
@push('withdrawal-styles')
    <link rel="stylesheet" href="{{ asset('css/fullwithdrawal.css') }}">
@endpush
@push('withdrawal-js')
    <script src="{{ asset('js/withdrawal.js') }}" defer></script>
@endpush
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right m-1">
                        <li class="breadcrumb-item">
                            <a href="{{ route('memberships.show', $membership->id) }}" class="btn btn-sm btn-dark">Back</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problem<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container">
        @if($membership->member_status_id !== 8)
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-12 margin-tb text-center p-0 mb-2">
                        <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                            <h3>Full Withdrawal - {{$membership->ranks->rank_name ?? '-'}} {{ $membership->name}}</h3>
                            <div id="progressForm" class="container">
                                <form action="{{ route('withdrawals.storeFull', $membership->id) }}" method="POST">
                                    @csrf
                                    <ul id="progressbar">
                                        <li id="development"><strong>Registration</strong></li>
                                        <li id="confirm"><strong>Calculation</strong></li>
                                    </ul>
                                    <br>
                                    <fieldset>
                                        <div class="form-card">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h2 class="fs-title text-center">Withdrawal Registration</h2>
                                                </div>
                                                <div class="card-body">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>Personal Details</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group row">
                                                                <div class="col-6 row">
                                                                    <label for="application_reg_no" class="col-sm-5 col-form-label">Registration Number</label>
                                                                    <div class="col-sm-7">
                                                                        <input type="text" id="application_reg_no" name="application_reg_no" class="form-control"
                                                                               value="{{$membership->regiments->regiment_code}}/{{now()->format('Y')}}/{{$fullValue}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 row">
                                                                    <label for="received_date" class="col-sm-5 col-form-label">Received Date <i class="nav-icon fas fa-exclamation-circle text-red"></i></label>
                                                                    <div class="col-sm-7">
                                                                        <input type="date" id="received_date" name="received_date" class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-6 row">
                                                                    <label for="regimental_number" class="col-sm-5 col-form-label">Regimental Number</label>
                                                                    <div class="col-sm-7">
                                                                        <input type="text" id="regimental_number" name="regimental_number" class="form-control" value="{{ $membership->regimental_number ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 row">
                                                                    <label for="unit" class="col-sm-5 col-form-label">Unit</label>
                                                                    <div class="col-sm-7">
                                                                        <input type="text" id="unit" class="form-control" value="{{ $membership->units->unit_name ?? '-'}}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-6 row">
                                                                    <label for="regiment" class="col-sm-5 col-form-label">Regiment</label>
                                                                    <div class="col-sm-7">
                                                                        <input type="text" id="regiment" class="form-control" value="{{ $membership->regiments->regiment_name ?? '-'}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 row">
                                                                    <label for="mobile_no" class="col-sm-5 col-form-label">Mobile Number</label>
                                                                    <div class="col-sm-7">
                                                                        <input type="text" id="mobile_no" class="form-control" value="{{ $membership->telephone_mobile ? : 'NA'}}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-6 row">
                                                                    <label for="rank" class="col-sm-5 col-form-label">Rank</label>
                                                                    <div class="col-sm-7">
                                                                        <input type="text" id="rank" class="form-control" value="{{ $membership->ranks->rank_name ?? '-' }}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 row">
                                                                    <label for="name" class="col-sm-5 col-form-label">Name</label>
                                                                    <div class="col-sm-7">
                                                                        <input type="text" id="name" class="form-control" value="{{ $membership->name ?? '-'}}" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if ($membership->member_status_id != 3)
                                                            <div class="card-header">
                                                                <h5>Account Details</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="form-group row">
                                                                    <div class="col-6 row">
                                                                        <label for="account_no" class="col-sm-4 col-form-label">Bank Account <i class="nav-icon fas fa-exclamation-circle text-red"></i></label>
                                                                        <div class="col-sm-8">
                                                                            <input type="text" id="account_no" name="account_no" class="form-control" value="{{$membership->account_no ?? '' }}">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-6 row">
                                                                        <label for="bank_code" class="col-sm-4 col-form-label">Bank code </label>
                                                                        <div class="col-sm-8">
                                                                            @if(isset($banks))
                                                                                <select name="bank_code" class="form-control" data-live-search="true">
                                                                                    <option value="" disabled>Select Bank Code </option>
                                                                                    @foreach($banks as $bank)
                                                                                        <option value="{{ $bank->id }}" {{ $membership->bank_code == $bank->id ? 'selected' : '' }}>
                                                                                            {{ $bank->id }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6 row">
                                                                        <label for="bank_name" class="col-sm-4 col-form-label">Bank Name <i class="nav-icon fas fa-exclamation-circle text-red"></i></label>
                                                                        <div class="col-sm-8">
                                                                            @if(isset($banks))
                                                                                <select id="bank_name" name="bank_name" class="form-control" data-live-search="true">
                                                                                    <option value="" disabled>Select Bank Name</option>
                                                                                    @foreach($banks as $bank)
                                                                                        <option value="{{ $bank->bank_name }}" {{ $membership->bank_name == $bank->bank_name ? 'selected' : '' }}>
                                                                                            {{ $bank->bank_name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-6 row">
                                                                        <label for="branch_code" class="col-sm-4 col-form-label">Branch Code</label>
                                                                        <div class="col-sm-8">
                                                                            @if(isset($branchCodes))
                                                                                <select id="branch_code" name="branch_code" class="form-control" data-live-search="true">
                                                                                    <option selected>Select Branch Code</option>
                                                                                    @foreach($branchCodes as $branchCode)
                                                                                        <option value="{{ $branchCode->branch_code }}" {{ $membership->branch_code == $branchCode->branch_code ? 'selected' : '' }}>
                                                                                            {{ $branchCode->branch_code }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6 row">
                                                                        <label for="branch_name" class="col-sm-4 col-form-label">Branch Name</label>
                                                                        <div class="col-sm-8">
                                                                            @if(isset($branches))
                                                                                <select id="branch_name" name="branch_name" class="form-control" data-live-search="true">
                                                                                    <option selected>Select Branch Name</option>
                                                                                    @foreach($branches as $branch)
                                                                                        <option value="{{ $branch->bank_branch_name }}" {{ $membership->branch_name == $branch->bank_branch_name ? 'selected' : '' }}>
                                                                                            {{ $branch->bank_branch_name }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        @endif

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="button" id="nextButton2" name="next_2" class="next action-button" value="Next"/>
{{--                                        <input type="button" name="previous" class="previous action-button-previous" value="Previous"/>--}}
                                    </fieldset>
                                    <fieldset>
                                        <div id="devStage" class="form-card">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h2 class="fs-title text-center">Withdrawal Calculation</h2>
                                                </div>
                                                <div class="card-body">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5>Loan Details</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            @if ($loanApplication)
                                                                @if(!$loanApplication->loan)
                                                                    <div class="col-6 row">
                                                                        <div class="col-sm-5 text-warning">
                                                                            <span>Loan Not Approved</span>
                                                                            <input type="hidden" name="loan_due_cap" value="0">
                                                                            <input type="hidden" name="arrest_interest" id="arrestInterest" value="0">
                                                                        </div>
                                                                    </div>
                                                                @elseif($loanApplication->loan->settled==1)
                                                                    <div class="col-6 row">
                                                                        <div class="col-sm-5 text-warning">
                                                                            <span>Loan Already Settled</span>
                                                                            <input type="hidden" name="loan_due_cap" value="0">
                                                                            <input type="hidden" name="arrest_interest" id="arrestInterest" value="0">
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="form-group row">
                                                                        <div class="col-6 row">
                                                                            <label class="col-sm-6 col-form-label">Loan Capital</label>
                                                                            <div class="col-sm-6">
                                                                                <input type="text" class="form-control" value=" {{number_format($loanApplication->loan->total_capital,2) ? : 0}}" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-6 row">
                                                                            <label class="col-sm-6 col-form-label">Recovered Capital</label>
                                                                            <div class="col-sm-6">
                                                                                <input type="text" class="form-control" value=" {{ number_format($loanApplication->loan->total_recovered_capital,2) ? : 0}}" readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-6 row">
                                                                            <label for="loan_due_cap" class="col-sm-6 col-form-label">Total Due Capital</label>
                                                                            <div class="col-sm-6">
                                                                                <input type="hidden" name="loan_due_cap" class="form-control" value=" {{ $dueAmount ? : 0}}" readonly>
                                                                                <input type="text" class="form-control" value=" {{ number_format($dueAmount,2) ? : 0}}" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-6 row">
                                                                            <label for="arrest_interest" class="col-sm-6 col-form-label">Arrears Interest</label>
                                                                            <div class="col-sm-6">
                                                                                <input type="text" name="arrest_interest" id="arrestInterest" class="form-control" value="0.00">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="col-6 row">
                                                                    <div class="col-sm-5 text-warning">
                                                                        <span>Did not register for a loan</span>
                                                                        <input type="hidden" name="loan_due_cap" value="0">
                                                                        <input type="hidden" name="arrest_interest" id="arrestInterest" value="0">
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="card-header">
                                                            <h5>Suwasahana Loan Details</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            @if ($suwasahana)
                                                                @if($suwasahana->settled==1)
                                                                    <div class="col-6 row">
                                                                        <div class="col-sm-5 text-warning">
                                                                            <span>Suwasahana Loan Already Settled</span>
                                                                            <input type="hidden" name="suwasahana_amount" value="0">
                                                                            <input type="hidden" name="suwasahana_arreas" id="suwasahanaArreas" value="0">
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="form-group row">
                                                                        <div class="col-6 row">
                                                                            <label class="col-sm-6 col-form-label">Loan Amount</label>
                                                                            <div class="col-sm-6">
                                                                                <input type="text" class="form-control" value=" {{ number_format($loanAmount,2) ? : 0}}" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-6 row">
                                                                            <label class="col-sm-6 col-form-label">Recovered Amount</label>
                                                                            <div class="col-sm-6">
                                                                                <input type="text" class="form-control" value=" {{ number_format($recoveredAmount,2) ? : 0}}" readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <div class="col-6 row">
                                                                            <label for="suwasahana_amount" class="col-sm-6 col-form-label">Suwasahana Due Amount</label>
                                                                            <div class="col-sm-6">
                                                                                <input type="hidden" name="suwasahana_amount" class="form-control" value=" {{ $dueSuwasahana ? : 0}}" readonly>
                                                                                <input type="text" class="form-control" value=" {{ number_format($dueSuwasahana,2) ? : 'No'}}" readonly>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-6 row">
                                                                            <label for="suwasahana_arreas" class="col-sm-6 col-form-label">Arrears Interest</label>
                                                                            <div class="col-sm-6">
                                                                                <input type="hidden" name="suwasahana_arreas" class="form-control" value="{{ $interestSuwasahana }}">
                                                                                <input type="text" class="form-control" value="{{ number_format($interestSuwasahana ?? 0,2) }}" readonly>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="col-6 row">
                                                                    <div class="col-sm-12 text-warning">
                                                                        <span>Did not register for a suwasahana loan</span>
                                                                        <input type="hidden" name="suwasahana_amount" value="0">
                                                                        <input type="hidden" name="suwasahana_arreas" value="0">
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="card-header">
                                                            <h5>Withdrawal Details</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group row">
                                                                <div class="col-6 row">
                                                                    <label for="fund_balance" class="col-sm-5 col-form-label">Fund Balance</label>
                                                                    <div class="col-sm-7">
                                                                        <input type="hidden" name="fund_balance" class="form-control" value="{{ $fundBalance ? : 0}}" readonly>
                                                                        <input type="text" class="form-control" value="{{ number_format($fundBalance,2) ? : 0}}" readonly>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6 row">
                                                                    <label for="eligible_amount" class="col-sm-5 col-form-label">Eligible to Withdraw</label>
                                                                    <div class="col-sm-7">
                                                                        <input type="text" id="eligibleAmount" name="eligible_amount" class="form-control" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <div class="col-6 row">
                                                                    <label for="other_deduction" class="col-sm-5 col-form-label">Other Deductions</label>
                                                                    <div class="col-sm-7">
                                                                        <input type="text" id="otherDeductions" name="other_deduction" class="form-control" value="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @if ($membership->member_status_id == 3)
                                                            <div class="card-header">
                                                                <h5>Nominee Details</h5>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="col-6 row">
                                                                    <label for="become_kia" class="col-sm-6 col-form-label">Become KIA Date <i class="nav-icon fas fa-exclamation-circle text-red"></i></label>
                                                                    <div class="col-sm-6">
                                                                        <input type="date" name="become_kia" class="form-control">
                                                                    </div>
                                                                </div>
{{--                                                                <div class="pull-left mb-2">--}}
{{--                                                                    <a class="btn btn-sm btn-outline-success" href="{{ route('nominees.create', ['membership_id' => $fullWithdrawal->membership->id]) }}"> Add Nominee</a>--}}
{{--                                                                </div>--}}
                                                                @if ($fullWithdrawal->membership->nominees->count() > 0)
                                                                    @if($fullWithdrawal->membership->nominees->where('accepted', 1)->where('enabled', 1)->count() > 0)
                                                                        <div class="card-body" id="hr">
                                                                            <div class="row">
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <strong>Name</strong>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <div class="form-group">
                                                                                        <strong>NIC</strong>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <div class="form-group">
                                                                                        <strong>Relationship</strong>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-1">
                                                                                    <div class="form-group">
                                                                                        <strong>%</strong>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <div class="form-group">
                                                                                        <strong>Amount</strong>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            @foreach($membership->nominees as $nominee)
                                                                                <div class="row">
                                                                                    <div class="col-md-4">
                                                                                        <div class="form-group">
                                                                                            <input type="hidden" class="form-control" name="nominee_id[]" value="{{ $nominee->id }}">
                                                                                            <input type="text" class="form-control" name="name[]" value="{{ $nominee->name }}" readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control" value="{{ $nominee->nomineenic }}" readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control" value="{{ $nominee->relationship->relationship_name }}" readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-1">
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control percentage" id="percentage" name="percentage[]" value="{{ $nominee->percentage }}">
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div class="form-group">
                                                                                            <input type="text" class="form-control paid_amount" id="paidAmount" name="paid_amount[]" readonly>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                @else
                                                                    <div class="col-6 row">
                                                                        <div class="col-sm-5 text-warning">
                                                                            <span>Not nominated</span>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="form-group row">
                                                                <div class="col-4 row">
                                                                    <label for="fwd_to" class="col-sm-4 col-form-label">For Approval</label>
                                                                    <div class="col-sm-8">
                                                                        @if(isset($users))
                                                                            <select name="fwd_to" id="assign" class="form-control" data-live-search="true">
                                                                                <option disabled selected>Assign a Officer</option>
                                                                                @foreach($users as $user)
                                                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-8 row">
                                                                    <label for="fwd_to_reason" class="col-sm-2 col-form-label">Remark</label>
                                                                    <div class="col-sm-10">
                                                                        <input type="text" name="fwd_to_reason" class="form-control" value="To check and process the application">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="submit" id="submitData" name="next" class="next action-button" value="Submit"/>
                                        <input type="button" name="previous" class="previous action-button-previous" value="Previous"/>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="container-fluid">
                <div class="card">
                    <h4 class="text-center m-3">Account already closed!</h4>
                </div>

            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var nextButton2 = document.getElementById('nextButton2');
            var receivedDateInput = document.getElementById('received_date');
            var accountNoInput = document.getElementById('account_no');

            nextButton2.addEventListener('click', function() {
                if (!receivedDateInput.value) {
                    alert('Received date cannot be empty');
                    event.preventDefault();
                    location.reload();
                } else if (!accountNoInput.value) {
                    alert('Account number cannot be empty');
                    event.preventDefault();
                    location.reload();
                }
            });

            var fundBalance = {{$fundBalance}};
            var dueSuwasahana = {{$dueSuwasahana}};
            var dueAmount = {{$dueAmount}};
            var suwashanaInterest = {{$interestSuwasahana}};

            var originalEligibleAmount = fundBalance - (dueSuwasahana + dueAmount +suwashanaInterest);
            var eligibleAmount = originalEligibleAmount;

            function updateEligibleAmount() {
                eligibleAmount = originalEligibleAmount;

                var otherDeductions = parseFloat(document.getElementById('otherDeductions').value) || 0;
                var arrestInterest = parseFloat(document.getElementById('arrestInterest').value) || 0;
                eligibleAmount -= (otherDeductions + arrestInterest);

                $('#eligibleAmount').val(eligibleAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                $('.eligibleAmount').val(eligibleAmount.toFixed(2));

                // Update paid_amount whenever eligibleAmount changes
                updatePaidAmount();
            }

            updateEligibleAmount();

            var otherDeductionsInput = document.getElementById('otherDeductions');
            var arrestInterestInput = document.getElementById('arrestInterest');
            otherDeductionsInput.addEventListener('input', updateEligibleAmount);
            arrestInterestInput.addEventListener('input', updateEligibleAmount);

            // Function to calculate and update paid_amount
            function updatePaidAmount() {
                var eligibleAmount = parseFloat(document.getElementById('eligibleAmount').value.replace(/,/g, '')) || 0;
                var percentageInputs = document.getElementsByClassName('percentage');
                var paidAmountInputs = document.getElementsByClassName('paid_amount');

                for (var i = 0; i < percentageInputs.length; i++) {
                    var percentage = parseFloat(percentageInputs[i].value) || 0;
                    var paidAmount = eligibleAmount * (percentage / 100);
                    paidAmountInputs[i].value = paidAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                }
            }

            // Call the updatePaidAmount function when any percentage input changes
            var percentageInputs = document.getElementsByClassName('percentage');
            for (var i = 0; i < percentageInputs.length; i++) {
                percentageInputs[i].addEventListener('input', updatePaidAmount);
            }
            updatePaidAmount();

            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);

        });
    </script>

@endsection
