@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Add Suwasahana Details</h3>
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
                    <form action="{{ route('suwasahana.store', $membership->id) }}" method="POST">
                        @csrf
                        <div class="card-header">
                            <h5>{{$membership->ranks->rank_name}} {{$membership->name}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="ABFvoucherno" class="col-sm-4 col-form-label">Voucher No</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="ABFvoucherno" class="form-control" >
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="PNRVoucherno" class="col-sm-4 col-form-label">PNR Voucher No</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="PNRVoucherno" class="form-control" >
                                    </div>
                                </div>

                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="total_capital" class="col-sm-4 col-form-label">Principal Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="total_capital" class="form-control" >
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="total_interest" class="col-sm-4 col-form-label">Interest Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="total_interest" class="form-control" >
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="no_of_installments" class="col-sm-4 col-form-label">No of Installments</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="no_of_installments" class="form-control" >
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="monthly_capital" class="col-sm-4 col-form-label">Monthly Capital</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="monthly_capital" class="form-control" >
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="Issue_Date" class="col-sm-4 col-form-label">Issued Date</label>
                                    <div class="col-sm-8">
                                        <input type="date" name="Issue_Date" class="form-control" >
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="LoanType" class="col-sm-4 col-form-label">Loan Type</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" data-live-search="true" name="LoanType" >
                                            <option value="" disabled selected>Select</option>
                                            <option value='PERSONAL LOAN'>PERSONAL LOAN</option>
                                            <option value='RELIEVE INDEBTEDNESS LOAN'>RELIEVE INDEBTEDNESS LOAN</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="form-group row">
                                    <div class="col-4 row">
                                        <label for="fwd_to" class="col-sm-4 col-form-label">Forward To</label>
                                        <div class="col-sm-8">
                                            @if(isset($users))
                                                <select name="fwd_to" class="form-control" data-live-search="true" required>
                                                    <option disabled selected>Assign a Officer</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-8 row">
                                        <label for="fwd_to_reason" class="col-sm-2 col-form-label">Remark</label>
                                        <div class="col-sm-10">
                                            <input type="text" name="fwd_to_reason" class="form-control" placeholder="Please provide a remark" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" value="submit" class="btn btn-sm btn-outline-info m-2">Submit</button>
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


