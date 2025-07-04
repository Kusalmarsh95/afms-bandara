@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Edit absent details</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('memberships.show', $absent->membership->id) }}" class="btn btn-sm btn-dark">Back</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fas fa-arrow-right"></i> {{$absent->membership->ranks->rank_name ?? '-'}} {{$absent->membership->name}}</div>
            </div>
            <div class="card-header">
                <div class="container-fluid">
                    <form action="{{ route('absents.update', $absent->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="from" class="col-sm-4 col-form-label">Absent From</label>
                                    <div class="col-sm-8">
                                        <input type="date" name="from" class="form-control" id="fromDate" value="{{ $absent->from }}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="to" class="col-sm-4 col-form-label">Absent To</label>
                                    <div class="col-sm-8">
                                        <input type="date" name="to" class="form-control" id="toDate" value="{{ $absent->to }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="days" class="col-sm-4 col-form-label">Days</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="days" class="form-control" id="days" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header"></div>
                        <div class="card-body">
                            <div class="form-group">
                                {{--                                <div class="col-6 row">--}}
                                {{--                                    <label for="fwd_to" class="col-sm-4 col-form-label">For Approval</label>--}}
                                {{--                                    <div class="col-sm-8">--}}
                                {{--                                        @if(isset($users))--}}
                                {{--                                            <select name="fwd_to" class="form-control col-sm-9" data-live-search="true">--}}
                                {{--                                                <option selected>Assign a Officer</option>--}}
                                {{--                                                @foreach($users as $user)--}}
                                {{--                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>--}}
                                {{--                                                @endforeach--}}
                                {{--                                            </select>--}}
                                {{--                                        @endif--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                                <div class="text-center">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('fromDate').addEventListener('change', updateDays);
            document.getElementById('toDate').addEventListener('change', updateDays);
        });

        function updateDays() {
            const fromDate = new Date(document.getElementById('fromDate').value);
            const toDate = new Date(document.getElementById('toDate').value);

            const timeDifference = toDate - fromDate;
            document.getElementById('days').value = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
        }
        updateDays()

        $(document).ready(function () {
            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);
        });
    </script>
@endsection
