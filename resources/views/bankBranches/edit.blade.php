@extends('layouts.app')


@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3><strong>Edit Bank Branch Details</strong></h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('bank-branches.index') }}" class="btn btn-sm btn-dark"> Back</a>
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
                <div class="container-fluid">
                    <form action="{{ route('bank-branches.update', ['bank_branch' => $branch->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for='bank_name' class="col-sm-4 col-form-label">Bank Name</label>
                                    <div class="col-sm-8">
                                        <input disabled name='bank_name' type="text" class="form-control" value="{{ $branch->bank->bank_name ? : ''}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="bank_branch_name" class="col-sm-4 col-form-label">Branch Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="bank_branch_name" class="form-control" value="{{ $branch->bank_branch_name ? : ''}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="branch_code" class="col-sm-4 col-form-label">Branch Code</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="branch_code" class="form-control" value="{{ $branch->branch_code ? : ''}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="contact_details" class="col-sm-4 col-form-label">Contact Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="contact_details" class="form-control" value="{{ $branch->contact_details ? : ''}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                            <input type="hidden" name="bank_id" value="{{ $branch->bank->id }}">
                            <input type="hidden" name="version" value="{{ $branch->version }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
