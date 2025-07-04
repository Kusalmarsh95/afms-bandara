@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2><strong>Reports</strong></h2>
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
                    <a class="btn btn-outline-info m-4" href="{{ route('outstanding') }}" style="width: 320px;">
                        <img src="{{ asset('images/3d-checklist.jpg') }}" class="img-circle elevation-2" alt="Outstanding Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Outstanding Report
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-outline-info m-4" href="{{ route('outstanding-details') }}" style="width: 320px;">
                        <img src="{{ asset('images/checklist-document.png') }}" class="img-circle elevation-2" alt="Outstanding Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Outstanding Application Details
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-outline-primary m-4" href="{{ route('closing-balance') }}" style="width: 320px;">
                        <img src="{{ asset('images/donation.png') }}" class="img-circle elevation-2" alt="Contribution Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Closing Balance
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-outline-primary m-4" href="{{ route('member-contribution') }}" style="width: 320px;">
                        <img src="{{ asset('images/donation.png') }}" class="img-circle elevation-2" alt="Contribution Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Contributions
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-outline-warning m-4" href="{{ route('loan-installment') }}" style="width: 320px;">
                        <img src="{{ asset('images/tax.png') }}" class="img-circle elevation-2" alt="Loan Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Next Loan Installment
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-outline-success m-4" href="{{ route('disburse-loan') }}" style="width: 320px;">
                        <img src="{{ asset('images/loans.png') }}" class="img-circle elevation-2" alt="Loan Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Loan Bulk Payment
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-outline-dark m-4" href="{{ route('disburse-partial') }}" style="width: 320px;">
                        <img src="{{ asset('images/cash-withdrawal.png') }}" class="img-circle elevation-2" alt="Loan Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Partial Bulk Payment
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-outline-secondary m-4" href="{{ route('disburse-full') }}" style="width: 320px;">
                        <img src="{{ asset('images/withdraw.png') }}" class="img-circle elevation-2" alt="Loan Icon" style="width: 40px; height: 40px; margin: 8px;">
                        Full Bulk Payment
                    </a>
                </div>
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

