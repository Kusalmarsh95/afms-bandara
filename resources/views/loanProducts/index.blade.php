@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2><strong>Loan Product Management</strong></h2>
                    </div>
                    @can('master-data-loan-product-create')
                    <div class="pull-right">
                        <a class="btn btn-success" href="{{ route('loan-products.create') }}">New Product</a>
                    </div>
                    @endcan
                </div>
            </div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="container-fluid">
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        </div>
    @endif

    <div class="card m-1">
        <div class="card-body">
            <table class="table table-bordered" id="loanProducts">
                <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Name</th>
                    <th>Percentage</th>
                    <th>Interest Rate</th>
                    <th>Deactivated Date </th>
                    <th>Status</th>
                    <th class="text-center" width="120px">Action</th>
                </tr>
                </thead>
                @php
                    $i=0;
                @endphp
                <tbody>
                @foreach ($loanProducts as $loanProduct)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $loanProduct->name ? : '-' }}</td>
                        <td>{{ $loanProduct->percentage ? : '-' }} %</td>
                        <td>{{ number_format($loanProduct->interest_rate,2) ? : '-' }} %</td>
                        <td>{{ $loanProduct->end_date ? : '-' }} </td>
                        <td class="text-center">
                            @if ($loanProduct->status == 1)
                                <span class="badge badge-success"><i class="fas fa-check"></i> Active</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times"></i> Inactive</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @can('master-data-loan-product-edit')
                            <a class="btn" href="{{ route('loan-products.edit',$loanProduct->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                            @endcan
                            @can('master-data-loan-product-delete')
                            <button class="btn delete-button" data-id="{{ $loanProduct->id }}">
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
                    <form id="deleteloanProduct" method="POST" action="">
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
            $('#loanProducts').DataTable({
                responsive: true
            });

            $(document).on('click', '.delete-button', function () {
                var loanProductId = $(this).data('id');
                var form = $('#deleteloanProduct');
                var action = '{{ route('loan-products.destroy', '') }}/' + loanProductId;
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

