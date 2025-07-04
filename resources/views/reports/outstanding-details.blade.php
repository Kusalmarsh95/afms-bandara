@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Outstanding Applications Details</h3>
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
                    <form method="GET" action="{{ route('outstanding-details') }}">
                        <div class="form-group row">
                            <div class="col-6 row">
                                <label for="type" class="col-sm-4 col-form-label">Application Type</label>
                                <div class="col-sm-8">
                                    <select name="type" class="form-control" data-live-search="true" required>
                                        <option selected value="" disabled>Select</option>
                                        <option value=1>Loan</option>
                                        <option value=2>Withdrawal</option>
                                        <option value=3>Full Withdrawal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 row">
                                <label for="date_range" class="col-sm-4 col-form-label">Date</label>
                                <div class="col-sm-8">
                                    <input type="date" name="date_range" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Process</button>
                            <a href="{{ route('outstanding-details') }}" class="btn btn-sm btn-dark">Refresh</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="text-right mb-3">
                    <a href="{{ route('pdf-outstanding-details', ['type' => request()->input('type'), 'date_range' => request()->input('date_range')]) }}"><i class="fas fa-file-pdf text-red"></i></a>
                </div>
                <table class="table table-bordered" id="outstanding">
                    <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Application Number</th>
                        <th class="text-center">Regimental Number</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Registered Date</th>
                    </tr>
                    </thead>

                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($applications as $application)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $application->application_reg_no ?? '-' }}</td>
                            <td>{{ $application->membership->regimental_number ?? '-' }}</td>
                            <td>{{ $application->membership->ranks->rank_name ?? '-'}} {{ $application->membership->name ?? '-'}}</td>
                            <td>{{ $application->userName ?? '-'}}</td>
                            <td>{{ date('Y-m-d', strtotime($application->registered_date)) ?? '-'}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#outstanding').DataTable({
                    responsive: true,
                    paging: true,
                    // searching: true,
                    info: false,
                    buttons: []
                });
            });

            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);
        </script>
@endsection


