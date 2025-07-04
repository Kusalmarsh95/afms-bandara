@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2>Member Changes</h2>
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
            <table class="table table-bordered" id="membershipChange">
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
                @foreach ($membershipChanges as $membership)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $membership->regimental_number ?? '' }}</td>
                        <td>{{ $membership->ranks->rank_name ?? '' }}</td>
                        <td>{{ $membership->name ?? '' }}</td>
                        <td>{{ $membership->enumber ?? '' }}</td>
                        <td>{{ $membership->status->status_name ?? '-'}}</td>
                        <td class="text-center">
                            @can('memberships-edit')
                            <a class="btn" href="{{ route('memberships.edit',$membership->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                            @endcan
                            @can('memberships-changes-approve')
                            <a class="btn" href="{{ route('membership-approval',$membership->id) }}"><i class="fas fa-user-check" style="color: yellowgreen;"></i></a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#membershipChange').DataTable({
                responsive: true
            });
        });

    </script>


@endsection
