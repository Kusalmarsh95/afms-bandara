@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Contribution Correction</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('memberships.show', $membership->id) }}" class="btn btn-sm btn-dark">Back</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
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
                    <i class="fas fa-arrow-right"></i> {{$membership->ranks->rank_name ?? '-'}} {{$membership->name}}
                </div>
            </div>
                <div class="container-fluid">
                    <form action="{{ route('correctionStore',['id' => $membership->id]) }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="type" class="col-sm-4 col-form-label">Type</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" data-live-search="true" name="type" required>
                                            <option value="" disabled selected>Select Type</option>
                                            <option value="Addition">Addition</option>
                                            <option value="Deduction">Deduction</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="amount" class="col-sm-4 col-form-label">Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="amount" class="form-control col-sm-5" placeholder="Amount" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-12 row">
                                    <label for="remark" class="col-sm-2 col-form-label">Remark</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="remark" class="form-control" placeholder="Remark" required>
                                    </div>
                                </div>
                            </div>
                            <div class="card-header"></div>
                            <div class="card-body">
                                <div class="form-group row col-md-10 offset-2">
                                    <div class="col-6 row">
                                        <label for="fwd_to" class="col-sm-4 col-form-label">For Approval</label>
                                        <div class="col-sm-8">
                                            @if(isset($users))
                                                <select name="fwd_to" class="form-control col-sm-9" data-live-search="true">
                                                    <option selected>Assign a Officer</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-left">
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
                                    </div>
                                </div>
                            </div>
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


