@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2><strong>Withdrawal Product Management</strong></h2>
                    </div>
                    @can('master-data-withdrawal-product-create')
                    <div class="pull-right">
                        <a class="btn btn-success" href="{{ route('withdrawal-products.create') }}">New Product</a>
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
            <table class="table table-bordered" id="withdrawalProducts">
                <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Name</th>
                    <th>Percentage</th>
                    <th>Status</th>
                    <th class="text-center" width="120px">Action</th>
                </tr>
                </thead>
                @php
                    $i=0;
                @endphp
                <tbody>
                @foreach ($withdrawalProducts as $withdrawalProduct)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $withdrawalProduct->name ? : '-' }}</td>
                        <td>{{ $withdrawalProduct->percentage ? : '-' }}</td>
                        <td class="text-center">
                            @if ($withdrawalProduct->status == 1)
                                <span class="badge badge-success"><i class="fas fa-check"></i> Active</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times"></i> Inactive</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @can('master-data-withdrawal-product-edit')
                            <a class="btn" href="{{ route('withdrawal-products.edit',$withdrawalProduct->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                            @endcan
                            @can('master-data-withdrawal-product-delete')
                            <button class="btn delete-button" data-id="{{ $withdrawalProduct->id }}">
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
                    <form id="deleteWithdrawalProduct" method="POST" action="">
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
            $('#withdrawalProducts').DataTable({
                responsive: true
            });

            $(document).on('click', '.delete-button', function () {
                var withdrawalProductId = $(this).data('id');
                var form = $('#deleteWithdrawalProduct');
                var action = '{{ route('withdrawal-products.destroy', '') }}/' + withdrawalProductId;
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

