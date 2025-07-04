@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2><strong>Loan Applications</strong></h2>
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
                <table class="table table-bordered" id="new">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th style="width: 110px">Application No</th>
                        <th style="width:220px">Name</th>
                        <th>Registered Date</th>
                        <th>Processing Location</th>
                        <th style="width: 150px">Action</th>
                    </tr>
                    </thead>
                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($loans as $loan)
                        @if($loan->processing == 1 | $loan->processing == 3)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $loan->application_reg_no ?? '-' }}</td>
                                <td>{{ $loan->membership->ranks->rank_name ?? '-'}} {{ $loan->membership->name ?? '-'}}</td>
                                <td>{{ $loan->registered_date ? (new DateTime($loan->registered_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $loan->userName }}</td>
                                <td class="text-center">
                                    @can('memberships-registered-loan-show')
                                        <a class="btn" href="{{ route('loan.show',$loan->id) }}"><i class="fas fa-eye" style="color: #85C1E9;"></i></a>
                                    @endcan
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div id="Approved" class="tab-content">
                <table class="table table-bordered" id="approved">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th style="width:100px">Application No</th>
                        <th style="width:200px">Name</th>
                        <th>Registered Date</th>
                        <th>Processing Location</th>
                        <th style="width:150px" >Action</th>
                    </tr>
                    </thead>
                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($loans as $loan)
                        @if($loan->processing == 4 | $loan->processing == 5)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $loan->application_reg_no ?? '-' }}</td>
                                <td>{{ $loan->membership->ranks->rank_name ?? '-'}} {{ $loan->membership->name ?? '-'}}</td>
                                <td>{{ $loan->registered_date ? (new DateTime($loan->registered_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $loan->userName }}</td>
                                <td class="text-center">
                                    @can('memberships-registered-loan-show')
                                        <a class="btn" href="{{ route('loan.show',$loan->id) }}"><i class="fas fa-eye" style="color: #85C1E9;"></i></a>
                                    @endcan
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div id="Rejected" class="tab-content">
                <table class="table table-bordered" id="reject">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th style="width:100px">Application No</th>
                        <th style="width:220px">Name</th>
                        <th>Registered Date</th>
                        <th>Reject Reason</th>
                        <th style="width: 120px">Action</th>
                    </tr>
                    </thead>
                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($loans as $loan)
                        @if($loan->processing == 2)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $loan->application_reg_no ?? '-' }}</td>
                                <td>{{ $loan->membership->ranks->rank_name ?? '-'}} {{ $loan->membership->name ?? '-'}}</td>
                                <td>{{ $loan->registered_date ? (new DateTime($loan->registered_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $loan->rejectReason->reason_name ?? '-' }}</td>
                                <td class="text-center">
                                    @can('memberships-registered-loan-show')
                                        <a class="btn" href="{{ route('loan.show',$loan->id) }}"><i class="fas fa-eye" style="color: #85C1E9;"></i></a>
                                    @endcan
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div id="Assign" class="tab-content">
                <table class="table table-bordered" id="assign">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th style="width: 110px">Application No</th>
                        <th style="width:220px">Name</th>
                        <th>Registered Date</th>
                        <th style="width: 200px">Remark</th>
                        <th style="width: 150px">Action</th>
                    </tr>
                    </thead>
                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($loans as $loan)
                        @if($loan->userName == Auth::user()->name)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $loan->application_reg_no ?? '-' }}</td>
                                <td>{{ $loan->membership->ranks->rank_name ?? '-'}} {{ $loan->membership->name ?? '-'}}</td>
                                <td>{{ $loan->registered_date ? (new DateTime($loan->registered_date))->format('Y-m-d') : '-' }}</td>
                                <td>{{ $loan->reason ?? '-' }}</td>
                                <td class="text-center">
                                    @can('loans-applications-edit')
                                        <a class="btn" href="{{ route('loan.edit',$loan->id) }}"><i class="fas fa-pen" style="color: darkkhaki;"></i></a>
                                    @endcan
                                    @can('loans-applications-show')
                                        <a class="btn" href="{{ route('loan.view',$loan->id) }}"><i class="fas fa-user-check" style="color: lightseagreen;"></i></a>
                                    @endcan
                                    @can('loans-applications-approved-show')
                                        <a class="btn" href="{{ route('loan.approved',$loan->id) }}"><i class="fas fa-clipboard-check" style="color: lightseagreen;"></i></a>
                                    @endcan
                                    @can('loans-applications-delete')
                                        <button class="btn delete-button" data-id="{{ $loan->id }}">
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
                    <form id="deleteLoan" method="POST" action="">
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
            $('#new').DataTable({
                responsive: true,
                // paging: false,
                // info: false,
                buttons: [
                ]
            });
            $('#approved').DataTable({
                responsive: true,
                // paging: false,
                // info: false,
                buttons: [
                ]
            });
            $('#reject').DataTable({
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
            $(document).on('click', '.delete-button', function () {
                var loanId = $(this).data('id');
                var form = $('#deleteLoan');
                var action = '{{ route('loan.destroy', '') }}/' + loanId;
                form.attr('action', action);
                $('#confirmDeleteModal').modal('show');
            });
            $(document).ready(function() {
                $(document).on('click', '.cancel-button', function() {
                    $('#confirmDeleteModal').modal('hide');
                });
            });
            setTimeout(function () {
                $('.alert').fadeOut();
            }, 2000);

        });

    </script>
@endsection

@push('scripts')
    <script src="{{ asset('/js/tab-index.js') }}"> </script>
@endpush

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('/css/tab-index.css') }}"/>
@endpush
