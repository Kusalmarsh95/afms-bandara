@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Full Withdrawal Application Details</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-dark" href="{{ route('memberships.show', $fullWithdrawal->member_id) }}">Go To Member</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-light" href="{{ route('withdrawals.indexFull') }}">Go To List</a>
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
            <div class="card-header">
                <h5>Account Details</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-6 row">
                        <label for="account_no" class="col-sm-4 col-form-label">Bank Account</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{$fullWithdrawal->fullWithdrawal->account_no ?? '-' }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6 row">
                        <label for="bank_name" class="col-sm-4 col-form-label">Bank Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{$fullWithdrawal->fullWithdrawal->bank_name ?? '-' }}" readonly>
                        </div>
                    </div>
                    <div class="col-6 row">
                        <label for="branch_name" class="col-sm-4 col-form-label">Branch Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{$fullWithdrawal->fullWithdrawal->branch_name ?? '-' }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
            @if ($fullWithdrawal->membership->member_status_id === 3)
                <div class="card-header">
                    <h5>Nominee Details</h5>
                </div>
                <div class="card-body">
                    <div class="col-6 row mb-2">
                        <label for="become_kia" class="col-sm-6 col-form-label">KIA Reported Date</label>
                        <div class="col-sm-6">
                            <input type="date" name="become_kia" class="form-control" value="{{$fullWithdrawal->fullWithdrawal->become_kia}}">
                        </div>
                    </div>
                    @if ($fullWithdrawal->membership->nominees->count() > 0)
                        @if($fullWithdrawal->membership->nominees->where('accepted', 1)->where('enabled', 1)->count() > 0)
                            <table class="table table-bordered" id="nominee">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>NIC</th>
                                    <th>Relationship</th>
                                    <th>%</th>
                                    <th>Account No</th>
                                    <th>Bank</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($fullWithdrawal->membership->nominees as $nominee)
                                    <tr>
                                        <td>{{ $nominee->name ?? '-' }}</td>
                                        <td>{{ $nominee->nomineenic ?? '-' }}</td>
                                        <td>{{ $nominee->relationship ? $nominee->relationship->relationship_name : '-' }}</td>
                                        <td>{{ $nominee->percentage ?? '-' }}</td>
                                        <td>{{ $nominee->details->account_number ?? '-' }}</td>
                                        <td>{{ $nominee->details->bank_name ?? '-' }}</td>
                                        <td>{{ number_format($nominee->details->paid_amount,2 ) ?? '-' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
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
            <div class="card-header">
                <h5>Loan Details</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-6 row">
                        <label for="loan_due_cap" class="col-sm-4 col-form-label">Loan Due Capital</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{ number_format(($fullWithdrawal->fullWithdrawal->loan_due_cap),2) ?? '0.00' }}" readonly>
                        </div>
                    </div>
                    <div class="col-6 row">
                        <label for="arrest_interest" class="col-sm-4 col-form-label">Arrears Interest</label>
                        <div class="col-sm-8">
                            <input type="text" name="arrest_interest" class="form-control" value="{{ number_format($fullWithdrawal->fullWithdrawal->arrest_interest,2) ?: 0 }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6 row">
                        <label for="suwasahana_amount" class="col-sm-4 col-form-label">Suwasahana Due</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" value="{{ number_format($fullWithdrawal->fullWithdrawal->suwasahana_amount,2) ?? '0.00' }}" readonly>
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
                            <input type="text" class="form-control" value="{{ number_format($fullWithdrawal->fullWithdrawal->fund_balance,2) ? : 0}}" readonly>
                        </div>
                    </div>
                    <div class="col-6 row">
                        <label for="eligible_amount" class="col-sm-5 col-form-label">Eligible to Withdraw</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" value=" {{ number_format($fullWithdrawal->fullWithdrawal->eligible_amount,2) ?? 0 }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-6 row">
                        <label for="other_deduction" class="col-sm-5 col-form-label">Other Deductions</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" name="other_deduction" value="{{ number_format($fullWithdrawal->fullWithdrawal->other_deduction,2) ?? 0}}" readonly>
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


