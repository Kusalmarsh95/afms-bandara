@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>Create Yearly Contributions History</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('memberships.show', $membershipId) }}" class="btn btn-sm btn-dark">Back</a>
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
            <div class="card-body">
                <div id="devStage" class="form-card">
                    <form action="{{ route('store-yearly-contribution', $membershipId) }}" method="POST">
                        @csrf
                        <div class="card-body" id="hr">
                            <div class="row">
                                <div class="col-md-1">
                                    <strong>Year</strong>
                                </div>
                                <div class="col-md-1">
                                    <strong>Quarter</strong>
                                </div>
                                <div class="col-md-2">
                                    <strong>Opening Balance</strong>
                                </div>
                                <div class="col-md-1">
                                    <strong>Rate</strong>
                                </div>
                                <div class="col-md-2">
                                    <strong>Interest</strong>
                                </div>
                                <div class="col-md-2">
                                    <strong>Contribution</strong>
                                </div>
                                <div class="col-md-2">
                                    <strong>Closing Balance</strong>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <select class="form-control year" data-live-search="true" name="year[]" required>
                                            <option value="" disabled selected>Year</option>
{{--                                            @foreach($interestRates as $interestRate)--}}
{{--                                                <option value="{{ $interestRate->year }}">{{ $interestRate->year }}</option>--}}
{{--                                            @endforeach--}}
                                            @php
                                                $seenYears = [];
                                            @endphp
                                            @foreach($interestRates as $interestRate)
                                                @if(!in_array($interestRate->year, $seenYears))
                                                    <option value="{{ $interestRate->year }}">{{ $interestRate->year }}</option>
                                                    @php
                                                        $seenYears[] = $interestRate->year;
                                                    @endphp
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <select name="icp_id[]" class="form-control quarter" data-live-search="true" required>
                                            <option selected value="" disabled>Select</option>
                                            <option value="0">Year</option>
                                            <option value="1">H-1</option>
                                            <option value="2">H-2</option>
                                            <option value="10">Q-1</option>
                                            <option value="20">Q-2</option>
                                            <option value="30">Q-3</option>
                                            <option value="40">Q-4</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control opening_balance" name="opening_balance[]">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control interest_rate" name="rate[]" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control yearly_interest" name="yearly_interest[]" readonly>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control contribution_amount" name="contribution_amount[]" >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control closing_balance" name="closing_balance[]" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-sm btn-info mt" id="addCategory">Add New</button>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-sm btn-success mt-2">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            // Store interest rates in a JavaScript object for easy lookup
            const interestRates = @json($interestRates).reduce((acc, rate) => {
                const key = rate.year + '-' + rate.icp_id; // Create a unique key
                acc[key] = rate.interest_rate; // Store interest rate by key
                return acc;
            }, {});

            function updateInterestRate(row) {
                const year = row.find('.year').val();
                const icp_id = row.find('.quarter').val();
                const openingBalance = parseFloat(row.find('.opening_balance').val()) || 0;
                const contributionAmount = parseFloat(row.find('.contribution_amount').val()) || 0;

                if (year && icp_id) {
                    const key = year + '-' + icp_id;
                    const interestRate = interestRates[key] || null;

                    const $rateInput = row.find('.interest_rate');
                    const $yearlyInterestInput = row.find('.yearly_interest');
                    const $closingBalanceInput = row.find('.closing_balance');

                    if (interestRate) {
                        $rateInput.val(interestRate);

                        const yearlyInterest = (openingBalance * interestRate) / 100; // Assuming interest rate is a percentage
                        $yearlyInterestInput.val(yearlyInterest.toFixed(2)); // Display as fixed decimal

                        const closingBalance = openingBalance + contributionAmount + yearlyInterest;
                        $closingBalanceInput.val(closingBalance.toFixed(2)); // Display as fixed decimal
                    } else {
                        $rateInput.val('');
                        $yearlyInterestInput.val('');
                        $closingBalanceInput.val('');
                    }
                }
            }

            $('#hr').on('change', '.year, .quarter', function () {
                const row = $(this).closest('.row');
                updateInterestRate(row);
            });

            // Live update for opening balance
            $('#hr').on('keyup', '.opening_balance, .contribution_amount', function () {
                const row = $(this).closest('.row');
                updateInterestRate(row);
            });

            function addCategoryRow() {
                var newRow = `
                <div class="row">
                    <div class="col-md-1">
                        <div class="form-group">
                            <select class="form-control year" data-live-search="true" name="year[]" required>
                                <option value="" disabled selected>Year</option>
                                @php
                    $seenYears = [];
                @endphp
                @foreach($interestRates as $interestRate)
                @if(!in_array($interestRate->year, $seenYears))
                <option value="{{ $interestRate->year }}">{{ $interestRate->year }}</option>
                                                    @php
                    $seenYears[] = $interestRate->year;
                @endphp
                @endif
                @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <select name="icp_id[]" class="form-control quarter" data-live-search="true" required>
                    <option selected value="" disabled>Select</option>
                    <option value="0">Year</option>
                        <option value="1">H-1</option>
                        <option value="2">H-2</option>
                        <option value="10">Q-1</option>
                        <option value="20">Q-2</option>
                        <option value="30">Q-3</option>
                        <option value="40">Q-4</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <input type="text" class="form-control opening_balance" name="opening_balance[]">
            </div>
        </div>
        <div class="col-md-1">
            <div class="form-group">
                <input type="text" class="form-control interest_rate" name="rate[]" readonly>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <input type="text" class="form-control yearly_interest" name="yearly_interest[]" readonly>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <input type="text" class="form-control contribution_amount" name="contribution_amount[]">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <input type="text" class="form-control closing_balance" name="closing_balance[]">
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-trash-alt"></i></button>
        </div>
    </div>
`;
                $('#hr').append(newRow);
            }

            $('#addCategory').click(function (e) {
                e.preventDefault();
                addCategoryRow();
            });

            $('#hr').on('click', '.remove-row', function () {
                $(this).closest('.row').remove();
            });
        });
    </script>

@endsection
