@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6 text-Left">
                        <h4><i class="nav-icon fas fa-address-card text-blue"></i> <strong>Master Data</strong> | Districts
                            Management</h4>
                    </div>
                    <div class="col-sm-6 text-right">
                        <h6> <strong>Master Data</strong> > <i class="nav-icon fas fa-map-location-dot text-blue"></i>
                            Districts
                            Management</h6>
                    </div>
                </div>
                <div class="row mb-2">
                    @can('master-data-district-create')
                        <div class="col-sm-12 text-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('districts.create') }}"><i
                                    class="nav-icon fas fa-map-location-dot"></i> Create Districts</a>
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
            <table class="table table-bordered " id="districtTable">
                <thead>
                <tr class="text-center">
                    <th style="width: 20px">No</th>
                    <th>Districts</th>
                    <th class="text-center" style="width: 120px">Action</th>
                </tr>
                </thead>
                @php
                    $i=0;
                @endphp
                <tbody>
                @foreach ($districts as $district)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $district->district_name ?? '-' }}</td>
                        <td class="text-center">
                            @can('master-data-district-edit')
                                <a class="btn" href="{{ route('districts.edit', $district->id) }}">
                                    <i class="fas fa-pen" style="color: lightseagreen;" title="Edit"></i>
                                </a>
                            @endcan
                            @can('master-data-district-delete')
                                <button class="btn delete-button" data-id="{{ $district->id }}">
                                    <i class="fas fa-trash-alt" style="color: red;" title="Delete"></i>
                                </button>
                            @endcan
                        </td>
                    </tr>
                @endforeach
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
                    <form id="deleteDistrictForm" method="POST" action="">
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
            $('#districtTable').DataTable({
                responsive: true,
                buttons: []
            });

            // $('.delete-button').click(function() {
            $(document).on('click', '.delete-button', function (e) {
                var districtId = $(this).data('id');
                var form = $('#deleteDistrictForm');
                var action = '{{ route('districts.destroy', '') }}/' + districtId;
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
