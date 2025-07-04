@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Create New Bank Branch</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('bank-branches.index') }}" class="btn btn-sm btn-dark">Back</a>
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
            <script type="text/javascript">
                $(document).ready(function () {
                    setTimeout(function () {
                        $('.alert').fadeOut();
                    }, 2000);
                });
            </script>
        @endif
        <div class="card">
            <div class="card-header">
                <div class="container-fluid">
                    <form action="{{ route('bank-branches.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="id" class="col-sm-4 col-form-label">Select Bank</label>
                                    <div class="col-sm-8">
                                        <select type="text" name="bank_id" class="form-control">
                                            @foreach ($banks as $bank)
                                            <option value="{{ $bank['id'] }}">{{ $bank['bank_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="bank_branch_name" class="col-sm-4 col-form-label">Branch Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="bank_branch_name" class="form-control" placeholder="Branch Name">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="branch_code" class="col-sm-4 col-form-label">Branch Code</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="branch_code" class="form-control" placeholder="Branch Code">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="contact_details" class="col-sm-4 col-form-label">Contact Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="contact_details" class="form-control" placeholder="Contact Number">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


