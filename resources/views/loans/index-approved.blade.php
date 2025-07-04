@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2><strong>Approved Loan Applications</strong></h2>
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
    <div class="card">
        <div class="card-header">
            @can('loans-applications-to-disburse')
                <button class="tab-link" onclick="openPage('Approved', this, '#3e7d2c')" id="defaultOpen">Send to Bulk</button>
            @endcan
            @can('loans-applications-to-disburse')
                <button class="tab-link" onclick="openPage('Pay', this, '#3e7d2c')" id="defaultOpen">Release Payment</button>
            @endcan
            @can('loans-applications-disburse')
                <button class="tab-link" onclick="openPage('Assign', this, '#3e7d2c')" id="defaultOpen">Ready to Bank</button>
            @endcan
            <div id="Approved" class="tab-content">
                <table class="table table-bordered" id="approved">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th style="width:100px">Application No</th>
                        <th style="width:200px">Name</th>
                        <th>Registered Date</th>
                        <th>Processing Location</th>
                        <th>Amount</th>
                        <th style="width:150px">Action</th>
                        <th><input type="checkbox" id="select-all"></th>
                    </tr>
                    </thead>
                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($loans as $loan)
                        @if($loan->processing == 4)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $loan->application_reg_no ?? '-' }}</td>
                                <td>{{ $loan->membership->ranks->rank_name ?? '-'}} {{ $loan->membership->name ?? '-'}}</td>
                                <td>{{ $loan->registered_date ? (new DateTime($loan->registered_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $loan->userName }}</td>
                                <td class="text-right">{{ number_format($loan->approved_amount, 2) }}</td>
                                <td class="text-center">
                                    @can('loans-applications-approved-show')
                                        <a class="btn" href="{{ route('loan.approved',$loan->id) }}"><i class="fas fa-clipboard-check" style="color: lightseagreen;"></i></a>
                                    @endcan
                                </td>
                                <td><input type="checkbox" class="loan-checkbox" data-amount="{{ $loan->approved_amount }}" value="{{ $loan->id }}"></td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <div class="card-body">
                    <div class="text-right">
                        <strong>Total Selected Amount: </strong>
                        <span id="total-amount">0.00</span>
                    </div>
                    <div class="mb-3 text-right">
                        <button id="send-to-bulk-btn" class="btn btn-success" disabled>Send to Bulk</button>
                    </div>
                </div>
            </div>
            <div id="Pay" class="tab-content">
                <div class="col-md-12 text-right mb-3">
                    <div class="col-md-12 text-right mb-3">
                        @if($sendToBankCount>0)
                            <button type="button" class="btn btn-sm btn-outline-success  m-2" data-toggle="modal" data-target="#payModal">Release Payment</button>
                        @endif
                    </div>
                </div>
                <table class="table table-bordered" id="pay">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th style="width:100px">Regimental number</th>
                        <th style="width:200px">Name</th>
                        <th>Account Number</th>
                        <th>Bank Code</th>
                        <th style="width:150px">Branch Code</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    @php
                        $i=0;
                        $total = 0;
                    @endphp
                    <tbody>
                    @foreach ($loans as $loan)
                        @if($loan->processing == 5)
                            <tr>
                                <td class="text-center">{{ ++$i }}</td>
                                <td>{{ $loan->membership->regimental_number }}</td>
                                <td>{{ $loan->membership->ranks->rank_name ?? '--' }} {{ $loan->membership->name }} {{ $loan->membership->regiments->regiment_name ?? '--' }}</td>
                                <td>{{ $loan->membership->account_no ?? '--' }}</td>
                                <td>{{ $loan->membership->bank_code ?? '--' }}</td>
                                <td>{{ $loan->membership->branch_code ?? '--' }}</td>
                                <td class="text-right">{{ number_format($loan->approved_amount, 2) }}</td>
                            </tr>
                            @php
                                $total += $loan->approved_amount;
                            @endphp
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <div class="text-right">
                    <strong>Total Amount: {{ number_format($total ?? '0',2) }}</strong>
                </div>
            </div>
            <div id="Assign" class="tab-content">
                <div class="col-md-12 text-right mb-3">
                    @if($payCount>0)
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('loan-custout') }}">Cust Out <i class="fas fa-file-csv text-green"></i> </a>
                            <button type="button" class="btn btn-sm btn-success  m-2" data-toggle="modal" data-target="#bankedModal">Send to Bank</button>
                    @endif
                    @if($sendToBankCount>0 || $payCount>0)
                            <a class="btn btn-sm btn-outline-warning" href="{{ route('pdf-disburse-loan') }}">Download <i class="fas fa-file-pdf text-red"></i> </a>
                    @endif
                </div>
                <table class="table table-bordered" id="assign">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th style="width:100px">Regimental number</th>
                        <th style="width:200px">Name</th>
                        <th>Account Number</th>
                        <th>Bank Code</th>
                        <th style="width:150px">Branch Code</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    @php
                        $i=0;
                        $total = 0;
                    @endphp
                    <tbody>
                    @foreach ($loans as $loan)
                        @if($loan->processing > 5)
                            <tr>
                                <td class="text-center">{{ ++$i }}</td>
                                <td>{{ $loan->membership->regimental_number }}</td>
                                <td>{{ $loan->membership->ranks->rank_name ?? '--' }} {{ $loan->membership->name }} {{ $loan->membership->regiments->regiment_name ?? '--' }}</td>
                                <td>{{ $loan->membership->account_no ?? '--' }}</td>
                                <td>{{ $loan->membership->bank_code ?? '--' }}</td>
                                <td>{{ $loan->membership->branch_code ?? '--' }}</td>
                                <td class="text-right">{{ number_format($loan->approved_amount, 2) }}</td>
                            </tr>
                            @php
                                $total += $loan->approved_amount;
                            @endphp
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <div class="text-right">
                    <strong>Total Amount: {{ number_format($total ?? '0',2) }}</strong>
                </div>
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
                        <h5 class="modal-title" id="payModalLabel">Send to Bank</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <label class="col-form-label text-center">Send Loan Applications to Bank</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="alert alert-warning text-center">
                            Please make sure to download the voucher. You will not be able to download voucher again.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="approval" value="banked" class="btn btn-success">Bank</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('loan.pay') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title" id="bankedModalLabel">Loan Disbursement</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <label class="col-form-label text-center">Release payment of Loan Applications</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="approval" value="pay" class="btn btn-success">Pay</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#approved').DataTable({
                responsive: true,
            });
            $('#pay').DataTable({
                responsive: true,
            });
            $('#assign').DataTable({
                responsive: true,
                // searching: false,
                // paging:false,
            });

            $('#select-all').on('click', function() {
                var isChecked = this.checked;
                $('.loan-checkbox').each(function() {
                    this.checked = isChecked;
                    updateTotalAmount();
                    toggleBulkButton();
                });
            });

            $(document).on('change', '.loan-checkbox', function() {
                updateTotalAmount();
                toggleBulkButton();
            });

            function updateTotalAmount() {
                var total = 0;
                $('.loan-checkbox:checked').each(function() {
                    total += parseFloat($(this).data('amount'));
                });
                $('#total-amount').text(total.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            }

            // Enable "Add to Bulk" button when any checkbox is selected
            function toggleBulkButton() {
                var selectedLoans = $('.loan-checkbox:checked').length;
                if (selectedLoans > 0) {
                    $('#send-to-bulk-btn').prop('disabled', false);
                } else {
                    $('#send-to-bulk-btn').prop('disabled', true);
                }
            }
            $('#send-to-bulk-btn').on('click', function() {
                var selectedLoans = [];


                // Collect the loan IDs of the selected loans
                $('.loan-checkbox:checked').each(function() {
                    selectedLoans.push($(this).val());
                });

                // Proceed only if loans are selected
                if (selectedLoans.length > 0) {
                    // Send the request to update the processing field
                    $.ajax({
                        url: '{{ route('loan.sendToBulk') }}',
                        method: 'POST',
                        data: {
                            loan_ids: selectedLoans,
                            _token: '{{ csrf_token() }}'  // CSRF token for security
                        },
                        success: function(response) {
                            alert(response.message);  // You can show a success message
                            $('#sendBulkModal').modal('hide');  // Hide the modal after success
                            location.reload();  // Reload the page to see the updated statuses
                        },
                        error: function(xhr, status, error) {
                            console.error('Error status:', status);
                            console.error('Error message:', error);
                            console.error('Response:', xhr.responseText);
                            alert('An error occurred while updating the status.');
                        }
                    });
                } else {
                    alert('Please select at least one loan.');
                }
            });


            setTimeout(function () {
                $('.alert').fadeOut();
            }, 8000);

        });
    </script>
@endsection
@push('scripts')
    <script src="{{ asset('/js/tab-index.js') }}"> </script>
@endpush

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('/css/tab-index.css') }}"/>
@endpush
