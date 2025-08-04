@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2><strong>Contribution Correction List</strong></h2>
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
            <button class="tab-link" onclick="openPage('Rejected', this, '#3e7d2c')">Rejected</button>
            <div id="Processing" class="tab-content">
                <table id="pending" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Remark</th>
                        <th>Amount</th>
                        <th>Interest</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($corrections as $correction)
                        @if($correction->accepted==0)
                            <tr>
                                <td>{{ $correction->membership->name ?? '-' }}</td>
                                <td>{{ $correction->type ?? '-' }}</td>
                                <td>{{ $correction->remark ?? '-' }}</td>
                                <td>{{ number_format($correction->amount,2) ?? '-' }}</td>
                                <td>{{ number_format($correction->interest,2) ?? '-' }}</td>
                                <td class="text-center">
                                    @can('bulk-additional-contribution-edit')
                                        <a class="btn" href="{{ route('corrections.edit',$correction->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                                    @endcan
                                    @can('bulk-additional-contribution-approve')
                                        <a class="btn" href="{{ route('correction-approval',$correction->id) }}"><i class="fas fa-user-check" style="color: yellowgreen;"></i></a>
                                    @endcan
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div id="Rejected" class="tab-content">
                <table id="rejcted" class="table table-striped table-bordered">
                    <thead class="text-center">
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Remark</th>
                        <th>Amount</th>
                        <th>Interest</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($corrections as $correction)
                        @if($correction->accepted==2)
                            <tr>
                                <td>{{ $correction->membership->name ?? '-' }}</td>
                                <td>{{ $correction->type ?? '-' }}</td>
                                <td>{{ $correction->remark ?? '-' }}</td>
                                <td>{{ number_format($correction->amount,2) ?? '-' }}</td>
                                <td>{{ number_format($correction->interest,2) ?? '-' }}</td>
                                <td class="text-center">
                                    @can('bulk-additional-contribution-edit')
                                        <a class="btn" href="{{ route('corrections.edit',$correction->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                                    @endcan
                                    @can('bulk-additional-contribution-approve')
                                        <a class="btn" href="{{ route('correction-approval',$correction->id) }}"><i class="fas fa-user-check" style="color: yellowgreen;"></i></a>
                                    @endcan
                                    @can('bulk-additional-contribution-delete')
                                        <button class="btn delete-button" data-id="{{ $correction->id }}">
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
                    <form id="deleteContributionForm" method="POST" action="">
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
            $('#pending').DataTable({
                responsive: true,
                // searching:false,
                buttons: [],
                // paging: false,
                // info: false,
            });
            $('#rejcted').DataTable({
                responsive: true,
                // paging: false,
                // info: false,
                buttons: [
                ]
            });

            $(document).on('click', '.delete-button', function () {
                var contributionId = $(this).data('id');
                var form = $('#deleteContributionForm');
                var action = '{{ route('correction-destroy', '') }}/' + contributionId;
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
            }, 4000);
        });
    </script>
@endsection

@push('scripts')
    <script src="{{ asset('/js/tab-index.js') }}"> </script>
@endpush

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('/css/tab-index.css') }}"/>
@endpush
