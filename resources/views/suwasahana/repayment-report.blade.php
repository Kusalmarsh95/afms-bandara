@extends('layouts.app')

@section('content')
    <div class="container">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Uploaded Suwasahana Repayments</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('suwasahana-repayment') }}" class="btn btn-sm btn-dark">Back to Upload Form</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <div class="card">
            <div class="card-header">
                <button class="tab-link" onclick="openPage('Summary', this, '#3e7d2c')" id="defaultOpen">Summary</button>
                <button class="tab-link" onclick="openPage('Failure', this, '#3e7d2c')">Failure</button>
                @php
                    $i=1;
                @endphp
                <div id="Summary" class="tab-content">
                    <table id="summaryTable" class="table table-striped table-bordered" style="width:100%; border-spacing:0" >
                        <thead>
                        <tr>
                            {{--                            <th>Member count</th>--}}
                            <th>Upload count</th>
                            <th>Failed Count</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            {{--                            <td>{{ $loanCount }}</td>--}}
                            <td>{{ $upload  ?? 0 }}</td>
                            <td>{{ $totalFailed ?? 0 }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
{{--                <div id="Success" class="tab-content">--}}
{{--                    @if (count($successes) > 0)--}}
{{--                        <table id="successTable" class="table table-striped table-bordered" style="width:100%; border-spacing:0" >--}}
{{--                            <thead>--}}
{{--                            <tr>--}}
{{--                                <th>No</th>--}}
{{--                                <th>Suwasahana Id</th>--}}
{{--                                <th>Regimental Number</th>--}}
{{--                                <th>Rank</th>--}}
{{--                                <th>Name</th>--}}
{{--                                <th>Capital</th>--}}
{{--                                <th>Interest</th>--}}
{{--                            </tr>--}}
{{--                            </thead>--}}
{{--                            <tbody>--}}
{{--                            @foreach ($successes as $success)--}}
{{--                                <tr>--}}
{{--                                    <td>{{ $i++ }}</td>--}}
{{--                                    <td>{{ $success['suwasahanaId'] }}</td>--}}
{{--                                    <td>{{ $success['regimentalNo'] }}</td>--}}
{{--                                    <td>{{ $success['rank'] }}</td>--}}
{{--                                    <td>{{ $success['name'] }}</td>--}}
{{--                                    <td>{{ $success['capital'] }}</td>--}}
{{--                                    <td>{{ $success['interest'] }}</td>--}}
{{--                                </tr>--}}
{{--                            @endforeach--}}
{{--                            </tbody>--}}
{{--                        </table>--}}
{{--                    @else--}}
{{--                        <div class="col-md-6 row">--}}
{{--                            <div class="col-sm-5 text-warning">--}}
{{--                                <span>No Success transactions</span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                </div>--}}
                <div id="Failure" class="tab-content">
                    @if (count($getFailures) > 0)
                        <a href="{{ route('suwasahana-failures',[$depositYear, $depositMonth]) }}" class="btn btn-sm btn-outline-success mb-2 float-sm-right">Download Failures</a>
                        <table id="failureTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Regimental Number</th>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Capital</th>
                                <th>Interest</th>
                                <th>Error</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($getFailures as $failure)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $failure['regimental_number'] }}</td>
                                    <td>{{ $failure['rank'] }}</td>
                                    <td>{{ $failure['name'] }}</td>
                                    <td>{{ $failure['capital'] }}</td>
                                    <td>{{ $failure['interest'] }}</td>
                                    <td>{{ $failure['error'] }}</td>
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
