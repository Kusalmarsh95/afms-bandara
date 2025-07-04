@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Edit Interest</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('memberships.show', $membershipId) }}" class="btn btn-sm btn-dark">Back</a>
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
            <div class="card-body">
                <div id="devStage" class="form-card">
                    <div class="row">
                        <div class="col-md-1">
                            <strong>Year</strong>
                        </div>
                        <div class="col-md-2">
                            <strong>Opening Balance</strong>
                        </div>
                        <div class="col-md-2">
                            <strong>Interest</strong>
                        </div>
                        <div class="col-md-2">
                            <strong>Rate</strong>
                        </div>
                        <div class="col-md-2">
                            <strong>Contribution</strong>
                        </div>
                        <div class="col-md-2">
                            <strong>Closing Balance</strong>
                        </div>
                        <div class="col-md-1">
                            <strong>Action</strong>
                        </div>

                        @foreach ($yearlyContributions as $summary)
                            <form action="{{ route('update-calculation', $summary->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row data-row">
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <input type="text" name="year" value="{{ $summary->year }}" class="form-control " readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="text" class="form-control opening_balance" name="opening_balance" value="{{ $summary->opening_balance  }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="text"  class="form-control yearly_interest" name="yearly_interest" value="{{ $summary->yearly_interest }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="text" class="form-control interest_rate"  name="rate" value="{{ $summary->interest_rate  }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="text" class="form-control contribution_amount" name="contribution_amount" value="{{ $summary->contribution_amount  }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <input type="text" class="form-control closing_balance" name="closing_balance" value="{{ $summary->closing_balance }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-sm"><i class="fas fa-check-circle" style="color: rebeccapurple;"></i></button>
                                    </div>
                                </div>
                            </form>
                            <div class="col-md-1">
                                <button class="btn delete-button btn-sm" data-id="{{ $summary->id }}"><i class="fas fa-trash-alt" style="color: red;"></i></button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this member?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn cancel-button btn-secondary">Cancel</button>
                    <form id="deleteInterestForm" method="POST" action="">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);

            $(document).on('click', '.delete-button', function () {
                var interestId = $(this).data('id');
                var form = $('#deleteInterestForm');
                var action = '{{ route('interest-destroy', '') }}/' + interestId;
                form.attr('action', action);
                $('#confirmDeleteModal').modal('show');
            });
            $(document).on('click', '.cancel-button', function() {
                $('#confirmDeleteModal').modal('hide');
            });

            function calculateValuesForRow(row) {
                var openingBalance = parseFloat(row.find('.opening_balance').val()) || 0;
                var interestRate = parseFloat(row.find('.interest_rate').val()) || 0;
                var contributionAmount = parseFloat(row.find('.contribution_amount').val()) || 0;

                var yearlyInterest = openingBalance * (interestRate/100);

                var closingBalance = openingBalance + yearlyInterest + contributionAmount;

                row.find('.yearly_interest').val(yearlyInterest.toFixed(2));
                row.find('.closing_balance').val(closingBalance.toFixed(2));
            }

            $('.data-row').each(function() {
                calculateValuesForRow($(this));
            });

            $(document).on('input', '.opening_balance, .interest_rate, .contribution_amount', function () {
                var row = $(this).closest('.data-row');
                calculateValuesForRow(row);
            });
        });
    </script>
@endsection


