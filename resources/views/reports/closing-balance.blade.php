@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Closing Balance Report</h3>
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
                    <form method="GET" action="{{ route('closing-balance') }}">
                        <div class="form-group row">
                            <div class="col-4 row">
                                <label for="year" class="col-sm-6 col-form-label">Year </label>
                                <div class="col-sm-6">
                                    <input type="text" name="year" class="form-control" value="{{ request('year') }}">
                                </div>
                            </div>
                            <div class="col-4 row">
                                <label for="icp_id" class="col-sm-4 col-form-label">Quarter</label>
                                <div class="col-sm-8">
                                    <select name="icp_id" class="form-control" data-live-search="true" required>
                                        <option selected value="" disabled>Select Quarter</option>
                                        <option value=10>1st Quarter</option>
                                        <option value=20>2nd Quarter</option>
                                        <option value=30>3rd Quarter</option>
                                        <option value=40>4th Quarter</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-4 row">
                                <label for="regiment_id" class="col-sm-4 col-form-label">Regiment</label>
                                <div class="col-sm-8">
                                    @if(isset($regiments))
                                        <select name="regiment_id" class="form-control" data-live-search="true" required>
                                            <option selected>Select Regiment</option>
                                            @foreach($regiments as $regiment)
                                                <option value="{{ $regiment->id }}">{{ $regiment->regiment_name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Process</button>
                            <a href="{{ route('closing-balance') }}" class="btn btn-sm btn-dark">Refresh</a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="text-right mb-3">
                    <a href="{{ route('closing-balance-csv', ['year' => request()->input('year'), 'icp_id' => request()->input('icp_id'), 'regiment_id' => request()->input('regiment_id')]) }}" class="btn btn-sm btn-success mr-3">Export</a>
                </div>
                <table class="table table-bordered" id="balance">
                    <thead>
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Regimental Number</th>
                        <th class="text-center">Category</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Member Status</th>
                        <th class="text-center">Regiment</th>
                        <th class="text-center">Balance</th>
                    </tr>
                    </thead>

                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($contributions as $contribution)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $contribution->membership->regimental_number }}</td>
                            <td>{{ $contribution->membership->category->category_name }}</td>
                            <td>{{ $contribution->membership->ranks->rank_name ?? '-'}} {{ $contribution->membership->name ?? '-'}}</td>
                            <td>{{ $contribution->membership->status->status_name ?? '-'}}</td>
                            <td>{{ $contribution->membership->regiments->regiment_name ?? '-'}}</td>
                            <td>{{ number_format($contribution->closing_balance,2) ?? '-'}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#balance').DataTable({
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


