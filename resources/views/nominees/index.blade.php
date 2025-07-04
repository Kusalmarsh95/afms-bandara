@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-12 text-center">
                        <h2>New Nominees</h2>
                    </div>
                </div>
            </div>
        </section>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                setTimeout(function () {
                    $('.alert').fadeOut();
                }, 2000);
            });
        </script>
    @endif

    <div class="card m-1">
        <div class="card-body">
            <table class="table table-bordered" id="nominee">
                <thead>
                <tr class="text-center">
                    <th style="width: 20px">No</th>
                    <th>NIC</th>
                    <th>Name</th>
                    <th class="text-center" style="width: 160px">Action</th>
                </tr>
                </thead>
                @php
                    $i=0;
                @endphp
                <tbody>
                @foreach ($nominees as $nominee)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $nominee->nomineenic ? : '-'}}</td>
                        <td>{{ $nominee->name }}</td>
                        <td class="text-center">
                            @can('nominees-edit')
                            <a class="btn" href="{{ route('nominees.edit',$nominee->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                            @endcan
                            @can('nominees-approve')
                            <a class="btn" href="{{ route('nominee-approval',$nominee->id) }}"><i class="fas fa-user-check" style="color: yellowgreen;"></i></a>
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
            $('#nominee').DataTable({
                responsive: true
            });
        });
    </script>

@endsection
