@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="container-fluid">
                <div class="pull-right">
                    <a class="btn btn-primary mt-1" href="{{ route('reports.index') }}">Back</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <h4 class="text-center mt-1"> Bank Deposit List</h4>
                </div>
            </div>
            <div class="container-fluid">
                <div class="card p-3">
                    <div class="row">
                        @if ($loans->count() > 0)
                            <div class="col-md-12 text-right mb-3">
                                <button type="button" class="btn btn-sm btn-outline-success  m-2" data-toggle="modal" data-target="#bankedModal">Banked <i class="fas fa-bank text-green"></i></button>
                                <a class="btn btn-sm btn-outline-warning" href="{{ route('pdf-disburse-loan') }}">Download <i class="fas fa-file-pdf text-red"></i> </a>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered table-secondary">
                                <thead>
                                <tr>
                                    <th class="text-center" scope="col">Ser No.</th>
                                    <th class="text-center" scope="col">Regimental number</th>
                                    <th class="text-center" scope="col">Name</th>
                                    <th class="text-center" scope="col">Unit</th>
                                    <th class="text-center" scope="col">Account No</th>
                                    <th class="text-center" scope="col">Bank</th>
                                    <th class="text-center" scope="col">Branch</th>
                                    <th class="text-center" scope="col">Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($loans as $loan)
                                    <tr>
                                        <td class="text-center">{{ ++$i }}</td>
                                        <td>{{ $loan->membership->regimental_number }}</td>
                                        <td>{{ $loan->membership->ranks->rank_name ?? '--' }} {{ $loan->membership->name }}</td>
                                        <td class="text-center">{{ $loan->membership->regiments->regiment_name ?? '--' }}</td>
                                        <td>{{ $loan->membership->account_no ?? '--' }}</td>
                                        <td>{{ $loan->membership->bank_code ?? '--' }}</td>
                                        <td>{{ $loan->membership->branch_code ?? '--' }}</td>
                                        <td class="text-right">{{ number_format($loan->approved_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td class="text-center" colspan="7">Total</td>
                                    <td class="text-right">{{ number_format($total, 2) }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="bankedModal" tabindex="-1" role="dialog" aria-labelledby="bankedModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="{{ route('loan.banked') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="bankedModalLabel">Final Disbursement</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <label class="col-form-label text-center">Loan Amounts deposit to bank</label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="approval" value="banked" class="btn btn-success">Banked</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

