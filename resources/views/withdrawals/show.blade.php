@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-9">
                        <h4>Withdrawal Details of Application -
                            @if ($partialWithdrawal->withdrawal_product == 1)
                                80% Withdrawal
                            @elseif ($partialWithdrawal->withdrawal_product == 2)
                                50% Withdrawal
                            @else
                                Unknown
                            @endif</h4>
                    </div>
                    <div class="col-sm-3">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-dark" href="{{ route('memberships.show', $partialWithdrawal->member_id) }}">Go to Member</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-default" href="{{ route('withdrawals.indexPartial') }}">Go to List</a>
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
        <div class="card">
            <div class="card-header">
                <h5>Personal Details</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-6 row">
                        <label for="application_reg_no" class="col-sm-5 col-form-label">Registration Number</label>
                        <div class="col-sm-7">
                            <input type="text" id="application_reg_no" name="application_reg_no" class="form-control" value="{{$partialWithdrawal->application_reg_no ?? '-'}}" readonly>
                        </div>
                    </div>
                    <div class="col-6 row">
                        <label for="received_date" class="col-sm-5 col-form-label">Registered Date</label>
                        <div class="col-sm-7">
                            <input type="date" id="registered_date" class="form-control" value="{{ $partialWithdrawal->registered_date ? (new DateTime($partialWithdrawal->registered_date))->format('Y-m-d') : '-' }}" readonly>
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
                            <input type="text" id="name" class="form-control" value="{{ $partialWithdrawal->membership->name ?? '-'}}" readonly>
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
                            <input type="text" name="account_no" class="form-control" value="{{$partialWithdrawal->withdrawal->account_no ?? '-' }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6 row">
                        <label for="bank_name" class="col-sm-4 col-form-label">Bank Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{ $partialWithdrawal->withdrawal->bank_name ?? '-'}}" readonly>
                        </div>
                    </div>
                    <div class="col-6 row">
                        <label for="branch_name" class="col-sm-4 col-form-label">Branch Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{ $partialWithdrawal->withdrawal->branch_name ?? '-'}}" readonly>
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
                            <input type="text" class="form-control" value="{{ number_format(($partialWithdrawal->withdrawal->loan_due_cap ?? '0'),2) }}" readonly>
                        </div>
                    </div>
                    <div class="col-6 row">
                        <label for="arrest_interest" class="col-sm-4 col-form-label">Arrears Interest</label>
                        <div class="col-sm-8">
                            <input type="text" name="arrest_interest" class="form-control" value="{{ number_format($partialWithdrawal->withdrawal->arrest_interest ?? '0',2) }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6 row">
                        <label for="suwasahana_amount" class="col-sm-4 col-form-label">Suwasahana Due</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{ number_format(($partialWithdrawal->withdrawal->suwasahana_amount+$partialWithdrawal->withdrawal->suwasahana_arreas) ?? '0',2) }}" readonly>
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
                        <input type="text" class="form-control"
                               value="@if(!$partialWithdrawal->withdrawal) NA
                                @elseif($partialWithdrawal->withdrawal->purpose == 0) Only for Settle the loan
                                @elseif($partialWithdrawal->withdrawal->purpose == 1) Settle and Withdraw / Withdraw
                                @elseif($partialWithdrawal->withdrawal->purpose == 2) Only for Settle the suwasahana
                                @endif" readonly>
                    </div>
                </div> <div class="form-group row">
                    <div class="col-6 row">
                        <label for="fund_balance" class="col-sm-5 col-form-label">Fund Balance</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" value="{{ number_format($partialWithdrawal->withdrawal->fund_balance ?? '0',2) }}" readonly>
                        </div>
                    </div>
                    <div class="col-6 row">
                        <label for="calculated_amount" class="col-sm-5 col-form-label">Calculated Amount</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" value="{{ number_format($partialWithdrawal->withdrawal->calculated_amount ?? '0.00',2) }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6 row">
                        <label for="eligible_amount" class="col-sm-5 col-form-label">Eligible to Withdraw</label>
                        <div class="col-sm-7">
                            <input type="text" name="eligible_amount" class="form-control" value="{{ number_format($partialWithdrawal->withdrawal->eligible_amount ?? '0.00',2)}}" readonly>
                        </div>
                    </div>
                    <div class="col-6 row">
                        <label for="requested_amount" class="col-sm-5 col-form-label">Requested Amount</label>
                        <div class="col-sm-7">
                            <input type="text" name="requested_amount" class="form-control" value="{{ number_format($partialWithdrawal->withdrawal->requested_amount ?? '0.00', 2)}}" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6 row">
                        <label for="total_withdraw_amount" class="col-sm-5 col-form-label">Withdraw Amount</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" value="{{ number_format($partialWithdrawal->withdrawal->total_withdraw_amount ?? '0.00',2)}}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);
        });
    </script>
@endsection


