@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2><strong>Contribution Interest Management</strong></h2>
                    </div>
                    @can('master-data-contribution-interest-create')
                    <div class="pull-right">
                        <a class="btn btn-success" href="{{ route('contribution-interests.create') }}"> Create New</a>
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
            <table class="table table-bordered " id="contributionInterest">
                <thead>
                <tr class="text-center">
                    <th style="width: 20px">No</th>
                    <th>Year</th>
                    <th>Quarter</th>
                    <th>Rate</th>
                    <th>status</th>
                    <th class="text-center" style="width: 120px">Action</th>
                </tr>
                </thead>
                @php
                    $i=0;
                @endphp
                <tbody>
                @foreach ($contributionInterests as $contributionInterest)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $contributionInterest->year ?? '-' }}</td>
                        <td>@if ($contributionInterest->icp_id == 0)
                                Yearly
                            @elseif ($contributionInterest->icp_id == 1)
                                1st Half
                            @elseif ($contributionInterest->icp_id == 2)
                                2nd Half
                            @elseif ($contributionInterest->icp_id == 10)
                                1st Quarter
                             @elseif ($contributionInterest->icp_id == 20)
                                2nd Quarter
                            @elseif ($contributionInterest->icp_id == 30)
                                3rd Quarter
                            @elseif ($contributionInterest->icp_id == 40)
                                4th Quarter
                            @else
                                Other
                            @endif</td>
                        <td>{{ number_format($contributionInterest->interest_rate,6) ?? '-' }}</td>
                        <td class="text-center">
                            @if ($contributionInterest->status == 1)
                                <span class="badge badge-success"><i class="fas fa-check"></i> Active</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-times"></i> Inactive</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @can('master-data-contribution-interest-edit')
                            <a class="btn" href="{{ route('contribution-interests.edit', $contributionInterest->id) }}">
                                <i class="fas fa-pen" style="color: lightseagreen;"></i>
                            </a>
                            @endcan
                            @can('master-data-contribution-interest-delete')
                            <button class="btn delete-button" data-id="{{ $contributionInterest->id }}">
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
                    <form id="deleteContributionInterest" method="POST" action="">
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
            $('#contributionInterest').DataTable({
                responsive: true,
                buttons: []
            });

            $(document).on('click', '.delete-button', function () {
                var rankId = $(this).data('id');
                var form = $('#deleteContributionInterest');
                var action = '{{ route('contribution-interests.destroy', '') }}/' + rankId;
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

