@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Upload Contribution</h3>
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
        @if ($message = Session::get('message'))
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
                    <form method="POST" action="{{ route('upload-xml') }}" enctype="multipart/form-data" id="upload-form">
                        @csrf
                        <div class="form-group row">
                            <div class="col-6 row">
                                <label for="category_id" class="col-sm-4 col-form-label">Rank Type</label>
                                <div class="col-sm-8">
                                    <select name="category_id" id="category_id" class="form-control" data-live-search="true" required>
                                        <option selected value="" disabled>Select Type</option>
                                        <option value="OFFRSNO">Officer</option>
                                        <option value="REGTLNO">Other Ranker</option>
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
                                                <option value="{{ $regiment->regiment_name }}">{{ $regiment->regiment_name }}</option>
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
                                    <input type="number" name="deposit_year" value="{{ date('Y') }}" class=" col-sm-4 form-control" required>
                                </div>
                            </div>
                            <div class="col-6 row">
                                <label for="deposit_month" class="col-sm-4 col-form-label">Contribution Month</label>
                                <div class="col-sm-8">
                                    <input type="number" name="deposit_month" value="{{ date('n') }}" class="col-sm-4 form-control"
                                           min="1" max="12" required>
{{--                                    <input type="number" min="1" max="12" name="deposit_month" value="{{ date('m') }}" class="col-sm-4 form-control" required>--}}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="xml_file">Upload XML File</label>
                            <input type="file" name="xml_file" id="xml_file" class="form-control" required>
                        </div>
                        <div class="col-md-6 text-right">
                            <button id="upload-button" type="submit" class="btn btn-sm btn-outline-primary">Upload and Process XML</button>
                        </div>
                    </form>
                </div>
            </div>
            @if($recently->count() > 0)
                <div class="card-body">
                    <div class="col-sm-12">
                        <h5>Recently Updated</h5>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th style="width: 50px">No</th>
                            <th>Regiment</th>
                            <th>Rank Type</th>
                            <th>count</th>
                            <th>Success Count</th>
                            <th>Year</th>
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
                                <td>{{ $recent->regiment ?? ''}}</td>
                                <td>{{ $recent->category->category_name ?? ''}}</td>
                                <td>{{ $recent->pnr_count }}</td>
                                <td>{{ $recent->success_count }}</td>
                                <td>{{ $recent->year }}</td>
                                <td>{{ $recent->created_at ? \Carbon\Carbon::parse($recent->created_at)->format('Y-m-d') : 'Date not specified' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div class="modal fade" id="uploadingModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Uploading...</span>
                        </div>
                        <h5 class="mt-3">Uploading...</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);

            $('#upload-form').on('submit', function (e) {
                $('#uploadingModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#uploadingModal').modal('show');
            });
            $('#category_id').on('change', function() {
                var selectedValue = $(this).val();
                if (selectedValue === 'REGTLNO') {
                    $('#regiment_id').show();
                    $('#type').hide();
                } else {
                    $('#regiment_id').hide();
                    $('#type').show();
                }
            });
        });

    </script>
@endsection


