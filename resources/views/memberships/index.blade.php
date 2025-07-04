@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2>ABF Membership</h2>
                    </div>
                    <div class="pull-right">
                        <a class="btn btn-success" href="{{ route('memberships.create') }}"> Create Member</a>
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
            <form method="GET" action="{{ route('memberships.index') }}">
                <div class="form-group row">
                    <div class="col-6 row"></div>
                    <div class="col-6 row">
                        <input type="text" name="search_value" class="form-control col-sm-9" placeholder="Regimental Number / E Number" value="{{ request('search_value') }}">
                        <button type="submit" class="btn btn-outline-info ml-2"><i class="fas fa-search"></i></button>
                        <a href="{{ route('memberships.index') }}" class="btn btn-outline-secondary ml-2"><i class="fas fa-circle-notch"></i></a>
                    </div>
                </div>

            </form>
            <table class="table table-bordered" id="memberships">
                <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-center">Regimental Number</th>
                    <th class="text-center">Rank</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">E Number</th>
                    <th class="text-center">Membership Status</th>
                    <th class="text-center">P & R Status</th>
                    <th class="text-center" width="160px">Action</th>
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
                        <td>{{ $membership->ranks->rank_name ?? "-"}}</td>
                        <td>{{ $membership->name }}</td>
                        <td>{{ $membership->enumber }}</td>
                        <td>{{ $membership->status->status_name ?? '-'}}</td>
                        <td>{{ $membership->pnr_status ?? '-'}}</td>
                        <td class="text-center">
                            @can('memberships-registered-show')
                            <a class="btn" href="{{ route('memberships.show',$membership->id) }}"><i class="fas fa-eye <i class=" style="color: #78b20a;"></i></a>
                            @endcan
                            @can('memberships-edit')
                            <a class="btn" href="{{ route('memberships.edit',$membership->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $memberships->render() !!}
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#memberships').DataTable({
                responsive: true,
                paging: false,
                searching: false,
                info: false,
                buttons: []
            });
        });

        setTimeout(function () {
            $('.alert').fadeOut();
        }, 4000);
    </script>

@endsection
