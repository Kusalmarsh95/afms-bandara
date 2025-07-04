@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h3>Quarterly Interest Calculation</h3>
                    </div>
{{--                    <div class="col-sm-6">--}}
{{--                        <ol class="breadcrumb float-sm-right m-1">--}}
{{--                            <li class="breadcrumb-item">--}}
{{--                                <a href="{{ route('contribution-interests.index') }}" class="btn btn-sm btn-dark">Back</a>--}}
{{--                            </li>--}}
{{--                        </ol>--}}
{{--                    </div>--}}
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
                    <form action="{{ route('store-calculation')}}" id="upload-form" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="year" class="col-sm-4 col-form-label">Year</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="year" class="form-control" value="{{ $interest->year }}" readonly>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="icp_id" class="col-sm-4 col-form-label">Quarter</label>
                                    <div class="col-sm-8">
                                        <input type="hidden" name="icp_id" class="form-control" value="{{ $interest->icp_id }}">
                                        <input type="text" class="form-control" value="@if ($interest->icp_id == 0) Yearly
                                        @elseif ($interest->icp_id == 1) 1st Half
                                        @elseif ($interest->icp_id == 2) 2nd Half
                                        @elseif ($interest->icp_id == 10) 1st Quarter
                                        @elseif ($interest->icp_id == 20) 2nd Quarter
                                        @elseif ($interest->icp_id == 30) 3rd Quarter
                                        @elseif ($interest->icp_id == 40) 4th Quarter
                                        @else Other @endif" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="interest_rate" class="col-sm-4 col-form-label">Interest Rate</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="interest_rate" class="form-control" value="{{ $interest->interest_rate }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="regiment_id" class="col-sm-4 col-form-label">Regiment</label>
                                    <div class="col-sm-8">
                                        @if(isset($regiments))
                                            <select name="regiment_id" class="form-control" data-live-search="true" required>
                                                <option value="" disabled selected>Select Regiment</option>
                                                @foreach($regiments as $regiment)
                                                    <option value="{{ $regiment->id }}">{{ $regiment->regiment_name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="category_id" class="col-sm-4 col-form-label">Rank Type</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" data-live-search="true" name="category_id" required>
                                            <option value="" disabled selected>Select Status</option>
                                            <option value=1>Officers</option>
                                            <option value=2>Other Ranks</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Commit Interest</button>
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
                            <th>Year</th>
                            <th>Quarter</th>
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
                                <td>{{ $recent->regiments->regiment_name ?? '' }}</td>
                                <td>{{ $recent->category->category_name ?? ''}}</td>
                                <td>{{ $recent->count ?? ''}}</td>
                                <td>{{ $recent->year ?? ''}}</td>
                                <td>@if ($recent->icp_id == 10)
                                        1st Quarter
                                    @elseif ($recent->icp_id == 20)
                                        2nd Quarter
                                    @elseif ($recent->icp_id == 30)
                                        3rd Quarter
                                    @elseif ($recent->icp_id == 40)
                                        4th Quarter
                                    @else
                                        N/A
                                    @endif</td>
                                <td>{{ $recent->date ? \Carbon\Carbon::parse($recent->date)->format('Y-m-d') : 'Date not specified' }}</td>
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
            $('#upload-form').on('submit', function (e) {
                $('#uploadingModal').modal({
                    backdrop: 'static', // Prevent closing the modal by clicking outside
                    keyboard: false     // Prevent closing with the Esc key
                });
                $('#uploadingModal').modal('show');
            });

            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);
        });
    </script>
@endsection


