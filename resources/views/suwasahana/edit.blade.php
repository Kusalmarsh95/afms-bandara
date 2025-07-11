@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3><strong>Edit Suwasahana Details</strong></h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-dark" href="{{ route('memberships.show', $suwasahana->member_id) }}">Back</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

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
                    <form id="suwasahanaForm" action="{{ route('suwasahana.update', $suwasahana->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-header">
                            <h5>{{ $suwasahana->membership->ranks->rank_name ?? '-'}} {{ $suwasahana->membership->name ?? '-'}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="ABFvoucherno" class="col-sm-6 col-form-label">Voucher No</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="ABFvoucherno" class="form-control" value="{{ $suwasahana->ABFvoucherno ?? '0' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="Issue_Date" class="col-sm-6 col-form-label">Issued Date</label>
                                    <div class="col-sm-6">
                                        <input type="date" name="Issue_Date" class="form-control" value="{{ $suwasahana->Issue_Date ?? '' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="total_capital" class="col-sm-6 col-form-label">Principal Amount</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="total_capital" class="form-control" value="{{ $suwasahana->total_capital ?? '0' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="total_interest" class="col-sm-6 col-form-label">Interest Amount</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="total_interest" class="form-control" value="{{ $suwasahana->total_interest ?? '0' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="total_recovered_capital" class="col-sm-6 col-form-label">Recovered Amount</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="total_recovered_capital" class="form-control" value="{{ $suwasahana->total_recovered_capital ?? '0' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="total_recovered_interest" class="col-sm-6 col-form-label">Recovered Interest</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="total_recovered_interest" class="form-control" value="{{ $suwasahana->total_recovered_interest ?? '0' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="settle_amount" class="col-sm-6 col-form-label">To Settle Amount</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="settle_amount" class="form-control" value="{{ ($suwasahana->total_capital - $suwasahana->total_recovered_capital) ?? '0' }}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="settle_interest" class="col-sm-6 col-form-label">To Settle Interest</label>
                                    <div class="col-sm-6">
                                        <input type="text" name="settle_interest" class="form-control" value="{{ ($suwasahana->total_interest - $suwasahana->total_recovered_interest) ?? '0' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="settled" class="col-sm-6 col-form-label">Settled</label>
                                    <select class="form-control col-sm-6" name="settled" required>
                                        <option value="" readonly>Select</option>
                                        <option value="1" {{ $suwasahana->settled == 1 ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ $suwasahana->settled == 0 ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                                <div class="col-6 row">
                                    <label for="settled_date" class="col-sm-6 col-form-label">Settled Date</label>
                                    <div class="col-sm-6">
                                        <input type="date" name="settled_date" class="form-control" value="{{ $suwasahana->settled_date ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#confirmModal">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="confirmModalLabel">Confirm Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to update Suwasahana details?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('suwasahanaForm').submit();">Yes, Update</button>
                </div>
            </div>
        </div>
    </div>
@endsection
