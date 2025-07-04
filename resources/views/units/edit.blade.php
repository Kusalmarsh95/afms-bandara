@extends('layouts.app')


@section('content')
    <div class="container-fluid">

        <div class="container-fluid">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6 text-Left">
                            <h4><i class="nav-icon fas fa-address-card text-blue"></i> <strong>Master Data</strong> | Units Management</h4>
                        </div>
                        <div class="col-sm-6 text-right">
                            <h6> <strong>Master Data</strong> > <i class="nav-icon fas fa-city text-blue"></i> Edit Units</h6>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12 text-right">
                            <a href="{{ route('units.index') }}" class="btn btn-primary btn-sm"> Back</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>

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
                    <form action="{{ route('units.update', ['unit' => $unit->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="regiment_id" class="col-sm-4 col-form-label">Regiment</label>
                                    <div class="col-sm-8">
                                        @if(isset($regiments))
                                            <select name="regiment_id" class="form-control" data-live-search="true">
                                                <option selected>Select Regiment</option>
                                                @foreach($regiments as $regiment)
                                                    <option value="{{ $regiment->id }}"{{ $unit->regiment_id == $regiment->id ? 'selected' : '' }}>{{ $regiment->regiment_name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="unit_name" class="col-sm-4 col-form-label">Unit</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="unit_name" class="form-control" value="{{ $unit->unit_name ? : '-'}}">
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
