@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Application Details of {{$loan->product->name ?? '-'}}</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-dark" href="{{ route('loan.index') }}">Back</a>
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
        <form id="loan" action="{{ route('loan.approval', $loan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <h5>Personal Details</h5>
                        </div>
                        @if ($loan->processing == 3)
                            <div class="col-md-6 text-right">
                                <a class="btn" href="{{ route('loan-voucher', ['id' => $loan->id, 'download' => 'pdf']) }}"><i class="fas fa-file-pdf text-red"></i></a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="application_reg_no" class="col-sm-5 col-form-label">Registration Number</label>
                            <div class="col-sm-7">
                                <input type="text" id="application_reg_no" name="application_reg_no" class="form-control" value="{{$loan->application_reg_no}}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="registered_date" class="col-sm-5 col-form-label">Registered Date</label>
                            <div class="col-sm-7">
                                <input type="date" id="registered_date" class="form-control" value="{{ $loan->registered_date ? (new DateTime($loan->registered_date))->format('Y-m-d') : '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="regimental_number" class="col-sm-5 col-form-label">Regimental Number</label>
                            <div class="col-sm-7">
                                <input type="text" id="regimental_number" class="form-control" value="{{$loan->membership->regimental_number ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="unit" class="col-sm-5 col-form-label">Unit</label>
                            <div class="col-sm-7">
                                <input type="text" id="unit" class="form-control" value="{{ $loan->membership->units->unit_name ?? '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="regiment" class="col-sm-5 col-form-label">Regiment</label>
                            <div class="col-sm-7">
                                <input type="text" id="regiment" class="form-control" value="{{ $loan->membership->regiments->regiment_name ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="mobile_no" class="col-sm-5 col-form-label">Mobile Number</label>
                            <div class="col-sm-7">
                                <input type="text" id="mobile_no" class="form-control" value="{{ $loan->membership->telephone_mobile ? : 'NA'}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="rank" class="col-sm-5 col-form-label">Rank</label>
                            <div class="col-sm-7">
                                <input type="text" id="rank" class="form-control" value="{{ $loan->membership->ranks->rank_name ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="name" class="col-sm-5 col-form-label">Name</label>
                            <div class="col-sm-7">
                                <input type="text" id="name" class="form-control" value="{{ $loan->membership->name ?? '-' }}" readonly>
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
                                <input type="text" name="account_no" class="form-control" value="{{ $loan->bank_acc_no ?? '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="bank_name" class="col-sm-4 col-form-label">Bank Name</label>
                            <div class="col-sm-8">
                                <input type="text" name="bank_name" class="form-control" value="{{$loan->bank_name ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="branch_name" class="col-sm-4 col-form-label">Branch Name</label>
                            <div class="col-sm-8">
                                <input type="text" name="branch_name" class="form-control" value="{{ $loan->bank_branch ?? '-' }}" readonly>
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
                    <h5>Loan Details</h5>
                </div>
                <div class="card-body">
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
                            <label for="approved_amount" class="col-sm-4 col-form-label">Approval Amount</label>
                            <div class="col-sm-8">
                                <input type="hidden" name="approved_amount" value="{{ $approvedAmount }}">
                                <input type="text" class="form-control" id="approved_amount" value="{{ number_format($approvedAmount, 2) ?? '0.00' }}">
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
                                    @if(isset($usersForward))
                                        <select name="fwd_to" class="form-control" data-live-search="true" required>
                                            <option disabled selected>Assign a Officer</option>
                                            @foreach($usersForward as $user)
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
                            @can('loans-applications-forward')
                            <button type="submit" name="approval" class="btn btn-sm btn-outline-info m-2" value="forward">Forward</button>
                            @endcan
                            @can('loans-applications-process')
                            <button type="submit" name="approval" class="btn btn-sm btn-outline-primary  m-2" value="process"
                                    @if ($loan->processing == 3) disabled @endif>
                                @if ($loan->processing == 3)
                                    Processing
                                @else
                                    Process
                                @endif
                            </button>
                            @endcan
                            @can('loans-applications-approve')
                            <button type="submit" name="approval" class="btn btn-sm btn-outline-warning  m-2" value="approve">Approve</button>
                            @endcan
                            @can('loans-applications-reject')
                            <button type="button" class="btn btn-sm btn-outline-danger  m-2" data-toggle="modal" data-target="#rejectModal" @if ($loan->processing == 2) disabled @endif>@if ($loan->processing === 2)
                                    Rejected
                                @else
                                    Reject
                                @endif</button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('loan.approval', $loan->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Reject Withdrawal</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <label for="fwd_to" class="col-form-label">Forward to</label>
                            <div class="col-md-12">
                                @if(isset($usersReject))
                                    <select name="fwd_to" class="form-control" data-live-search="true">
                                        <option selected>Assign</option>
                                        @foreach($usersReject as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class="row">
                            <label for="fwd_to_reason">Reason for Rejecting</label>
                            <div class="col-md-12">
                                @if(isset($rejectReasons))
                                    <select name="fwd_to_reason" class="form-control" data-live-search="true">
                                        <option selected disabled>Reason</option>
                                        @foreach($rejectReasons as $rejectReason)
                                            <option value="{{ $rejectReason->id }}">{{ $rejectReason->reason_name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="approval" value="reject" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('.alert').fadeOut();
            }, 2000);
        });
    </script>
@endsection


