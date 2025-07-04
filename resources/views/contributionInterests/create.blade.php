@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Create New Interest</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('contribution-interests.index') }}" class="btn btn-sm btn-dark">Back</a>
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
                    <form action="{{ route('contribution-interests.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="year" class="col-sm-4 col-form-label">Year</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="year" class="form-control" value="{{date('Y')}}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="icp_id" class="col-sm-4 col-form-label">Quarter</label>
                                    <div class="col-sm-8">
                                        <select name="icp_id" class="form-control" data-live-search="true">
                                            <option selected value="" disabled>Select Quarter</option>
                                            <option value=10>1st Quarter</option>
                                            <option value=20>2nd Quarter</option>
                                            <option value=30>3rd Quarter</option>
                                            <option value=40>4th Quarter</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="interest_rate" class="col-sm-4 col-form-label">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="interest_rate" class="form-control" value="0.00">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="name" class="col-sm-4 col-form-label">Status</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" data-live-search="true" name="status" required>
                                            <option value="" disabled selected>Select Status</option>
                                            <option value=1>Active</option>
                                            <option value=0>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
                        </div>
                    </form>
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


