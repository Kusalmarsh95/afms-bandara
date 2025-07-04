@extends('layouts.app')


@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2>Rejected Members</h2>
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

    <div class="card m-1">
        <div class="card-body">
            <table class="table table-bordered" id="membershipAssign">
                <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Regimental Number</th>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>E Number</th>
                    <th>Membership Status</th>
                    <th class="text-center" style="width: 120px">Action</th>
                </tr>
                </thead>
                @php
                    $i=0;
                @endphp
                <tbody>
                @foreach ($memberships as $membership)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $membership->regimental_number }}</td>
                        <td>{{ $membership->ranks->rank_name ?? '-'}}</td>
                        <td>{{ $membership->name }}</td>
                        <td>{{ $membership->enumber }}</td>
                        <td>{{ $membership->status->status_name ?? '-'}}</td>
                        <td class="text-center">
                            @can('memberships-edit')
                            <a class="btn" href="{{ route('memberships.edit',$membership->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                            @endcan
                            @can('memberships-rejected-approve')
                            <a class="btn" href="{{ route('membership-approval',$membership->id) }}"><i class="fas fa-user-check" style="color: yellowgreen;"></i></a>
                            @endcan
                            @can('memberships-delete')
                            <button class="btn delete-button" data-id="{{ $membership->id }}">
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
                    <form id="deleteMemberForm" method="POST" action="">
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
            $('#membershipAssign').DataTable({
                responsive: true
            });

            $(document).on('click', '.delete-button', function (e) {
                var memberId = $(this).data('id');
                var form = $('#deleteMemberForm');
                var action = '{{ route('memberships.destroy', '') }}/' + memberId;
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
