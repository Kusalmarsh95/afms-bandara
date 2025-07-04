@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 text-Left">
                        <h4><i class="nav-icon fas fa-edit text-green"></i> <strong>Administration</strong> | Users
                            Management</h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <h6> <strong>Administration</strong> > <i class="nav-icon fas fa-user-circle text-green"></i> Users
                            Management</h6>
                    </div>
                </div>
                <div class="row mb-2">
                    @can('administration-user-create')
                    <div class="col-sm-12 text-right">
                        <a class="btn bg-blue btn-sm" href="{{ route('users.create') }}"><i class="nav-icon fas fa-user-circle"></i> Create User</a>
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

            <table class="table table-bordered" id="users">
                <thead>
                <!-- This was missing in your original code -->
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Roles</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($users))
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if(!empty($user->getRoleNames()))
                                    @foreach($user->getRoleNames() as $v)
                                        <label class="badge badge-success">{{ $v }}</label>
                                    @endforeach
                                @endif
                            </td>
                            <td>
                                @can('administration-user-edit')
                                <a class="btn" href="{{ route('users.edit',$user->id) }}" title="Edit"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                                @endcan
                                @can('administration-user-delete')
                                <button class="btn delete-button" data-id="{{ $user->id }}" title="Delete">
                                    <i class="fas fa-trash-alt" style="color: red;"></i>
                                </button>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel"
         aria-hidden="true">
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
                    <form id="deleteUserForm" method="POST" action="">
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
            $('#users').DataTable({
                responsive: true
            });

            $(document).on('click', '.delete-button', function () {
                var userId = $(this).data('id');
                var form = $('#deleteUserForm');
                var action = '{{ route('users.destroy', '') }}/' + userId;
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
