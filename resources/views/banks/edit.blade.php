@extends('layouts.app')


@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 text-Left">
                        <h4><i class="nav-icon fas fa-address-card text-blue"></i> <strong>Master Data</strong> | Bank
                            Management</h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <h6> <strong>Master Data</strong> > <i class="nav-icon fas fa-building-columns text-blue"></i>
                            Edit Bank </h6>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-12 text-right">
                        <a href="{{ route('banks.index') }}" class="btn btn-sm btn-primary">Back</a>
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
                    <form action="{{ route('banks.update', ['bank' => $bank->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="id" class="col-sm-4 col-form-label">Bank Code</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="id" class="form-control" value="{{ $bank->id ? : '-'}}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="bank_name" class="col-sm-4 col-form-label">Bank Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="bank_name" class="form-control" value="{{ $bank->bank_name ? : '-'}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
