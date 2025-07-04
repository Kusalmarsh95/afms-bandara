@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 text-Left">
                        <h4><i class="nav-icon fas fa-edit text-green"></i> <strong>Administration</strong> | Role Management</h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <h6> <strong>Administration</strong> > <i class="nav-icon fas fa-universal-access text-green"></i> Role Management</h6>
                    </div>
                </div>
                <div class="row mb-2">
                    @can('administration-role-create')
                    <div class="col-sm-12 text-right">
                        <a class="btn btn-success btn-sm" href="{{ route('roles.create') }}"><i class="nav-icon fas fa-universal-access"></i> Create New Role</a>
                    </div>
                    @endcan
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
            <table class="table table-bordered" id="rolesTable">
                <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Name</th>
                    <th style="width: 260px">Action</th>
                </tr>
                </thead>
                @php
                    $i=0;
                @endphp
                <tbody>
                @foreach ($roles as $role)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $role->name }}</td>
                        <td class="text-center">
                            {{--                            <a class="btn" href="{{ route('roles.show',$role->id) }}"><i class="fas fa-eye" style="color: green;"></i></a>--}}
                            @can('administration-role-edit')
                            <a class="btn" href="{{ route('roles.edit',$role->id) }}" title="Edit"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                            @endcan
                            @can('administration-role-delete')
                            <button class="btn delete-button" data-id="{{ $role->id }}" title="Delete">
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
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this member?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn cancel-button btn-secondary">Cancel</button>
                    <form id="deleteRoleForm" method="POST" action="">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#rolesTable').DataTable({
                responsive: true,
                buttons:[],
            });

            $(document).on('click', '.delete-button', function () {
                var roleId = $(this).data('id');
                var form = $('#deleteRoleForm');
                var action = '{{ route('roles.destroy', '') }}/' + roleId;
                form.attr('action', action);
                $('#confirmDeleteModal').modal('show');
            });
            $(document).on('click', '.cancel-button', function() {
                $('#confirmDeleteModal').modal('hide');
            });

            setTimeout(function () {
                $('.alert').fadeOut();
            }, 2000);
        });
    </script>
@endsection
