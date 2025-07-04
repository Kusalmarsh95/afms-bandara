@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2><strong>New Suwasahana Loans</strong></h2>
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
                <table class="table table-bordered" id="suwasahana">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>ABF Voucher</th>
                        <th>Loan Type</th>
                        <th>Amount</th>
                        <th style="width: 120px">Action</th>
                    </tr>
                    </thead>
                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($suwasahana as $suwa)
                        @if($suwa->accepted==0)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $suwa->membership->ranks->rank_name ?? '-'}} {{ $suwa->membership->name ?? '-'}}</td>
                                <td>{{ $suwa->ABFvoucherno ?? '-' }}</td>
                                <td>{{ $suwa->LoanType ?? '-' }}</td>
                                <td>{{ $suwa->total_capital ?? '-' }}</td>
                                <td class="text-center">
                                    <a class="btn" href="{{ route('suwasahana.edit',$suwa->id) }}"><i class="fas fa-pen" style="color: dimgray;"></i></a>
                                    @can('loans-applications-show')
                                        <a class="btn" href="{{ route('suwasahana.show',$suwa->id) }}"><i class="fas fa-user-check" style="color: lightseagreen;"></i></a>
                                    @endcan
                                    <button class="btn delete-button" data-id="{{ $suwa->id }}">
                                        <i class="fas fa-trash-alt" style="color: red;"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div id="Rejected" class="tab-content">
                <table class="table table-bordered" id="rejectsuwasahana">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>ABF Voucher</th>
                        <th>Loan Type</th>
                        <th>Amount</th>
                        <th style="width: 120px">Action</th>
                    </tr>
                    </thead>
                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($suwasahana as $suwa)
                        @if($suwa->accepted==2)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $suwa->membership->ranks->rank_name ?? '-'}} {{ $suwa->membership->name ?? '-'}}</td>
                                <td>{{ $suwa->ABFvoucherno ?? '-' }}</td>
                                <td>{{ $suwa->LoanType ?? '-' }}</td>
                                <td>{{ $suwa->total_capital ?? '-' }}</td>
                                <td class="text-center">
                                    <a class="btn" href="{{ route('suwasahana.edit',$suwa->id) }}"><i class="fas fa-pen" style="color: dimgray;"></i></a>
                                    @can('loans-applications-show')
                                        <a class="btn" href="{{ route('suwasahana.show',$suwa->id) }}"><i class="fas fa-user-check" style="color: lightseagreen;"></i></a>
                                    @endcan
                                    <button class="btn delete-button" data-id="{{ $suwa->id }}">
                                        <i class="fas fa-trash-alt" style=" color: red;"></i>
                                    </button>
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
                    Are you sure you want to remove suwasahana details?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn cancel-button btn-secondary">Cancel</button>
                    <form id="deleteSuwasahanaForm" method="POST" action="">
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

            $(document).on('click', '.delete-button', function () {
                var suwasahanaId = $(this).data('id');
                var form = $('#deleteSuwasahanaForm');
                var action = '{{ route('suwasahana.destroy', '') }}/' + suwasahanaId;
                form.attr('action', action);
                $('#confirmDeleteModal').modal('show');
            });
            $(document).on('click', '.cancel-button', function() {
                $('#confirmDeleteModal').modal('hide');
            });
            $('#suwasahana').DataTable({
                responsive: true,
                // paging: false,
                // info: false,
                buttons: [
                ]
            });
            $('#rejectsuwasahana').DataTable({
                responsive: true,
                // paging: false,
                // info: false,
                buttons: [
                ]
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
