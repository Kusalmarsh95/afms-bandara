@extends('layouts.app')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h3>Member Contribution</h3>
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
                <form method="GET" action="{{ route('member-contribution') }}">
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
                    <div class="col-md-12 text-center">
                        <a href="{{ route('member-contribution') }}" class="btn btn-sm btn-dark">Refresh</a>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Process</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="text-right mb-3">
                <a href="{{ route('member-contribution-csv', ['category_id' => request()->input('category_id'), 'regiment_id' => request()->input('regiment_id'), 'type' => request()->input('type')]) }}" class="btn btn-sm btn-success mr-3">Export</a>
            </div>
            <table class="table table-bordered" id="balance">
                <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Reg Number</th>
                    <th class="text-center" style="width: 70px">E Number</th>
                    <th class="text-center">Rank Type</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Regiment</th>
                    <th class="text-center">Contribution</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $i=0;
                @endphp
                @foreach ($memberships as $membership)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $membership->regimental_number }}</td>
                        <td>{{ $membership->enumber }}</td>
                        <td>{{ $membership->category->category_name }}</td>
                        <td>{{ $membership->ranks->rank_name ?? 'NA '}} {{ $membership->name ?? '-'}}</td>
                        <td>{{ $membership->regiments->regiment_name ?? ' NA '}}</td>
                        <td>{{ number_format($membership->contribution_amount,2) ?? '-'}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var table = $('#balance').DataTable({
                responsive: true,
                paging: true,
                info: false,
                buttons: []
            });

            setTimeout(function() {
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
        });
    </script>
@endsection
