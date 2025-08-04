@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Contribution Correction Approval</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('corrections') }}" class="btn btn-sm btn-dark">Back</a>
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
                <div class="card-title">
                    <i class="fas fa-arrow-right"></i> {{$correction->membership->name ?? '-'}}
                </div>
            </div>
            <div class="container-fluid">
                <form action="{{ route('correction-approvalstore', $correction->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-4 row">
                                <label for="type" class="col-sm-4 col-form-label">Type</label>
                                <div class="col-sm-4">
                                    <select class="form-control" data-live-search="true" name="type" readonly>
                                        <option value="" disabled selected>Select Type</option>
                                        <option @if($correction->type == "Addition") selected @endif>Addition</option>
                                        <option @if($correction->type == "Deduction") selected @endif>Deduction</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4 row">
                                <label for="amount" class="col-sm-4 col-form-label">Amount</label>
                                <div class="col-sm-8">
                                    <input type="text" name="amount" class="form-control col-sm-5" value="{{ number_format($correction->amount,2) }}" readonly>
                                </div>
                            </div>
                            <div class="col-4 row">
                                <label for="interest" class="col-sm-4 col-form-label">Interest</label>
                                <div class="col-sm-8">
                                    <input type="text" name="interest" class="form-control col-sm-5" value="{{ number_format($correction->interest,2) }}" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 row">
                                <label for="remark" class="col-sm-2 col-form-label">Remark</label>
                                <div class="col-sm-10">
                                    <input type="text" name="remark" class="form-control" value="{{ $correction->remark }}">
                                </div>
                            </div>
                        </div>
                        <div class="card-header"></div>
                        <div class="card-body">
                            <div class="col-md-6 text-right">
                                <button type="submit" name="approval" value="approve" class="btn btn-sm btn-outline-primary">Approve</button>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#rejectModal" @if ($correction->accepted == 2) disabled @endif>Reject</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('correction-approvalstore', $correction->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Reject Contribution Correction</h5>
{{--                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                            <span aria-hidden="true">&times;</span>--}}
{{--                        </button>--}}
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <label for="fwd_to" class="col-form-label">Assign to</label>
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
                                <textarea class="form-control" name="fwd_to_reason" id="fwd_to_reason" rows="4"></textarea>
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


