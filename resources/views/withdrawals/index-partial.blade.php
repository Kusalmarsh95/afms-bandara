@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h3><strong>Registered Partial Withdrawals</strong></h3>
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
            <button class="tab-link" onclick="openPage('Processing', this, '#3e7d2c')" id="defaultOpen">Processing</button>
            <button class="tab-link" onclick="openPage('Approved', this, '#3e7d2c')">Approved</button>
            <button class="tab-link" onclick="openPage('Rejected', this, '#3e7d2c')">Rejected</button>
            <button class="tab-link" onclick="openPage('Assign', this, '#3e7d2c')">Assign To Me</button>
            <div id="Processing" class="tab-content">
                <table id="processing" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Application Number</th>
                        <th>Withdrawal Type</th>
                        <th>Registered Date</th>
                        <th>Processing Location</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($partialWithdrawals as $partialWithdrawal)
                        @if($partialWithdrawal->processing==1 | $partialWithdrawal->processing==3)
                            <tr>
                                <td>{{ $partialWithdrawal->membership->ranks->rank_name ?? '-' }} {{ $partialWithdrawal->membership->name ?? '-' }}</td>
                                <td>{{ $partialWithdrawal->application_reg_no ?? '-' }}</td>
                                <td>
                                    @if ($partialWithdrawal->withdrawal_product == 1)
                                        80% Withdrawal
                                    @elseif ($partialWithdrawal->withdrawal_product == 2)
                                        50% Withdrawal
                                    @else
                                        Other
                                    @endif
                                    @if ($partialWithdrawal->special == 1) <label class="badge badge-warning">Special</label>@endif
                                </td>
                                <td>{{ $partialWithdrawal->received_date ? (new DateTime($partialWithdrawal->received_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $partialWithdrawal->userName }}</td>
                                <td class="text-center">
                                    <a class="btn" href="{{ route('withdrawals.show',$partialWithdrawal->id) }}"><i class="fas fa-eye" style="color: lightseagreen;"></i></a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div id="Approved" class="tab-content">
                <table id="approved" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Application Number</th>
                        <th>Withdrawal Type</th>
                        <th>Registered Date</th>
                        <th>Processing Location</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($partialWithdrawals as $partialWithdrawal)
                        @if($partialWithdrawal->processing==4 |$partialWithdrawal->processing==5)
                            <tr>
                                <td>{{ $partialWithdrawal->membership->ranks->rank_name ?? '-' }} {{ $partialWithdrawal->membership->name ?? '-' }}</td>
                                <td>{{ $partialWithdrawal->application_reg_no ?? '-' }}</td>
                                <td>
                                    @if ($partialWithdrawal->withdrawal_product == 1)
                                        80% Withdrawal
                                    @elseif ($partialWithdrawal->withdrawal_product == 2)
                                        50% Withdrawal
                                    @else
                                        Other
                                    @endif
                                        @if ($partialWithdrawal->special == 1) <label class="badge badge-warning">Special</label>@endif
                                </td>
                                <td>{{ $partialWithdrawal->received_date ? (new DateTime($partialWithdrawal->received_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $partialWithdrawal->userName }}</td>
                                <td class="text-center">
                                    <a class="btn" href="{{ route('withdrawals.show',$partialWithdrawal->id) }}"><i class="fas fa-eye" style="color: lightseagreen;"></i></a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div id="Rejected" class="tab-content">
                <table id="rejected" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Application Number</th>
                        <th>Withdrawal Type</th>
                        <th>Rejected Date</th>
                        <th>Reason</th>
                        <th style="width: 60px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($partialWithdrawals as $partialWithdrawal)
                        @if($partialWithdrawal->processing==2)
                            <tr>
                                <td>{{ $partialWithdrawal->membership->ranks->rank_name ?? '-' }} {{ $partialWithdrawal->membership->name ?? '-' }}</td>
                                <td>{{ $partialWithdrawal->application_reg_no ?? '-' }}</td>
                                <td>
                                    @if ($partialWithdrawal->withdrawal_product == 1)
                                        80% Withdrawal
                                    @elseif ($partialWithdrawal->withdrawal_product == 2)
                                        50% Withdrawal
                                    @else
                                        Other
                                    @endif
                                        @if ($partialWithdrawal->special == 1) <label class="badge badge-warning">Special</label>@endif
                                </td>
                                <td>{{ $partialWithdrawal->reject_date ? (new DateTime($partialWithdrawal->reject_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $partialWithdrawal->rejectReason->reason_name ?? '-' }}</td>
                                <td class="text-center">
                                    @can('withdrawals-partial-edit')
                                        <a class="btn" href="{{ route('withdrawals.editPartial',$partialWithdrawal->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                                    @endcan
                                    @can('withdrawals-partial-approve')
                                         <a class="btn" href="{{ route('partial-view',$partialWithdrawal->id) }}"><i class="fas fa-user-check" style="color: yellowgreen;"></i></a>
                                    @endcan
                                    @can('withdrawals-partial-disburse')
                                         <a class="btn" href="{{ route('partial-approved',$partialWithdrawal->id) }}"><i class="fas fa-clipboard-check" style="color: yellowgreen;"></i></a>
                                    @endcan
                                    @can('withdrawals-partial-delete')
                                    <button class="btn delete-button" data-id="{{ $partialWithdrawal->id }}">
                                        <i class="fas fa-trash-alt" style="color: red;"></i>
                                    </button>
                                    @endcan
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div id="Assign" class="tab-content">
                <table id="assign" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Application Number</th>
                        <th>Registered Date</th>
                        <th>Remark</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($partialWithdrawals as $partialWithdrawal)
                        @if($partialWithdrawal->userName == Auth::user()->name)
                            <tr>
                                <td>{{ $partialWithdrawal->membership->ranks->rank_name ?? '-' }} {{ $partialWithdrawal->membership->name ?? '-' }}</td>
                                <td>{{ $partialWithdrawal->application_reg_no ?? '-' }}</td>
                                <td>{{ $partialWithdrawal->received_date ? (new DateTime($partialWithdrawal->received_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $partialWithdrawal->reason }}</td>
                                <td class="text-center">
                                    @can('withdrawals-partial-edit')
                                        <a class="btn" href="{{ route('withdrawals.editPartial',$partialWithdrawal->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                                    @endcan
                                    @can('withdrawals-partial-show')
                                        <a class="btn" href="{{ route('partial-view',$partialWithdrawal->id) }}"><i class="fas fa-user-check" style="color: yellowgreen;"></i></a>
                                    @endcan
                                    @can('withdrawals-partial-approved-show')
                                        <a class="btn" href="{{ route('partial-approved',$partialWithdrawal->id) }}"><i class="fas fa-clipboard-check" style="color: yellowgreen;"></i></a>
                                    @endcan
                                    @can('withdrawals-partial-delete')
                                        <button class="btn delete-button" data-id="{{ $partialWithdrawal->id }}">
                                            <i class="fas fa-trash-alt" style="color: red;"></i>
                                        </button>
                                    @endcan
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
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
                    <form id="deletePartial" method="POST" action="">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#processing').DataTable({
                responsive: true,
                // searching:false,
                buttons: [],
                // paging: false,
                // info: false,
            });
            $('#approved').DataTable({
                responsive: true,
                // searching:false,
                buttons: [],
                // paging: false,
                // info: false,
            });
            $('#rejected').DataTable({
                responsive: true,
                // paging: false,
                // info: false,
                buttons: [
                ]
            });
            $('#assign').DataTable({
                responsive: true,
                // paging: false,
                // info: false,
                buttons: [
                ]
            });
            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);

            $(document).on('click', '.delete-button', function () {
                var partiaId = $(this).data('id');
                var form = $('#deletePartial');
                var action = '{{ route('withdrawals.destroyPartial', '') }}/' + partiaId;
                form.attr('action', action);
                $('#confirmDeleteModal').modal('show');
            });
            $(document).ready(function() {
                $(document).on('click', '.cancel-button', function() {
                    $('#confirmDeleteModal').modal('hide');
                });
            });
        });

    </script>
@endsection

@push('scripts')
    <script src="{{ asset('/js/tab-index.js') }}"> </script>
@endpush

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('/css/tab-index.css') }}"/>
@endpush
