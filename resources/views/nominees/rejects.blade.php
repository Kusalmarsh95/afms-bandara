@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2>Rejected Nominees</h2>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                setTimeout(function () {
                    $('.alert').fadeOut();
                }, 2000);
            });
        </script>
    @endif

    <div class="card m-1">
        <div class="card-body">
            <table class="table table-bordered" id="nominee">
                <thead>
                <tr class="text-center">
                    <th style="width: 20px">No</th>
                    <th>NIC</th>
                    <th>Name</th>
                    <th class="text-center" style="width: 160px">Action</th>
                </tr>
                </thead>
                @php
                    $i=0;
                @endphp
                <tbody>
                @foreach ($nomineeRejects as $nominee)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $nominee->nomineenic ? : '-'}}</td>
                        <td>{{ $nominee->name }}</td>
                        <td class="text-center">
                            @can('nominees-edit')
                            <a class="btn" href="{{ route('nominees.edit',$nominee->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                            @endcan
                            @can('nominees-approve')
                            <a class="btn" href="{{ route('nominee-approval',$nominee->id) }}"><i class="fas fa-user-check" style="color: yellowgreen;"></i></a>
                            @endcan
                            @can('nominees-delete')
                            <button class="btn delete-button" data-id="{{ $nominee->id }}">
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
                    <form id="deleteNomineeForm" method="POST" action="">
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
            $('#nominee').DataTable({
                responsive: true
            });
            $(document).on('click', '.delete-button', function () {
                var nomineeId = $(this).data('id');
                var form = $('#deleteNomineeForm');
                var action = '{{ route('nominees.destroy', '') }}/' + nomineeId;
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
