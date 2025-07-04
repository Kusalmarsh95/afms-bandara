@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2><strong>Absent Settlement</strong></h2>
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

        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <button class="tab-link" onclick="openPage('All', this, '#3e7d2c')" id="defaultOpen">All</button>
            <button class="tab-link" onclick="openPage('Processing', this, '#3e7d2c')">Processing</button>
            <button class="tab-link" onclick="openPage('Rejected', this, '#3e7d2c')">Rejected</button>
            <button class="tab-link" onclick="openPage('Assign', this, '#3e7d2c')">Assign To Me</button>
            <div id="All" class="tab-content">
                <table class="table table-bordered" id="settlement">
                    <thead>
                    <tr>
                        <th>Application No</th>
                        <th>Regimental Number</th>
                        <th>Name</th>
                        <th>Total Capital</th>
                        <th>Recovered Capital</th>
{{--                        <th style="width: 80px">Action</th>--}}
                    </tr>
                    </thead>

                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($loans as $loan)
                        @if(!$loan->absentSettlement)
                            <tr>
                                {{--                        <td>{{ ++$i }}</td>--}}
                                <td>{{ $loan->application_reg_no ?? '-' }}</td>
                                <td>{{ $loan->membership->regimental_number ?? '-' }}</td>
                                <td>{{ $loan->membership->ranks->rank_name ?? '-'}} {{ $loan->membership->name ?? '-'}}</td>
                                <td>{{ number_format($loan->loan->total_capital,2) ?? '-' }}</td>
                                <td>{{ number_format($loan->loan->total_recovered_capital,2) ?? '-' }}</td>
{{--                                <td class="text-center">--}}
{{--                                    @can('loans-direct-settlement-approve')--}}
{{--                                        <a class="btn" href="{{ route('absent-settlement-view',$loan->id) }}"><i class="fas fa-user-check" style="color: lightseagreen;"></i></a>--}}
{{--                                    @endcan--}}
{{--                                </td>--}}
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                {!! $loans->render() !!}
            </div>
            <div id="Processing" class="tab-content">
                <table class="table table-bordered" id="newSettlement">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Application No</th>
                        <th>Name</th>
                        <th>Total Capital</th>
                        <th>Recovered Capital</th>
{{--                        <th style="width: 80px">Action</th>--}}
                    </tr>
                    </thead>
                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($loans as $loan)
                        @if($loan->absentSettlement && $loan->absentSettlement->processing==1)
                            <tr>
                                {{--                        <td>{{ ++$i }}</td>--}}
                                <td>{{ $loan->application_reg_no ?? '-' }}</td>
                                <td>{{ $loan->membership->regimental_number ?? '-' }}</td>
                                <td>{{ $loan->membership->ranks->rank_name ?? '-'}} {{ $loan->membership->name ?? '-'}}</td>
                                <td>{{ number_format($loan->loan->total_capital,2) ?? '-' }}</td>
                                <td>{{ number_format($loan->loan->total_recovered_capital,2) ?? '-' }}</td>
{{--                                <td class="text-center">--}}
{{--                                    @can('loans-direct-settlement-approve')--}}
{{--                                        <a class="btn" href="{{ route('absent-settlement-view',$loan->id) }}"><i class="fas fa-user-check" style="color: lightseagreen;"></i></a>--}}
{{--                                    @endcan--}}
{{--                                </td>--}}
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div id="Rejected" class="tab-content">
                <table class="table table-bordered" id="rejectSettlement">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Application No</th>
                        <th>Name</th>
                        <th>Total Capital</th>
                        <th>Recovered Capital</th>
{{--                        <th style="width: 50px">Action</th>--}}
                    </tr>
                    </thead>
                    @php
                        $i=0;
                    @endphp
                    <tbody>
                    @foreach ($loans as $loan)
                        @if($loan->absentSettlement && $loan->absentSettlement->processing==2)
                            <tr>
                                {{--                        <td>{{ ++$i }}</td>--}}
                                <td>{{ $loan->application_reg_no ?? '-' }}</td>
                                <td>{{ $loan->membership->regimental_number ?? '-' }}</td>
                                <td>{{ $loan->membership->ranks->rank_name ?? '-'}} {{ $loan->membership->name ?? '-'}}</td>
                                <td>{{ number_format($loan->loan->total_capital,2) ?? '-' }}</td>
                                <td>{{ number_format($loan->loan->total_recovered_capital,2) ?? '-' }}</td>
{{--                                <td class="text-center">--}}
{{--                                    @can('loans-direct-settlement-approve')--}}
{{--                                        <a class="btn" href="{{ route('absent-settlement-view',$loan->id) }}"><i class="fas fa-user-check" style="color: lightseagreen;"></i></a>--}}
{{--                                    @endcan--}}
{{--                                </td>--}}
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div id="Assign" class="tab-content">
                <table class="table table-bordered" id="assignSettlement">
                    <thead class="text-center">
                    <tr>
                        <th>No</th>
                        <th>Application No</th>
                        <th>Name</th>
                        <th>Total Capital</th>
                        <th>Recovered Capital</th>
{{--                        <th style="width: 50px">Action</th>--}}
                    </tr>
                    </thead>
                    @php
                        $i=0;
                    @endphp
                    <tbody>
{{--                    @foreach ($loans as $loan)--}}
{{--                        @if($loan->userName == Auth::user()->name)--}}
{{--                            <tr>--}}
{{--                                <td>{{ ++$i }}</td>--}}
{{--                                <td>{{ $loan->application_reg_no ?? '-' }}</td>--}}
{{--                                <td>{{ $loan->membership->ranks->rank_name ?? '-'}} {{ $loan->membership->name ?? '-'}}</td>--}}
{{--                                <td>{{ number_format($loan->loan->total_capital,2) ?? '-' }}</td>--}}
{{--                                <td>{{ number_format($loan->loan->total_recovered_capital,2) ?? '-' }}</td>--}}
{{--                                <td class="text-center">--}}
{{--                                    @can('loans-direct-settlement-approve')--}}
{{--                                        <a class="btn" href="{{ route('loan.editSettlement',$loan->id) }}"><i class="fas fa-user-check" style="color: lightseagreen;"></i></a>--}}
{{--                                    @endcan--}}
{{--                                    @can('loans-direct-settlement-approve')--}}
{{--                                        <button class="btn delete-button" data-id="{{ $loan->id }}">--}}
{{--                                            <i class="fas fa-trash-alt" style="color: red;"></i>--}}
{{--                                        </button>--}}
{{--                                    @endcan--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
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
                    <form id="deleteLoan" method="POST" action="">
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
            $('#settlement').DataTable({
                responsive: true,
                paging: false,
                searching: false,
                info: false,
                buttons: []
            });
            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);
            $('#newSettlement').DataTable({
                responsive: true,
                // paging: false,
                // info: false,
                buttons: [
                ]
            });
            $('#rejectSettlement').DataTable({
                responsive: true,
                // paging: false,
                // info: false,
                buttons: [
                ]
            });
            $('#assignSettlement').DataTable({
                responsive: true,
                // paging: false,
                // info: false,
                buttons: [
                ]
            });
            $(document).on('click', '.delete-button', function () {
                var loanId = $(this).data('id');
                var form = $('#deleteLoan');
                var action = '{{ route('settlement.destroy', '') }}/' + loanId;
                form.attr('action', action);
                $('#confirmDeleteModal').modal('show');
            });
            $(document).on('click', '.cancel-button', function() {
                $('#confirmDeleteModal').modal('hide');
            });
        });

    </script>

@endsection
@push('scripts')
    <script src="{{ asset('/js/tab-index.js') }}"> </script>
@endpush

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('/css/tab-index.css') }}"/>
@endpush
