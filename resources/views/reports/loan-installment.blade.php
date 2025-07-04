@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Loan Installment Report</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('reports.index') }}" class="btn btn-sm btn-dark">Back</a>
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
                <div class="container-fluid">
                    <form method="GET" action="{{ route('loan-installment') }}">
                        <div class="form-group row">
                            <div class="col-6 row">
                                <label for="category_id" class="col-sm-4 col-form-label">Rank Type</label>
                                <div class="col-sm-8">
                                    <select name="category_id" id="category_id" class="form-control" data-live-search="true" required>
                                        <option selected value="" disabled>Select Type</option>
                                        <option value="1">Officer</option>
                                        <option value="2">Other Ranker</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 row" id="regiment_id" style="display: none;">
                                <label for="regiment_id" class="col-sm-4 col-form-label">Regiment</label>
                                <div class="col-sm-8">
                                    @if(isset($regiments))
                                        <select name="regiment_id" id="regiment_id" class="form-control" data-live-search="true">
                                            <option selected value="" >Select Regiment</option>
                                            @foreach($regiments as $regiment)
                                                <option value="{{ $regiment->id }}">{{ $regiment->regiment_name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 row" id="type" style="display: none;">
                                <label for="type" class="col-sm-4 col-form-label">Type</label>
                                <div class="col-sm-8">
                                    <select name="type" id="type" class="form-control" data-live-search="true">
                                        <option selected value="" disabled>Select Type</option>
                                        <option value="Regular">Regular</option>
                                        <option value="Volunteer">Volunteer</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6 row">
                                <label for="deposit_year" class="col-sm-4 col-form-label">Year</label>
                                <div class="col-sm-8">
                                    <input type="number" name="year" value="{{ date('Y') }}" class=" col-sm-4 form-control" required>
                                </div>
                            </div>
                            <div class="col-6 row">
                                <label for="month" class="col-sm-4 col-form-label">Contribution Month</label>
                                <div class="col-sm-8">
                                    <input type="number" min="1" max="12" name="month" value="{{ date('m') }}" class="col-sm-4 form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <a href="{{ route('loan-installment') }}" class="btn btn-sm btn-dark">Refresh</a>
                            <button type="submit" class="btn btn-sm btn-outline-primary">Process</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="text-right mb-3">
                    <a href="{{ route('installment-csv', ['year' => request()->input('year'), 'month' => request()->input('month'), 'category_id' => request()->input('category_id'), 'regiment_id' => request()->input('regiment_id'), 'type' => request()->input('type')]) }}" class="btn btn-sm btn-success mr-3">Export</a>
                </div>
                <table class="table table-bordered" id="loans">
                    <thead class="text-center">
                    <tr>
                        <th>Application No</th>
                        <th>Reg No</th>
                        <th>Name</th>
                        <th>Unit</th>
{{--                        <th>Total Capital</th>--}}
{{--                        <th>Recovered Capital</th>--}}
                        <th>Payment No</th>
                        <th>Capital</th>
                        <th>Interest</th>
                        <th>Installment</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($loans as $loan)
                        <tr>
                            <td>{{ $loan->application_reg_no ?? 'NA' }}</td>
                            <td>{{ $loan->membership->regimental_number ?? 'NA'}}</td>
                            <td>{{ $loan->membership->ranks->rank_name ?? 'NA'}} {{ $loan->membership->name ?? '-'}}</td>
                            <td>{{ $loan->membership->regiments->regiment_name ?? 'NA'}} </td>
{{--                            <td>{{ number_format($loan->loan->total_capital,2) ?? 'NA' }}</td>--}}
{{--                            <td>{{ number_format($loan->loan->total_recovered_capital,2) ?? 'NA' }}</td>--}}
                            <td>{{ $loan->repayment[0]->payment_no ?? '-' }}</td>
                            <td>{{ $loan->repayment[0]->capital_due ?? '-' }}</td>
                            <td>{{ $loan->repayment[0]->interest_due ?? '-' }}</td>
                            <td>
                                @if($loan->repayment->count()!=0)
                                    {{ number_format($loan->repayment[0]->capital_due + $loan->repayment[0]->interest_due,2) ?? '-' }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                $('#loans').DataTable({
                    responsive: true,
                    paging: true,
                    // searching: true,
                    info: false,
                });
            });


            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);

            $('#category_id').on('change', function () {
                if ($(this).val() === '2') {
                    $('#regiment_id').show();
                    $('#type').hide();
                } else {
                    $('#type').show();
                    $('#regiment_id').hide();
                }
            });
        </script>
@endsection


