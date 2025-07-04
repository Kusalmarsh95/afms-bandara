@extends('layouts.app')
@push('withdrawal-styles')
    <link rel="stylesheet" href="{{ asset('css/withdrawal.css') }}">
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
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-lg-12 margin-tb text-center p-0 mb-2">
                    <div class="card px-0 pt-4 pb-0 mt-3 mb-3">
                        <h3>Special Withdrawal - {{$membership->ranks->rank_name ?? '-'}} {{ $membership->name}}</h3>
                        <div id="progressForm" class="container">
                            <form action="{{ route('withdrawals.store', $membership->id) }}" method="POST">
                                @csrf
                                <ul id="progressbar">
                                    <li class="active" id="project"><strong>Eligibility</strong></li>
                                    <li id="development"><strong>Registration</strong></li>
                                    <li id="confirm"><strong>Calculation</strong></li>
                                </ul>
                                <br>
                                <fieldset>
                                    <div class="form-card justify-content-center">
                                        <div class="form-group ml-3">
                                            <div class="col-md-12">
                                                <h2 class="fs-title text-center">Eligibility Requirements</h2>
                                            </div>
                                            <div class="form-group row">
{{--                                                <div class="col-6 row">--}}
{{--                                                    <label for="gender" class="col-sm-4 col-form-label">Gender</label>--}}
{{--                                                    <div class="col-sm-8">--}}
{{--                                                        <select class="form-control" name="gender" data-live-search="true" id="gender">--}}
{{--                                                            <option value="" disabled selected>Select Gender</option>--}}
{{--                                                            <option value=1>Male</option>--}}
{{--                                                            <option value=0>Female</option>--}}
{{--                                                        </select>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
                                                <div class="col-6 row">
                                                    <label for="withdrawal_product" class="col-sm-5 col-form-label">Select Required Withdrawal</label>
                                                    <div class="col-sm-7">
                                                        @if(isset($withdrawalProducts))
                                                            <select id="withdrawal_product" name="withdrawal_product" class="form-control" data-live-search="true" >
                                                                <option disabled selected>Select Withdrawal Product</option>
                                                                @foreach($withdrawalProducts as $withdrawalProduct)
                                                                    <option value="{{ $withdrawalProduct->id }}" data-male-service="{{ $withdrawalProduct->male_service }}" data-female-service="{{ $withdrawalProduct->female_service }}"
                                                                            data-percentage="{{ $withdrawalProduct->percentage }}">{{ $withdrawalProduct->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
{{--                                                <div class="col-6 row">--}}
{{--                                                    <label for="enlisted_date" class="col-sm-5 col-form-label">Military Service</label>--}}
{{--                                                    <div class="col-sm-7">--}}
{{--                                                        <input type="text" id="enlisted_date" class="form-control" value="{{ $armyService.' Years' ?? '-' }}" readonly>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                                <div class="col-2 row" id="selectedServiceStatus">--}}
{{--                                                    --}}{{-- dynamically updated by JavaScript --}}
{{--                                                </div>--}}
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-6 row">
                                                    <label for="loan_settlement" class="col-sm-5 col-form-label">Amount</label>
                                                    <div class="col-sm-7">
                                                        <input type="text" id="loanSettlement" class="form-control" readonly>
                                                        <input type="hidden" name="special" value="1">
                                                    </div>
                                                </div>
                                                <div class="col-2 row" id="loanEligibility">
                                                    {{-- dynamically updated by JavaScript --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="button" name="next" id="nextButton" class="next action-button" value="Next"/>
                                </fieldset>
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
                                                                    <input type="text" id="application_reg_no" name="application_reg_no" class="form-control" readonly>
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
                                                    <div class="card-header">
                                                        <h5>Account Details</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group row">
                                                            <div class="col-6 row">
                                                                <label for="account_no" class="col-sm-4 col-form-label">Bank Account</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" id="account_no" name="account_no" class="form-control" value="{{$membership->account_no ?? '-' }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-6 row">
                                                                <label for="bank_code" class="col-sm-4 col-form-label">Bank code</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="bank_code" class="form-control" value="{{$membership->bank_code ?? '-' }}" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 row">
                                                                <label for="bank_name" class="col-sm-4 col-form-label">Bank Name </label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="bank_name" class="form-control" value="{{$membership->bank_name ?? '-' }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-6 row">
                                                                <label for="branch_code" class="col-sm-4 col-form-label">Branch Code</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="branch_code" class="form-control" value="{{$membership->branch_code ?? '-' }}" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 row">
                                                                <label for="branch_name" class="col-sm-4 col-form-label">Branch Name</label>
                                                                <div class="col-sm-8">
                                                                    <input type="text" name="branch_name" class="form-control" value="{{$membership->branch_name ?? '-' }}" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="button" id="nextButton2" name="next_2" class="next action-button" value="Next"/>
                                    <input type="button" name="previous" class="previous action-button-previous" value="Previous"/>
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
                                                                        <input type="hidden" name="arrest_interest" value="0">
                                                                    </div>
                                                                </div>
                                                            @elseif($loanApplication->loan->settled==1)
                                                                <div class="col-6 row">
                                                                    <div class="col-sm-5 text-warning">
                                                                        <span>Loan Already Settled</span>
                                                                        <input type="hidden" name="loan_due_cap" value="0">
                                                                        <input type="hidden" name="arrest_interest" value="0">
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
                                                                            <input type="text" name="arrest_interest" class="form-control" value="0">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="col-6 row">
                                                                <div class="col-sm-5 text-warning">
                                                                    <span>Did not register for a loan</span>
                                                                    <input type="hidden" name="loan_due_cap" value="0">
                                                                    <input type="hidden" name="arrest_interest" value="0">
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
                                                                            <input type="text" class="form-control" value=" {{ number_format($recoveredAmount) ? : 0}}" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <div class="col-6 row">
                                                                        <label for="suwasahana_amount" class="col-sm-6 col-form-label">Total Due Amount</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="hidden" name="suwasahana_amount" class="form-control" value=" {{ $dueSuwasahana ? : 0}}" readonly>
                                                                            <input type="text" class="form-control" value=" {{ number_format($dueSuwasahana ?? 0,2)}}" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-6 row">
                                                                        <label for="suwasahana_arreas" class="col-sm-6 col-form-label">Arrears Interest</label>
                                                                        <div class="col-sm-6">
                                                                            <input type="hidden" name="suwasahana_arreas" class="form-control" value="{{ $interestSuwasahana ?? 0 }}">
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
                                                                <label for="calculated_amount" class="col-sm-5 col-form-label">Calculated Amount</label>
                                                                <div class="col-sm-7">
                                                                    <input type="hidden" name="calculated_amount" class="form-control calculatedAmount">
                                                                    <input type="text" id="calculatedAmount" class="form-control" readonly>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-6 row">
                                                                <label for="eligible_amount" class="col-sm-5 col-form-label">Eligible to Withdraw</label>
                                                                <div class="col-sm-7">
                                                                    <input type="hidden" name="eligible_amount" class="form-control eligibleAmount">
                                                                    <input type="text" id="eligibleAmount" class="form-control" readonly>
                                                                </div>
                                                            </div>
                                                            <div class="col-6 row">
                                                                <label for="requested_amount" class="col-sm-5 col-form-label">Requested Amount</label>
                                                                <div class="col-sm-7">
                                                                    <input type="text" name="requested_amount" class="form-control" value="0.00">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="col-6 row">
                                                                <label for="purpose" class="col-sm-5 col-form-label">Purpose</label>
                                                                <div class="col-sm-7">
                                                                    <select name="purpose" id="purpose" class="form-control" data-live-search="true">
                                                                        <option disabled selected>Select Purpose</option>
                                                                        <option value=0 @if (!$loanApplication) disabled
                                                                                @elseif ($loanApplication)
                                                                                    @if(!$loanApplication->loan) disabled
                                                                                    @elseif($loanApplication->loan->settled==1) disabled
                                                                                    @endif
                                                                                @endif >Only for Settle loan</option>
                                                                        <option value=1>Settle loan and Withdraw / Withdraw</option>
                                                                        <option value=2 @if (!$suwasahana) disabled @endif >Only for settle suwasahana</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var withdrawalProductSelect = document.getElementById('withdrawal_product');
            var applicationRegNoInput = document.getElementsByName('application_reg_no')[0];
            var nextButton = document.getElementById('nextButton');
            var arrestInterestInput = document.querySelector('input[name="arrest_interest"]');

            let eligibleAmount = 0; // ✅ Declare in outer scope

            function formatAmount(number) {
                return number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            }

            nextButton.disabled = true;
            console.log('Arrest interest input found:', arrestInterestInput);
            function updateEligibleAmountDisplay(newEligibleAmount) {
                $('#eligibleAmount').val(formatAmount(newEligibleAmount));
                $('.eligibleAmount').val(newEligibleAmount.toFixed(2));
            }

            function recalcEligibleAmount() {
                var arrestInterest = parseFloat(arrestInterestInput?.value) || 0;
                var adjustedEligible = eligibleAmount - arrestInterest;

                updateEligibleAmountDisplay(adjustedEligible);

                var loanEligibility = document.getElementById('loanEligibility');
                var loanSettlementInput = $('#loanSettlement');

                if (adjustedEligible < 0) {
                    loanSettlementInput.val('Less Amount');
                    loanEligibility.innerHTML = '<span class="badge badge-danger mt-2" style="height: 20px!important"><i class="fas fa-times"></i> Dissatisfied</span>';
                    nextButton.disabled = true;
                } else {
                    loanSettlementInput.val('Acceptable Amount');
                    loanEligibility.innerHTML = '<span class="badge badge-success mt-2" style="height: 20px!important"><i class="fas fa-check"></i> Satisfied</span>';
                    nextButton.disabled = false;
                }
            }

            withdrawalProductSelect.addEventListener('change', function () {
                var selectedOption = this.options[this.selectedIndex];
                var percentage = selectedOption.getAttribute('data-percentage');
                var calculatedAmount = {{$fundBalance}} * (percentage / 100);

                // ✅ Update global eligibleAmount here
                eligibleAmount = calculatedAmount - {{ $dueSuwasahana + $dueAmount + $interestSuwasahana }};

                $('#calculatedAmount').val(calculatedAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                $('.calculatedAmount').val(calculatedAmount.toFixed(2));
                $('#eligibleAmount').val(eligibleAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                $('.eligibleAmount').val(eligibleAmount.toFixed(2));

                var loanSettlementInput = $('#loanSettlement');
                var loanEligibility = document.getElementById('loanEligibility');

                if (eligibleAmount < 0) {
                    loanSettlementInput.val('Less Amount');
                    loanEligibility.innerHTML = '<span class="badge badge-danger mt-2" style="height: 20px!important"><i class="fas fa-times"></i> Dissatisfied</span>';
                } else {
                    loanSettlementInput.val('Acceptable Amount');
                    loanEligibility.innerHTML = '<span class="badge badge-success mt-2" style="height: 20px!important"><i class="fas fa-check"></i> Satisfied</span>';
                }

                nextButton.disabled = loanEligibility.innerHTML.includes('Dissatisfied');

                updateApplicationRegNo();
            });
            if (arrestInterestInput) {
                arrestInterestInput.addEventListener('input', recalcEligibleAmount);
            }


            function updateApplicationRegNo() {
                // var selectedProductId = withdrawalProductSelect.value;
                var code = '{{$membership->regiments->regiment_code}}';
                var year = '{{now()->format('Y')}}';
                applicationRegNoInput.value = code + '/' + year + '/' + '{{$partialValue}}';

            }

            updateApplicationRegNo();

            var nextButton2 = document.getElementById('nextButton2');
            nextButton2.disabled = true;

            var receivedDateInput = document.getElementById('received_date');
            receivedDateInput.addEventListener('change', function () {
                nextButton2.disabled = !receivedDateInput.value;
            });

            var submitData = document.getElementById('submitData');
            submitData.disabled = true;

            var purposeInput = document.getElementById('purpose');
            purposeInput.addEventListener('change', function () {
                submitData.disabled = !purposeInput.value;
            });

            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);

        });
    </script>

@endsection
