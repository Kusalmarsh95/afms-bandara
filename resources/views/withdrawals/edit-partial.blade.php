@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Edit Details of
                            @if ($partialWithdrawal->withdrawal_product == 1)
                                80% Withdrawal
                            @elseif ($partialWithdrawal->withdrawal_product == 2)
                                50% Withdrawal
                            @else
                                Other Withdrawal
                            @endif</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-dark" href="{{ route('withdrawals.indexPartial') }}">Back</a>
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
        <form id="withdrawal" action="{{ route('withdrawals.updatePartial', $partialWithdrawal->id) }}" method="POST">
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
                                <input type="text" id="application_reg_no" name="application_reg_no" class="form-control" value="{{$partialWithdrawal->application_reg_no}}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="received_date" class="col-sm-5 col-form-label">Received Date</label>
                            <div class="col-sm-7">
                                <input type="date" id="received_date" class="form-control" value="{{ $partialWithdrawal->received_date ? (new DateTime($partialWithdrawal->received_date))->format('Y-m-d') : '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="regimental_number" class="col-sm-5 col-form-label">Regimental Number</label>
                            <div class="col-sm-7">
                                <input type="text" id="regimental_number" class="form-control" value="{{$partialWithdrawal->membership->regimental_number ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="unit" class="col-sm-5 col-form-label">Unit</label>
                            <div class="col-sm-7">
                                <input type="text" id="unit" class="form-control" value="{{ $partialWithdrawal->membership->units->unit_name ?? ''}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="regiment" class="col-sm-5 col-form-label">Regiment</label>
                            <div class="col-sm-7">
                                <input type="text" id="regiment" class="form-control" value="{{ $partialWithdrawal->membership->regiments->regiment_name ?? ''}}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="mobile_no" class="col-sm-5 col-form-label">Mobile Number</label>
                            <div class="col-sm-7">
                                <input type="text" id="mobile_no" class="form-control" value="{{ $partialWithdrawal->membership->telephone_mobile ?? ''}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="rank" class="col-sm-5 col-form-label">Rank</label>
                            <div class="col-sm-7">
                                <input type="text" id="rank" class="form-control" value="{{ $partialWithdrawal->membership->ranks->rank_name ?? '-'}}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="name" class="col-sm-5 col-form-label">Name</label>
                            <div class="col-sm-7">
                                <input type="text" id="name" class="form-control" value="{{ $partialWithdrawal->membership->name }}" readonly>
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
                                <input type="text" name="account_no" class="form-control" value="{{$partialWithdrawal->withdrawal->account_no ?? '-' }}">
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
                                            <option value="{{ $bank->id }}" {{ $partialWithdrawal->withdrawal->bank_code == $bank->id ? 'selected' : '' }}>
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
                                            <option value="{{ $bank->bank_name }}" {{ $partialWithdrawal->withdrawal->bank_name == $bank->bank_name ? 'selected' : '' }}>
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
                                            <option value="{{ $branchCode->branch_code }}" {{ $partialWithdrawal->withdrawal->branch_code == $branchCode->branch_code ? 'selected' : '' }}>
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
                                            <option value="{{ $branch->bank_branch_name }}" {{ $partialWithdrawal->withdrawal->branch_name == $branch->bank_branch_name ? 'selected' : '' }}>
                                                {{ $branch->bank_branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <h5>Loan Details</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="loan_due_cap" class="col-sm-4 col-form-label">Loan Due Capital</label>
                            <div class="col-sm-8">
                                <input type="hidden" name="loan_due_cap" class="form-control" value="{{ $dueAmount ?: 0 }}">
                                <input type="text" class="form-control" value="{{ number_format($dueAmount,2) ?? '0.00' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="arrest_interest" class="col-sm-4 col-form-label">Arrears Interest</label>
                            <div class="col-sm-8">
                                <input type="text" name="arrest_interest" class="form-control" value="{{ number_format($partialWithdrawal->withdrawal->arrest_interest,2) ?: 0 }}" >
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
                                <input type="text" class="form-control" value="{{ $partialWithdrawal->membership->loan10month == 1 ? 'Yes' : 'No'  }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <h5>Withdrawal Details</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-12 row">
                            <label for="purpose" class="col-sm-2 col-form-label">Purpose</label>
                            <select name="purpose" id="purpose" class="form-control" data-live-search="true">
                                <option disabled selected>Select Purpose</option>
                                <option value=0 @if($partialWithdrawal->withdrawal->purpose == 0) selected @endif>Only for Settle loan</option>
                                <option value=1 @if($partialWithdrawal->withdrawal->purpose == 1) selected @endif>Settle and Withdraw / Withdraw</option>
                                <option value=2 @if($partialWithdrawal->withdrawal->purpose == 2) selected @endif>Only for Settle suwasahana</option>
                            </select>
                        </div>
                    </div> <div class="form-group row">
                        <div class="col-6 row">
                            <label for="fund_balance" class="col-sm-5 col-form-label">Fund Balance</label>
                            <div class="col-sm-7">
                                <input type="hidden" name="fund_balance" class="form-control" value="{{ $fundBalance ?: 0 }}" readonly>
                                <input type="text" class="form-control" value="{{ number_format($fundBalance,2) ?? '0.00' }}" readonly>
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
                                <input type="text" name="requested_amount" class="form-control" value="{{ number_format($partialWithdrawal->withdrawal->requested_amount, 2) ?: 0 }}">
                            </div>
                        </div>
                    </div>
                </div>
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

            var percentage = {{$withdrawalProduct->percentage}};
            var calculatedAmount = {{$fundBalance}} * (percentage/100);
            console.log({{$fundBalance}})
            var eligibleAmount = calculatedAmount - {{ $dueSuwasahana + $dueAmount}};


            $('#calculatedAmount').val(calculatedAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
            $('.calculatedAmount').val(calculatedAmount.toFixed(2));
            $('#eligibleAmount').val(eligibleAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
            $('.eligibleAmount').val(eligibleAmount.toFixed(2));


            setTimeout(function () {
                $('.alert').fadeOut();
            }, 3000);

        });
    </script>
@endsection


