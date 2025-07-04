@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Edit Details of Full Withdrawal</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-dark" href="{{ route('withdrawals.indexFull') }}">Back</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="withdrawal" action="{{ route('withdrawals.updateFull', $fullWithdrawal->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h5>Personal Details</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="application_reg_no" class="col-sm-5 col-form-label">Registration Number</label>
                            <div class="col-sm-7">
                                <input type="text" id="application_reg_no" name="application_reg_no" class="form-control" value="{{$fullWithdrawal->application_reg_no}}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="received_date" class="col-sm-5 col-form-label">Received Date</label>
                            <div class="col-sm-7">
                                <input type="date" id="received_date" class="form-control" value="{{ $fullWithdrawal->received_date ? (new DateTime($fullWithdrawal->received_date))->format('Y-m-d') : '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="regimental_number" class="col-sm-5 col-form-label">Regimental Number</label>
                            <div class="col-sm-7">
                                <input type="text" id="regimental_number" class="form-control" value="{{$fullWithdrawal->membership->regimental_number ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="unit" class="col-sm-5 col-form-label">Unit</label>
                            <div class="col-sm-7">
                                <input type="text" id="unit" class="form-control" value="{{ $fullWithdrawal->membership->units->unit_name ?? ''}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="regiment" class="col-sm-5 col-form-label">Regiment</label>
                            <div class="col-sm-7">
                                <input type="text" id="regiment" class="form-control" value="{{ $fullWithdrawal->membership->regiments->regiment_name ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="mobile_no" class="col-sm-5 col-form-label">Mobile Number</label>
                            <div class="col-sm-7">
                                <input type="text" id="mobile_no" class="form-control" value="{{ $fullWithdrawal->membership->telephone_mobile ?? ''}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="rank" class="col-sm-5 col-form-label">Rank</label>
                            <div class="col-sm-7">
                                <input type="text" id="rank" class="form-control" value="{{ $fullWithdrawal->membership->ranks->rank_name ?? '-'}}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="name" class="col-sm-5 col-form-label">Name</label>
                            <div class="col-sm-7">
                                <input type="text" id="name" class="form-control" value="{{ $fullWithdrawal->membership->name }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                @if($fullWithdrawal->membership->member_status_id != 3)
                    <div class="card-header">
                        <h5>Account Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-6 row">
                                <label for="account_no" class="col-sm-4 col-form-label">Bank Account</label>
                                <div class="col-sm-8">
                                    <input type="text" name="account_no" class="form-control" value="{{$fullWithdrawal->fullWithdrawal->account_no ?? '-' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6 row">
                                <label for="bank_code" class="col-sm-4 col-form-label">Bank code</label>
                                <div class="col-sm-8">
                                    @if(isset($banks))
                                        <select name="bank_code" class="form-control" data-live-search="true">
                                            <option value="" disabled>Select Bank Name</option>
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->id }}" {{ $fullWithdrawal->fullWithdrawal->bank_code == $bank->id ? 'selected' : '' }}>
                                                    {{ $bank->id }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 row">
                                <label for="bank_name" class="col-sm-4 col-form-label">Bank Name</label>
                                <div class="col-sm-8">
                                    @if(isset($banks))
                                        <select id="bank_name" name="bank_name" class="form-control" data-live-search="true">
                                            <option value="" disabled>Select Bank Name</option>
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->bank_name }}" {{ $fullWithdrawal->fullWithdrawal->bank_name == $bank->bank_name ? 'selected' : '' }}>
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
                                                <option value="{{ $branchCode->branch_code }}" {{ $fullWithdrawal->fullWithdrawal->branch_code == $branchCode->branch_code ? 'selected' : '' }}>
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
                                                <option value="{{ $branch->bank_branch_name }}" {{ $fullWithdrawal->fullWithdrawal->branch_name == $branch->bank_branch_name ? 'selected' : '' }}>
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
                <div class="card-header">
                    <h5>Loan Details</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="loan_due_cap" class="col-sm-4 col-form-label">Loan Due Capital</label>
                            <div class="col-sm-8">
                                <input type="hidden" name="loan_due_cap" class="form-control" value="{{ ($loanAmount-$recoveredAmount) ?: 0 }}">
                                <input type="text" class="form-control" value="{{ number_format(($loanAmount-$recoveredAmount),2) ?? '0.00' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="arrest_interest" class="col-sm-4 col-form-label">Arrears Interest</label>
                            <div class="col-sm-8">
                                <input type="text" name="arrest_interest" class="form-control" value="{{ number_format($fullWithdrawal->fullWithdrawal->arrest_interest,2) ?: 0 }}" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="suwasahana_amount" class="col-sm-4 col-form-label">Suwasahana Due</label>
                            <div class="col-sm-8">
                                <input type="hidden" name="suwasahana_amount" class="form-control" value="{{ $dueSuwasahana ?: 0 }}" readonly>
                                <input type="text" class="form-control" value="{{ number_format($dueSuwasahana,2) ?? '0.00' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="loan_10_month" class="col-sm-4 col-form-label">10 Month Loan</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $fullWithdrawal->membership->loan10month == 1 ? 'Yes' : 'No'  }}" readonly>
                            </div>
                        </div>
                    </div>
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
                                <input type="hidden" name="eligible_amount" class="form-control eligibleAmount">
                                <input type="text" id="eligibleAmount" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="other_deduction" class="col-sm-5 col-form-label">Other Deductions</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="otherDeductions" name="other_deduction">
                            </div>
                        </div>
                    </div>
                </div>
                @if ($fullWithdrawal->membership->member_status_id == 3)
                    <div class="card-header">
                        <h5>Nominee Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="col-6 row">
                            <label for="become_kia" class="col-sm-6 col-form-label">Become KIA Date <i class="nav-icon fas fa-exclamation-circle text-red"></i></label>
                            <div class="col-sm-6">
                                <input type="date" name="become_kia" class="form-control" value="{{$fullWithdrawal->fullWithdrawal->become_kia}}">
                            </div>
                        </div>

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
                                    @foreach($fullWithdrawal->membership->nominees as $nominee)
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
                <div class="card m-2">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-4 row">
                                <label for="fwd_to" class="col-sm-4 col-form-label">Forward To</label>
                                <div class="col-sm-8">
                                    @if(isset($users))
                                        <select name="fwd_to" class="form-control" data-live-search="true" required>
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
                                    <input type="text" name="fwd_to_reason" class="form-control" placeholder="Please provide a remark" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-sm btn-outline-info" >Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            var fundBalance = {{$fundBalance}};
            var dueSuwasahana = {{$dueSuwasahana}};
            var dueAmount = {{$dueAmount}};
            var other = {{$fullWithdrawal->fullWithdrawal->other_deduction}};

            $('#otherDeductions').val(other.toFixed(2));

            var originalEligibleAmount = fundBalance - (dueSuwasahana + dueAmount);
            var eligibleAmount = originalEligibleAmount;

            function updateEligibleAmount() {
                eligibleAmount = originalEligibleAmount;

                var otherDeductions = parseFloat(document.getElementById('otherDeductions').value) || 0;
                eligibleAmount -= otherDeductions;

                $('#eligibleAmount').val(eligibleAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                $('.eligibleAmount').val(eligibleAmount.toFixed(2));
            }

            updateEligibleAmount();

            var otherDeductionsInput = document.getElementById('otherDeductions');
            otherDeductionsInput.addEventListener('input', updateEligibleAmount);

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
            }, 3000);

        });
    </script>
@endsection


