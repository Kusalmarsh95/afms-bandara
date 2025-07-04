@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3><strong>Absent Settlement Details</strong></h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-dark" href="{{ route('memberships.show', $loan->membership->id) }}">Back</a>

                            </li>
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-default" href="{{ URL::previous() }}">Go to List</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <div class="container-fluid">
                    <form action="{{ route('absent-settlement-update', $loan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <h5>{{$loan->membership->ranks->rank_name}} {{$loan->membership->name}}</h5>
                                </div>
{{--                                @if ($loan->loan->settled == 1)--}}
{{--                                    <div class="col-md-6 text-right">--}}
{{--                                        <a class="btn" href="{{ route('loan-settlement-pdf', ['id' => $loan->id, 'download' => 'pdf']) }}"><i class="fas fa-file-pdf text-red"></i></a>--}}
{{--                                    </div>--}}
{{--                                @endif--}}
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-6 col-form-label">Registration No</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="application_reg_no" class="form-control" value="{{ $loan->application_reg_no ?? '-'}}" readonly>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="settled" class="col-sm-6 col-form-label">settled</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="settled" class="form-control"  value="@if( $loan->loan->settled)Yes @else No @endif" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-6 col-form-label">Registered Date</label>
                                    <div class="col-sm-6">
                                        <input type="date" class="form-control" value="{{ $loan ? (new DateTime($loan->registered_date))->format('Y-m-d') : '' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-6 col-form-label">Last Payment</label>
                                    <div class="col-sm-6">
                                        <input type="date" class="form-control" value="{{ $loan ? (new DateTime($loan->last_pay_date))->format('Y-m-d') : '' }}" readonly style="color: red;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="total_capital" class="col-sm-6 col-form-label">Capital Amount</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value="{{ number_format($loan->loan->total_capital,2) ?? '-'}}" readonly>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="total_recovered_capital" class="col-sm-6 col-form-label">Recovered Capital</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="total_recovered_capital" class="form-control" value="{{ number_format($loan->loan->total_recovered_capital,2) ?: '-'}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="loan_due_cap" class="col-sm-6 col-form-label">Due Capital</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="loan_due_cap" class="form-control" value="{{ number_format($loan->loan->total_capital - $loan->loan->total_recovered_capital,2) ?: '-'}}" readonly>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="total_recovered_interest" class="col-sm-6 col-form-label">Recovered Interest</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value="{{ number_format($loan->loan->total_recovered_interest,2) ?? '-'}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="fund_balance" class="col-sm-6 col-form-label">Fund Balance</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="fund_balance" class="form-control" value="{{ number_format($fundBalance ?? 0, 2) }}" readonly>
                                    </div>
                                </div>
{{--                                <div class="col-6 row">--}}
{{--                                    <label for="settlement_date" class="col-sm-6 col-form-label">Settlement Date</label>--}}
{{--                                    <div class="col-sm-6">--}}
{{--                                        <input type="date" name="settlement_date" class="form-control" value="{{ $loan->directSettlement ? (new DateTime($loan->directSettlement->settlement_date))->format('Y-m-d') : '' }}">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="settlement_amount" class="col-sm-6 col-form-label">Settlement Amount</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="settlement_amount" class="form-control" value="{{ number_format(($loan->loan->total_capital - $loan->loan->total_recovered_capital) ?? 0,2)}}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="arrest_interest" class="col-sm-6 col-form-label">Arrears Interest</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="arrest_interest" class="form-control" value="{{ $loan->directSettlement ? $loan->directSettlement->arrest_interest : number_format($loan->arrest_interest,2) }}">
                                    </div>
                                </div>
                            </div>

                            @if ($loan->loan->settled != 1)
                                <div class="card">
                                    <div class="card-body">
                                        @cannot('loans-direct-settlement-settle')
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
                                        @endcan
                                        <div class="col-md-12 text-center">
                                            @can('loans-direct-settlement-forward')
                                                <button type="submit" name="settlement" value="forward" class="btn btn-sm btn-outline-info m-2">Forward</button>
                                            @endcan
                                            @can('loans-direct-settlement-to-settle')
                                                <button type="submit" name="settlement" value="send" class="btn btn-sm btn-outline-primary m-2">To Settle</button>
                                            @endcan
                                            @can('loans-direct-settlement-settle')
                                                <button type="submit" name="settlement" value="approve" class="btn btn-sm btn-outline-success m-2">Settle</button>
                                            @endcan
                                            @if ($loan->directSettlement)
                                                <button type="button" class="btn btn-sm btn-outline-danger  m-2" data-toggle="modal" data-target="#rejectModal" @if ($loan->directSettlement->reject_reason_id != null) disabled @endif>Reject</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('loan.updateSettlement', $loan->id) }}" method="POST">
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
                        <button type="submit" name="settlement" value="reject" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        setTimeout(function () {
            $('.alert').fadeOut();
        }, 4000);
    </script>
@endsection
