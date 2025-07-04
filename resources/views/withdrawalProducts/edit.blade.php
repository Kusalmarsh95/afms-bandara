@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Edit Product</h3>
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
                    <form action="{{ route('withdrawal-products.update', $withdrawalProduct->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="name" class="col-sm-4 col-form-label">Product Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="name" class="form-control" value="{{$withdrawalProduct->name}}" required>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="percentage" class="col-sm-4 col-form-label">Percentage (%)</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="percentage" class="form-control" value="{{$withdrawalProduct->percentage}}" required>
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
                                        <input type="text" name="male_service" class="form-control" value="{{$withdrawalProduct->male_service}}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="female_service" class="col-sm-4 col-form-label">Female </label>
                                    <div class="col-sm-8">
                                        <input type="text" name="female_service" class="form-control" value="{{$withdrawalProduct->female_service}}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="name" class="col-sm-4 col-form-label">Status</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" data-live-search="true" name="status" required>
                                            <option value="" disabled>Select Status</option>
                                            <option value=1 @if($withdrawalProduct->status == 1) selected @endif>Active</option>
                                            <option value=0 @if($withdrawalProduct->status == 0) selected @endif>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
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


