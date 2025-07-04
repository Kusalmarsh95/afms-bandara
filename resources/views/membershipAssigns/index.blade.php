@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2>New Members</h2>
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
            <table class="table table-bordered" id="membershipAssign">
                <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Regimental Number</th>
                    <th>Rank</th>
                    <th>Name</th>
                    <th>E Number</th>
                    <th>Membership Status</th>
                    <th class="text-center" width="100px">Action</th>
                </tr>
                </thead>
                @php
                    $i=0;
                @endphp
                <tbody>
                @foreach ($memberships as $membership)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $membership->regimental_number }}</td>
                        <td>{{ $membership->ranks->rank_name ?? '-' }}</td>
                        <td>{{ $membership->name }}</td>
                        <td>{{ $membership->enumber }}</td>
                        <td>{{ $membership->status->status_name ?? '-'}}</td>
                        <td class="text-center">
                            @if(!isset($membership->ranks->rank_name) && !isset($membership->status->status_name))
                                <i class="fas fa-exclamation-circle" title="Details Incomplete"></i>
                            @endif
                            @can('memberships-edit')
                            <a class="btn" href="{{ route('memberships.edit',$membership->id) }}"><i class="fas fa-pen" style="color: yellowgreen;"></i></a>
                            @endcan
                            @can('memberships-approve')
                            <a class="btn" href="{{ route('membership-approval',$membership->id) }}"><i class="fas fa-user-check" style="color: lightseagreen;"></i></a>
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
            $('#membershipAssign').DataTable({
                responsive: true
            });
        });

    </script>
@endsection
