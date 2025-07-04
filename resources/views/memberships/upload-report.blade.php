@extends('layouts.app')

@section('content')
    <div class="container">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Uploaded Member Changes</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('changes-upload') }}" class="btn btn-sm btn-dark">Back to Upload Form</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        <div class="card">
            <div class="card-header">
                <button class="tab-link" onclick="openPage('Success', this, '#3e7d2c')" id="defaultOpen">Success</button>
                <button class="tab-link" onclick="openPage('Failure', this, '#3e7d2c')">Failure</button>
                @php
                    $i=1;
                @endphp
                <div id="Success" class="tab-content">
                    @if (count($successes) > 0)
                        <table id="successTable" class="table table-striped table-bordered" style="width:100%; border-spacing:0" >
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>E No</th>
                                <th>Regimental Number</th>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($successes as $success)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $success['enumber'] }}</td>
                                    <td>{{ $success['regimentalNo'] }}</td>
                                    <td>{{ $success['rank'] }}</td>
                                    <td>{{ $success['name'] }}</td>
                                    <td>{{ $success['status'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="col-md-6 row">
                            <div class="col-sm-5 text-warning">
                                <span>No Success transactions</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div id="Failure" class="tab-content">
                    @if (count($failures) > 0)
                        <a href="{{ route('download.failures') }}" class="btn btn-sm btn-outline-success mb-2 float-sm-right">Download Failures</a>
                        <table id="failureTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>E No</th>
                                <th>Regimental Number</th>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Error</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($failures as $failure)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $failure['enumber'] }}</td>
                                    <td>{{ $failure['regimentalNo'] }}</td>
                                    <td>{{ $failure['rank'] }}</td>
                                    <td>{{ $failure['name'] }}</td>
                                    <td>{{ $failure['status'] }}</td>
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
