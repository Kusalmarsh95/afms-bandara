@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h3><strong>Registered Full Withdrawals</strong></h3>
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
                        <th>Registered Date</th>
                        <th>Processing Location</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($fullWithdrawals as $fullWithdrawal)
                        @if($fullWithdrawal->processing==1 | $fullWithdrawal->processing==3)
                            <tr>
                                <td>{{ $fullWithdrawal->membership->ranks->rank_name ?? '-' }} {{ $fullWithdrawal->membership->name ?? '-' }}</td>
                                <td>{{ $fullWithdrawal->application_reg_no ?? '-' }}</td>
                                <td>{{ $fullWithdrawal->received_date ? (new DateTime($fullWithdrawal->received_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $fullWithdrawal->userName }}</td>
                                <td class="text-center">
                                    <a class="btn" href="{{ route('withdrawals.showFull',$fullWithdrawal->id) }}"><i class="fas fa-eye" style="color: lightseagreen;"></i></a>
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
                        <th>Registered Date</th>
                        <th>Processing Location</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($fullWithdrawals as $fullWithdrawal)
                        @if($fullWithdrawal->processing==4 | $fullWithdrawal->processing==5)
                            <tr>
                                <td>{{ $fullWithdrawal->membership->ranks->rank_name ?? '-' }} {{ $fullWithdrawal->membership->name ?? '-' }}</td>
                                <td>{{ $fullWithdrawal->application_reg_no ?? '-' }}</td>
                                <td>{{ $fullWithdrawal->received_date ? (new DateTime($fullWithdrawal->received_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $fullWithdrawal->userName }}</td>
                                <td class="text-center">
                                    <a class="btn" href="{{ route('withdrawals.showFull',$fullWithdrawal->id) }}"><i class="fas fa-eye" style="color: lightseagreen;"></i></a>
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
                        <th>Rejected Date</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($fullWithdrawals as $fullWithdrawal)
                        @if($fullWithdrawal->processing==2)
                            <tr>
                                <td>{{ $fullWithdrawal->membership->ranks->rank_name ?? '-' }} {{ $fullWithdrawal->membership->name ?? '-' }}</td>
                                <td>{{ $fullWithdrawal->application_reg_no ?? '-' }}</td>
                                <td>{{ $fullWithdrawal->reject_date ? (new DateTime($fullWithdrawal->reject_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $fullWithdrawal->rejectReason->reason_name ?? '-' }}</td>
                                <td class="text-center">
                                    @can('withdrawals-full-edit')
                                        <a class="btn" href="{{ route('withdrawals.editFull',$fullWithdrawal->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                                    @endcan
                                    @can('withdrawals-full-show')
                                        <a class="btn" href="{{ route('full-view',$fullWithdrawal->id) }}"><i class="fas fa-user-check" style="color: yellowgreen;"></i></a>
                                    @endcan
                                    @can('withdrawals-full-delete')
                                    <button class="btn delete-button" data-id="{{ $fullWithdrawal->id }}">
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
                    @foreach ($fullWithdrawals as $fullWithdrawal)
                        @if($fullWithdrawal->userName == Auth::user()->name)
                            <tr>
                                <td>{{ $fullWithdrawal->membership->ranks->rank_name ?? '-' }} {{ $fullWithdrawal->membership->name ?? '-' }}</td>
                                <td>{{ $fullWithdrawal->application_reg_no ?? '-' }}</td>
                                <td>{{ $fullWithdrawal->received_date ? (new DateTime($fullWithdrawal->received_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $fullWithdrawal->reason ?? '-' }}</td>
                                <td class="text-center">
                                    @can('withdrawals-full-edit')
                                        <a class="btn" href="{{ route('withdrawals.editFull',$fullWithdrawal->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                                    @endcan
                                    @can('withdrawals-full-show')
                                        <a class="btn" href="{{ route('full-view',$fullWithdrawal->id) }}"><i class="fas fa-user-check" style="color: yellowgreen;"></i></a>
                                    @endcan
                                    @can('withdrawals-full-approved-show')
                                        <a class="btn" href="{{ route('full-approved',$fullWithdrawal->id) }}"><i class="fas fa-clipboard-check" style="color: yellowgreen;"></i></a>
                                    @endcan
                                    @can('withdrawals-full-delete')
                                        <button class="btn delete-button" data-id="{{ $fullWithdrawal->id }}">
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
                    Are you sure you want to delete this application?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn cancel-button btn-secondary">Cancel</button>
                    <form id="deleteFull" method="POST" action="">
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
            }, 2000);

            $(document).on('click', '.delete-button', function () {
                var fullWithdrawalId = $(this).data('id');
                var form = $('#deleteFull');
                var action = '{{ route('withdrawals.destroyFull', '') }}/' + fullWithdrawalId;
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
