@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Suwasahana Application Details</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-dark" href="{{ route('suwasahana.index') }}">Back</a>
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
        <form id="loan" action="{{ route('suwasahana.approval', $suwasahana->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h5>{{$suwasahana->membership->ranks->rank_name}} {{$suwasahana->membership->name}}</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="ABFvoucherno" class="col-sm-4 col-form-label">Voucher No</label>
                            <div class="col-sm-8">
                                <input type="text" name="ABFvoucherno" class="form-control" value="{{ $suwasahana->ABFvoucherno ?? '-'}}">
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="PNRVoucherno" class="col-sm-4 col-form-label">PNR Voucher No</label>
                            <div class="col-sm-8">
                                <input type="text" name="PNRVoucherno" class="form-control" value="{{ $suwasahana->PNRVoucherno ?? '-'}}">
                            </div>
                        </div>


                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="total_capital" class="col-sm-4 col-form-label">Principal Amount</label>
                            <div class="col-sm-8">
                                <input type="text" name="total_capital" class="form-control" value="{{ $suwasahana->total_capital ?? '-'}}">
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="total_interest" class="col-sm-4 col-form-label">Interest Amount</label>
                            <div class="col-sm-8">
                                <input type="text" name="total_interest" class="form-control" value="{{ $suwasahana->total_interest ?? '-'}}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="no_of_installments" class="col-sm-4 col-form-label">No of Installments</label>
                            <div class="col-sm-8">
                                <input type="text" name="no_of_installments" class="form-control" value="{{ $suwasahana->no_of_installments ?? '-'}}">
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="monthly_capital" class="col-sm-4 col-form-label">Monthly Capital</label>
                            <div class="col-sm-8">
                                <input type="text" name="monthly_capital" class="form-control" value="{{ $suwasahana->monthly_capital ?? '-'}}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="settled" class="col-sm-4 col-form-label">Loan Type</label>
                            <select class="form-control col-sm-8" data-live-search="true" name="LoanType" required>
                                <option value="" disabled>Select</option>
                                <option value='PERSONAL LOAN' {{ $suwasahana->LoanType == 'PERSONAL LOAN' ? 'selected' : '' }}>PERSONAL LOAN</option>
                                <option value='RELIEVE INDEBTEDNESS LOAN' {{ $suwasahana->LoanType == 'RELIEVE INDEBTEDNESS LOAN' ? 'selected' : '' }}>RELIEVE INDEBTEDNESS LOAN</option>
                            </select>

                        </div>
                        <div class="col-6 row">
                            <label for="Issue_Date" class="col-sm-4 col-form-label">Issued Date</label>
                            <div class="col-sm-8">
                                <input type="date" name="Issue_Date" class="form-control" value="{{ $suwasahana->Issue_Date ?? '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        @can('loans-applications-approve')
                            <button type="submit" name="approval" class="btn btn-sm btn-outline-success  m-2" value="approve">Approve</button>
                        @endcan
                        @can('loans-applications-reject')
                            <button type="button" class="btn btn-sm btn-outline-danger  m-2" data-toggle="modal" data-target="#rejectModal" @if ($suwasahana->accepted == 2) disabled @endif>
                                @if ($suwasahana->accepted == 2)
                                    Rejected
                                @else
                                    Reject
                                @endif</button>
                        @endcan
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('suwasahana.approval', $suwasahana->id) }}" method="POST">
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
                                @if(isset($users))
                                    <select name="fwd_to" class="form-control" data-live-search="true">
                                        <option selected>Assign</option>
                                        @foreach($users as $user)
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


