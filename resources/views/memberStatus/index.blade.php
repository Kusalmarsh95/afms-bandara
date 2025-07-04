@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2><strong>Member Status Management</strong></h2>
                    </div>
                    @can('master-data-member-status-create')
                    <div class="pull-right">
                        <a class="btn btn-success" href="{{ route('member-status.create') }}"> Create New</a>
                    </div>
                    @endcan
                </div>
            </div>
        </section>
    </div>

    @if ($message = Session::get('fail'))
        <div class="alert alert-danger">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="card m-1">
        <div class="card-body">
            <table class="table table-bordered " id="memberStatusTable">
                <thead>
                <tr class="text-center">
                    <th style="width: 20px">No</th>
                    <th>Member Status</th>
                    <th class="text-center" style="width: 120px">Action</th>
                </tr>
                </thead>
                @php
                    $i=0;
                @endphp
                <tbody>
                @foreach ($memberStatus as $status)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $status->status_name ?? '-' }}</td>
                        <td class="text-center">
                            @can('master-data-member-status-edit')
                            <a class="btn" href="{{ route('member-status.edit', $status->id) }}">
                                <i class="fas fa-pen" style="color: lightseagreen;"></i>
                            </a>
                            @endcan
                            @can('master-data-member-status-delete')
                            <button class="btn delete-button" data-id="{{ $status->id }}">
                                <i class="fas fa-trash-alt" style="color: red;"></i>
                            </button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this member?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteMemberStatusForm" method="POST" action="">
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
            $('#memberStatusTable').DataTable({
                responsive: true,
                buttons: []
            });

            // $('.delete-button').click(function() {
            $(document).on('click', '.delete-button', function (e) {
                var memberStatusId = $(this).data('id');
                var form = $('#deleteMemberStatusForm');
                var action = '{{ route('member-status.destroy', '') }}/' + memberStatusId;
                form.attr('action', action);
                $('#confirmDeleteModal').modal('show');
            });

            $(document).ready(function () {
                setTimeout(function () {
                    $('.alert').fadeOut();
                }, 2000);
            });
        });
    </script>
@endsection

