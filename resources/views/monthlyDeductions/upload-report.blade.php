@extends('layouts.app')

@section('content')
    <div class="container">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Uploaded Contributions</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('contribution-upload') }}" class="btn btn-sm btn-dark">Back to Upload Form</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <div class="card">
            <div class="card-header">
                <button class="tab-link" onclick="openPage('Summary', this, '#3e7d2c')" id="defaultOpen">Summary</button>
{{--                                <button class="tab-link" onclick="openPage('Success', this, '#3e7d2c')" id="default">Success</button>--}}
                <button class="tab-link" onclick="openPage('Failure', this, '#3e7d2c')">Failure</button>
                <div id="Summary" class="tab-content">
                    <table id="summaryTable" class="table table-striped table-bordered" style="width:100%; border-spacing:0" >
                        <thead>
                        <tr>
                            {{--                            <th>Member count</th>--}}
                            <th>Success count</th>
                            <th>Updated count</th>
                            <th>Failed count</th>
{{--                            <th>Members Amount</th>--}}
{{--                            <th>Upload Amount</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            {{--                            <td>{{ $memberCount }}</td>--}}
                            <td>{{ $summary['inserted']  ?? 0 }}</td>
                            <td>{{ $summary['updated'] ?? 0 }}</td>
                            <td>{{ $summary['failed'] ?? 0 }}</td>
{{--                            <td>LKR {{ number_format($summary['total_amount'] ?? 0, 2) }}</td>--}}
{{--                            <td>LKR {{ number_format($summary['failure_amount'] ?? 0, 2) }}</td>--}}
                        </tr>
                        </tbody>
                    </table>
                </div>

                @php
                    $i = 1;
                @endphp
                                <div id="Failure" class="tab-content">
                                    @if (count($failures) > 0)
                                        <a href="{{ route('download.failures', [$depositYear, $depositMonth, $reqCategory]) }}" class="btn btn-sm
                                        btn-outline-success mb-2 float-sm-right">Download Failures</a>
                                        <table id="failureTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>E No</th>
                                                <th>Unit</th>
                                                <th>Regimental Number</th>
                                                <th>Rank</th>
                                                <th>Name</th>
                                                <th>Amount</th>
                                                <th>Error</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($failures as $failure)
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $failure['enumber'] }}</td>
                                                    <td>{{ $failure['unit'] }}</td>
                                                    <td>{{ $failure['regimental_number'] }}</td>
                                                    <td>{{ $failure['rank'] }}</td>
                                                    <td>{{ $failure['name'] }}</td>
                                                    <td>{{ $failure['amount'] }}</td>
                                                    <td>{{ $failure['reason'] }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="col-md-6 row">
                                            <div class="col-sm-5 text-warning">
                                                <span>No Failure transactions</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('/js/tab-index.js') }}"> </script>
@endpush

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('/css/tab-index.css') }}"/>
@endpush
