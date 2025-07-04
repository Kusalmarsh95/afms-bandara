@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Additional Contribution</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('memberships.show', $membership->id) }}" class="btn btn-sm btn-dark">Back</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

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
                <div class="card-title">
                    <i class="fas fa-arrow-right"></i> {{ $membership->ranks->rank_name ?? '-' }} {{ $membership->name }}
                </div>
            </div>
            <div class="container-fluid">
                <form action="{{ route('monthlyDeductions.store', ['id' => $membership->id]) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-4 row">
                                <label for="year" class="col-sm-4 col-form-label">Deposit Year</label>
                                <div class="col-sm-8">
                                    <input type="number" name="year" value="{{ date('Y') }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-4 row">
                                <label for="month" class="col-sm-4 col-form-label">Deposit Month</label>
                                <div class="col-sm-8">
                                    <input type="number" name="month" value="{{ date('n') }}" class="form-control" min="1" max="12" required>
                                </div>
                            </div>
                            <div class="col-4 row">
                                <label for="amount" class="col-sm-4 col-form-label">Amount</label>
                                <div class="col-sm-8">
                                    <input type="text" name="amount" class="form-control" placeholder="Amount" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-6 row">
                                <label for="type" class="col-sm-2 col-form-label">Type</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="type" id="type" required>
                                        <option value="" disabled selected>Select Type</option>
                                        <option value="Deposit">Deposit</option>
                                        <option value="Refund">Refund</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6 row" id="reason-container" style="display: none;">
                                <label for="reason" class="col-sm-2 col-form-label">Reason</label>
                                <div class="col-sm-10">
                                    <select name="reason" id="reason" class="form-control">
                                        <option value="">Select Reason</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-12 row">
                                <label for="remark" class="col-sm-1 col-form-label">Remark</label>
                                <div class="col-sm-11">
                                    <input type="text" name="remark" class="form-control" placeholder="Remark" required>
                                </div>
                            </div>
                        </div>

                        <div class="card-header"></div>
                        <div class="card-body">
                            <div class="form-group row col-md-10 offset-2">
                                <div class="col-6 row">
                                    <label for="fwd_to" class="col-sm-4 col-form-label">For Approval</label>
                                    <div class="col-sm-8">
                                        @if(isset($users))
                                            <select name="fwd_to" class="form-control col-sm-9" data-live-search="true">
                                                <option selected>Assign an Officer</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 text-left">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Section -->
    <script type="text/javascript">
        $(document).ready(function () {
            function updateReasonDropdown() {
                const type = $('#type').val();
                const reasonContainer = $('#reason-container');
                const reasonSelect = $('#reason');
                reasonSelect.empty(); // Clear options

                if (type === 'Deposit') {
                    reasonContainer.show();
                    reasonSelect.append(new Option('Direct Deposit', 'Direct Deposit'));
                    reasonSelect.append(new Option('Unit Savings', 'Unit Savings'));
                } else if (type === 'Refund') {
                    reasonContainer.show();
                    reasonSelect.append(new Option('Unit Deduction', 'Unit Deduction'));
                    reasonSelect.append(new Option('AWOL Deduction', 'AWOL Deduction'));
                    reasonSelect.append(new Option('Advance B Recovery', 'Advance B Recovery'));
                } else {
                    reasonContainer.hide();
                }
            }

            $('#type').on('change', function () {
                updateReasonDropdown();
            });

            // Optional: show on page load if already selected (e.g. after validation error)
            updateReasonDropdown();

            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);
        });
    </script>
@endsection
