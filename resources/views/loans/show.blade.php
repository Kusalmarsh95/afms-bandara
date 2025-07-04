@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-9">
                        <h4>Loan Details of {{$loan->application_reg_no}} - {{ $loan->membership->ranks->rank_name ?? '-' }} {{ $loan->membership->name ?? '-' }}
                            <label class="badge
                                @if ($loan->loan->settled == 1)
                                    badge-success
                                @else
                                    badge-warning
                                @endif">
                                @if ($loan->loan->settled == 1)
                                    Settled
                                @elseif ($loan->processing == 0)
                                    Recovering
                                @elseif ($loan->processing == 1)
                                    Registered
                                @elseif ($loan->processing == 2)
                                    Rejected
                                @elseif ($loan->processing == 3)
                                    Processing
                                @elseif ($loan->processing == 4)
                                    Approved
                                @elseif ($loan->processing == 5)
                                    Disburse
                                @else
                                    -
                                @endif
                            </label>
                        </h4>
                    </div>
                    <div class="col-sm-3">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-dark" href="{{ route('memberships.show', $loan->membership->id) }}">Go to Member</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-default" href="{{ route('loan.index') }}">Go to List</a>
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
        <form id="loan" action="{{ route('loan.disburse', $loan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h5>Account Details</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="account_no" class="col-sm-4 col-form-label">Bank Account</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $loan->bank_acc_no ?? '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="bank_name" class="col-sm-4 col-form-label">Bank Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{$loan->bank_name ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="branch_name" class="col-sm-4 col-form-label">Branch Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $loan->bank_branch ?? '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <h5>Suwasahana Loan Details</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="suwasahana_amount" class="col-sm-4 col-form-label">Suwasahana Due</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ number_format($loan->suwasahana_amount,2) ?? '0.00' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="loan_10_month" class="col-sm-4 col-form-label">10 Month Loan</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $loan->membership->loan10month == 1 ? 'Yes' : 'No'  }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <h5>Salary Details</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="total_salary" class="col-sm-5 col-form-label">Total Salary</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="total_salary" value="{{ number_format($loan->total_salary,2) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="salary_40" class="col-sm-5 col-form-label">40% Salary</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="salary_40" name="salary_40" value="{{ number_format($loan->salary_40,2) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="basic_salary" class="col-sm-5 col-form-label">Basic Salary</label>
                            <div class="col-sm-7">
                                <input type="text" id="basic_salary" class="form-control" name="basic_salary" value="{{ number_format($loan->basic_salary,2) }}" readonly>
                            </div>
                        </div>
{{--                        <div class="col-6 row">--}}
{{--                            <label for="deductions" class="col-sm-5 col-form-label">Total Deductions</label>--}}
{{--                            <div class="col-sm-7">--}}
{{--                                <input type="text" class="form-control" value="0.00" name="deductions" readonly>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="good_conduct" class="col-sm-5 col-form-label">Good Conduct</label>
                            <div class="col-sm-7">
                                <input type="text" name="good_conduct" class="form-control" value="{{ number_format($loan->good_conduct,2) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="ten_month_loan" class="col-sm-5 col-form-label">10 Month loan</label>
                            <div class="col-sm-7">
                                <input type="text" name="ten_month_loan" class="form-control " value="{{ number_format($loan->ten_month_loan,2) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="incentive" class="col-sm-5 col-form-label">Incentive</label>
                            <div class="col-sm-7">
                                <input type="text" name="incentive" class="form-control" value="{{ number_format($loan->incentive,2) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="other_loan" class="col-sm-5 col-form-label">Other Loan Deduction</label>
                            <div class="col-sm-7">
                                <input type="text" name="other_loan" class="form-control" value="{{ number_format($loan->other_loan,2) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="qualification" class="col-sm-5 col-form-label">Qualification</label>
                            <div class="col-sm-7">
                                <input type="text" name="qualification" class="form-control" value="{{ number_format($loan->qualification,2) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="festival_advance" class="col-sm-5 col-form-label">Festival Advance</label>
                            <div class="col-sm-7">
                                <input type="text" name="festival_advance" class="form-control" value="{{ number_format($loan->festival_advance,2) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="ration" class="col-sm-5 col-form-label">Ration value</label>
                            <div class="col-sm-7">
                                <input type="text" name="ration" class="form-control" value="{{ number_format($loan->ration,2) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="special_advance" class="col-sm-5 col-form-label">Special Advance</label>
                            <div class="col-sm-7">
                                <input type="text" name="special_advance" class="form-control" value="{{ number_format($loan->special_advance,2) }}" readonly>
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
                            <label for="fund_balance" class="col-sm-4 col-form-label">Registered Date</label>
                            <div class="col-sm-8">
                                <input type="date" class="form-control" value="{{ isset($loan->registered_date) ? date('Y-m-d', strtotime($loan->registered_date)) : '' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="fund_balance" class="col-sm-4 col-form-label">Fund Balance</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ number_format($loan->fund_balance,2) ?? '0.00' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="allowed_amount_from_fund" class="col-sm-4 col-form-label">Calculated Amount</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ number_format($loan->allowed_amount_from_fund,2) ?? '0.00' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="no_of_installments" class="col-sm-4 col-form-label">No of Installments</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="no_of_installments" value="{{ $loan->no_of_installments }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="calculated_amount" class="col-sm-4 col-form-label">Suggested With Basic</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ number_format($loan->suggested_amount,2) ?? '0.00' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="requested_amount" class="col-sm-4 col-form-label">Requested Amount</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ number_format($loan->total_amount_requested,2) ? :'' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="approved_amount" class="col-sm-4 col-form-label">Approved Amount</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="approved_amount" value="{{ number_format($loan->approved_amount, 2) ?? '0.00' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <h5>Payment Plan</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="monthly_capital_portion" class="col-sm-4 col-form-label">Monthly Capital</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ number_format($loan->monthly_capital_portion,2) ?? '0.00' }}" readonly>
                            </div>
                        </div>

                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="arrest_dates" class="col-sm-4 col-form-label">Arrears Days</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $loan->arrest_dates }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="arrest_interest" class="col-sm-4 col-form-label">Arrears Interest</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ number_format($loan->arrest_interest,2) }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="voucher_id" class="col-sm-4 col-form-label">Voucher No</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $loan->voucher_id ?: '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="file_ref_no" class="col-sm-4 col-form-label">File Reference</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ $loan->file_ref_no ?: '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="total_capital" class="col-sm-4 col-form-label">Total Capital</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ number_format($loan->loan->total_capital,2) ?: '0' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="file_ref_no" class="col-sm-4 col-form-label">Recovered Capital</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" value="{{ number_format($loan->loan->total_recovered_capital,2) ?: '0' }}" readonly>
                            </div>
                        </div>
                    </div>
                    @if($loan->approved_amount!=0)
                        <div id="payments">
                            <div class="card">
                                <div class="card-header">
                                    <a class="card-link" data-toggle="collapse" href="#collapseData" id="payments">
                                        Repayment History
                                        <span class="float-right"><i class="fas fa-chevron-right"></i></span>
                                    </a>
                                </div>
                                <div id="collapseData" class="collapse" data-parent="#payments">
                                    <div class="card-body">
                                        @if ($loan->repayment)
                                            <table class="table table-bordered" id="contributionTable">
                                                <thead>
                                                <tr>
                                                    <th>No</th>
{{--                                                    <th>Due Date</th>--}}
                                                    <th>Paid Date</th>
                                                    <th>Due Capital</th>
                                                    <th>Due Interest</th>
                                                    <th>Recovered Capital</th>
                                                    <th>Recovered Interest</th>
                                                    <th>Remaining Balance</th>
                                                </tr>
                                                </thead>
                                                @php
                                                    $remainingLoan = $loan->loan->total_capital;
                                                @endphp
                                                <tbody>
                                                @foreach ($loan->repayment->sortBy('payment_no') as $repayment)
                                                    @php
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $repayment->payment_no ?? '-' }}</td>
{{--                                                        <td>{{ $repayment['due_date'] ?? '-' }}</td>--}}
                                                        <td>{{ $repayment->payment_date ?? 'NA'}}</td>
                                                        <td>{{ number_format($repayment->capital_due, 2) ?? '-' }}</td>
                                                        <td>{{ number_format($repayment->interest_due, 2) ?? '-' }}</td>
                                                        <td>{{ number_format($repayment->capital_received, 2) ?? '-' }}</td>
                                                        <td>{{ number_format($repayment->interest_received, 2) ?? '-' }}</td>
                                                        <td>{{ number_format($repayment->loan_balance, 2) ?? '-' }}</td>
{{--                                                        <td>{{ number_format($repayment['total_installment'], 2) ?? '-' }}</td>--}}
                                                        {{--                                                    <td>{{ $repayment['payment_date'] ?? '-' }}</td>--}}
{{--                                                        <td>{{ number_format(floatval($repayment['repayment_data']['capital_received'] ?? '-'), 2) }}</td>--}}
{{--                                                        <td>{{ number_format(floatval($repayment['repayment_data']['interest_received'] ?? '-'), 2) }}</td>--}}
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="col-6 row">
                                                <div class="col-sm-5 text-warning">
                                                    <span>No data</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);
        });
    </script>
@endsection


