@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Create New Product</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('withdrawal-products.index') }}" class="btn btn-sm btn-dark">Back</a>
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
                    <form action="{{ route('withdrawal-products.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="name" class="col-sm-4 col-form-label">Product Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="name" class="form-control" placeholder="Product Name" required>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="percentage" class="col-sm-4 col-form-label">Percentage (%)</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="percentage" class="form-control" placeholder="Percentage" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-4 row">
                                    <label for="name" class="col-sm-10 col-form-label">Minimum Service Period ,</label>
                                    <div class="col-sm-2">
{{--                                        <input type="text" name="name" class="form-control" placeholder="Product Name" required>--}}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="male_service" class="col-sm-4 col-form-label">Male </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="male_service" class="form-control" value="0" required>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="female_service" class="col-sm-4 col-form-label">Female </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="female_service" class="form-control" value="0" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
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
            }, 2000);
        });
    </script>
@endsection


