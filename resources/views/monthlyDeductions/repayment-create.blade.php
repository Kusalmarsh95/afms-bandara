@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Create Loan Batch</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('monthlyDeductions.index') }}" class="btn btn-sm btn-dark">Back</a>
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
        @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                <p>{{ $message }}</p>
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <div class="container-fluid">
                    <div class="row col-md-12">
                        <label class="col-md-8 col-form-label">Download excluded withdrawal application details</label>
                        <div class="text-right m-2">
                            <button type="button" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#partialModal">Partial Withdrawals</button>
                            <button type="button" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#fullModal">Full Withdrawals</button>
                        </div>
                    </div>
                    <hr>
                    <form method="POST" action="{{ route('repayment-batch') }}" enctype="multipart/form-data" id="upload-form">
                        @csrf
                        <div class="form-group row">
                            <div class="col-2 row">
                                <label for="year" class="col-sm-4 col-form-label">Year</label>
                                <div class="col-sm-8">
                                    <input type="number" name="year" value="{{ date('Y') }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-4 row">
                                <label for="month" class="col-sm-8 col-form-label">Contribution Month</label>
                                <div class="col-sm-4">
                                    <input type="number" min="1" max="12" name="month" value="{{ date('m') }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-6 row">
                                <label for="processing" class="col-sm-5 col-form-label">Exclude Withdrawals</label>
                                <div class="col-sm-7">
                                    <select name="processing" id="processing" class="form-control" data-live-search="true" required>
                                        <option selected value="" disabled>Select Excludes</option>
                                        <option value="3">Registered</option>
                                        <option value="4">Approved</option>
                                        <option value="5">To Disburse</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-6 text-right">
                            <button id="upload-button" type="submit" class="btn btn-sm btn-outline-primary">Create Batch</button>
                        </div>
                    </form>
                </div>
            </div>
            @if($recently->count() > 0)
                <div class="card-body">
                    <div class="col-sm-12">
                        <h5>Recently Created Batches</h5>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="width: 50px">No</th>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $i=0;
                        @endphp
                        @foreach ($recently as $recent)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $recent->year }}</td>
                                <td>{{ $recent->month }}</td>
                                <td>{{ $recent->created_at ? \Carbon\Carbon::parse($recent->created_at)->format('Y-m-d') : 'Date not specified' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div class="modal fade" id="partialModal" tabindex="-1" role="dialog" aria-labelledby="partialModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('partial-csv') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="partialModalLabel">Withdrawal</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <label for="processing" class="col-sm-5 col-form-label">Exclude Withdrawals</label>
                                <div class="col-sm-7">
                                    <select name="processing" id="processing" class="form-control" data-live-search="true" required>
                                        <option selected value="" disabled>Select Excludes</option>
                                        <option value="3">Registered</option>
                                        <option value="4">Approved</option>
                                        <option value="5">To Disburse</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="download" class="btn btn-sm btn-success">Download</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="fullModal" tabindex="-1" role="dialog" aria-labelledby="fullModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('full-csv') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="fullModalLabel">Full Withdrawal</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <label for="processing" class="col-sm-5 col-form-label">Exclude Withdrawals</label>
                                <div class="col-sm-7">
                                    <select name="processing" id="processing" class="form-control" data-live-search="true" required>
                                        <option selected value="" disabled>Select Excludes</option>
                                        <option value="3">Registered</option>
                                        <option value="4">Approved</option>
                                        <option value="5">To Disburse</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="download" class="btn btn-sm btn-success">Download</button>
                        </div>
                    </form>
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


