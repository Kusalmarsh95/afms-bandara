@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Add a Nominee</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <button class="btn btn-sm btn-dark" onclick="goBack()">Back</button>
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
                <div class="card-title"><i class="fas fa-arrow-right"></i> {{$nominee->membership->name ?? 'Member name not mentioned'}} </div>
            </div>
            <div class="card-header">
                <div class="container-fluid">
                    <form action="{{ route('n-approval', ['id' => $nominee->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="name" class="col-sm-4 col-form-label">Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="name" class="form-control" value="{{$nominee->name ?: '-'}}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="nomineenic" class="col-sm-4 col-form-label">NIC</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="nomineenic" class="form-control" value="{{$nominee->nomineenic ?: '-'}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="relationship_id" class="col-sm-4 col-form-label">Relationship</label>
                                    <div class="col-sm-8">
                                        @if(isset($relationships))
                                            <select name="relationship_id" class="form-control" data-live-search="true">
                                                <option>Select Relationship</option>
                                                @foreach($relationships as $relationship)
                                                    <option value="{{ $relationship->id }}" {{ $nominee->relationship_id == $relationship->id ? 'selected' : '' }}>{{ $relationship->relationship_name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="percentage" class="col-sm-4 col-form-label">Percentage (%)</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="percentage" class="form-control" value="{{$nominee->percentage ?: '-'}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="nominee_address1" class="col-sm-4 col-form-label">Address</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="nominee_address1" class="form-control" value="{{$nominee->nominee_address1 ?: '-'}}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="enabled" class="col-sm-4 col-form-label">Active</label>
                                    <div class="col-sm-8">
                                        <input type="hidden" name="enabled" value=0>
                                        <input type="checkbox" name="enabled" value=1 {{ $nominee->enabled ? 'checked' : '' }}>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="nominee_address2" class="col-sm-4 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="nominee_address2" class="form-control" value="{{$nominee->nominee_address2 ?: '-'}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="nominee_address3" class="col-sm-4 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="nominee_address3" class="form-control" value="{{$nominee->nominee_address3 ?: '-'}}">
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
                                    <label for="account_number" class="col-sm-4 col-form-label">Account Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="account_number" value="{{ $nominee->details->account_number ?? '' }}" class="form-control" >
                                    </div>
                                  </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="bank_name" class="col-sm-4 col-form-label">Bank Name </label>
                                    <div class="col-sm-8">
{{--                                        @if(isset($banks))--}}
{{--                                            <select name="bank_name" class="form-control" data-live-search="true">--}}
{{--                                                <option disabled>Select Bank</option>--}}
{{--                                                @foreach($banks as $bank)--}}
{{--                                                    <option value="{{ $bank->bank_name }}"{{ $nominee->details->bank_name == $bank->bank_name ? 'selected' : '' }}>{{ $bank->bank_name ?? ''}}</option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}
{{--                                        @endif--}}
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="branch_name" class="col-sm-4 col-form-label">Branch Name</label>
                                    <div class="col-sm-8">
{{--                                        @if(isset($branches))--}}
{{--                                            <select name="branch_name" class="form-control" data-live-search="true">--}}
{{--                                                <option selected>Select Branch Name</option>--}}
{{--                                                @foreach($branches as $branch)--}}
{{--                                                    <option value="{{ $branch->bank_branch_name }}"{{ $nominee->details->branch_name == $branch->bank_branch_name ? 'selected' : '' }}>{{ $branch->bank_branch_name ?? ''}}</option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}
{{--                                        @endif--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header"></div>
                        <div class="card-body">
                            <div class="col-md-6 text-right">
                                <button type="submit" name="approval" value="approve" class="btn btn-sm btn-primary">Approve</button>
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal" @if ($nominee->accepted == 2) disabled @endif>Reject</button>
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
                <form action="{{ route('n-approval', ['id' => $nominee->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">Reject Nominee</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
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
        function goBack() {
            window.history.back();
        }
    </script>
@endsection


