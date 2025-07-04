@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 text-Left">
                        <h4><i class="nav-icon fas fa-address-card text-blue"></i> <strong>Master Data</strong> | Units Management</h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <h6> <strong>Master Data</strong> > <i class="nav-icon fas fa-city text-blue"></i> Units
                            Management</h6>
                    </div>

                </div>
                <div class="row mb-2">
                    @can('master-data-unit-create')
                        <div class="col-sm-12 text-right">
                            <a class="btn btn-sm bg-blue" href="{{ route('units.create') }}"> <i class="nav-icon fas fa-city"></i> Create Unit</a>
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
            <table class="table table-bordered" id="units">
                <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Regiment Name</th>
                    <th>Unit Name</th>
                    <th style="width: 120px">Action</th>
                </tr>
                </thead>
                @php
                    $i=0;
                @endphp
                <tbody>
                @foreach ($units as $unit)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $unit->regiment->regiment_name ?? '-' }}</td>
                        <td>{{ $unit->unit_name ?? '-' }}</td>
                        <td class="text-center">
                            @can('master-data-unit-edit')
                                <a class="btn" href="{{ route('units.edit',$unit->id) }}" title="Edit"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                            @endcan
                            @can('master-data-unit-delete')
                                <button class="btn delete-button" data-id="{{ $unit->id }}" title="Delete">
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
                    <form id="deleteUnitForm" method="POST" action="">
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
            $('#units').DataTable({
                responsive: true,
                // paging: false,
                // info: false,
                buttons: [
                ]
            });

            $(document).on('click', '.delete-button', function () {
                var unitId = $(this).data('id');
                var form = $('#deleteUnitForm');
                var action = '{{ route('units.destroy', '') }}/' + unitId;
                form.attr('action', action);
                $('#confirmDeleteModal').modal('show');
            });
            $(document).on('click', '.cancel-button', function() {
                $('#confirmDeleteModal').modal('hide');
            });

            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);
        });
    </script>
@endsection
