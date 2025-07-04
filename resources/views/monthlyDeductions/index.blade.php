@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2><strong>Monthly Bulk Uploads</strong></h2>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="card m-1">
        <div class="card-body">
            <div class="row offset-1">
                <div class="col-md-6">
                    <a class="btn btn-outline-primary m-4" href="{{ route('contribution-upload') }}" style="width: 320px;">
                        <img src="{{ asset('images/donation.png') }}" class="img-circle elevation-2" alt="Contribution Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Upload Monthly Contribution
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-outline-warning m-4" href="{{ route('repayment-create') }}" style="width: 320px;">
                        <img src="{{ asset('images/tax.png') }}" class="img-circle elevation-2" alt="Loan Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Create Loan Batch
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-outline-warning m-4" href="{{ route('repayment-upload') }}" style="width: 320px;">
                        <img src="{{ asset('images/tax.png') }}" class="img-circle elevation-2" alt="Loan Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Upload Loan Repayment
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-outline-success m-4" href="{{ route('changes-upload') }}" style="width: 320px;">
                        <img src="{{ asset('images/team.png') }}" class="img-circle elevation-2" alt="Loan Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Upload Member Changes
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-outline-info m-4" href="{{ route('suwasahana-repayment') }}" style="width: 320px;">
                        <img src="{{ asset('images/suwasahana.png') }}" class="img-circle elevation-2" alt="Suwasahana Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Upload Suwasahana Repayment
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('.alert').fadeOut();
            }, 2000);
        });
    </script>
@endsection

