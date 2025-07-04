@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h4>Edit Application Details of {{$loan->product->name}}</h4>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a class="btn btn-sm btn-dark" href="{{ route('loan.index') }}">Back</a>
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
        <form id="loan" action="{{ route('loan.update', $loan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-header">
                    <h5>Personal Details</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="application_reg_no" class="col-sm-5 col-form-label">Registration Number</label>
                            <div class="col-sm-7">
                                <input type="text" id="application_reg_no" name="application_reg_no" class="form-control" value="{{$loan->application_reg_no}}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="received_date" class="col-sm-5 col-form-label">Received Date</label>
                            <div class="col-sm-7">
                                <input type="date" name="received_date" class="form-control" value="{{ $loan->received_date ? (new DateTime($loan->received_date))->format('Y-m-d') : '-' }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="regimental_number" class="col-sm-5 col-form-label">Regimental Number</label>
                            <div class="col-sm-7">
                                <input type="text" id="regimental_number" class="form-control" value="{{$loan->membership->regimental_number ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="unit" class="col-sm-5 col-form-label">Unit</label>
                            <div class="col-sm-7">
                                <input type="text" id="unit" class="form-control" value="{{ $loan->membership->units->unit_name ?? '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="regiment" class="col-sm-5 col-form-label">Regiment</label>
                            <div class="col-sm-7">
                                <input type="text" id="regiment" class="form-control" value="{{ $loan->membership->regiments->regiment_name ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="mobile_no" class="col-sm-5 col-form-label">Mobile Number</label>
                            <div class="col-sm-7">
                                <input type="text" id="mobile_no" class="form-control" value="{{ $loan->membership->telephone_mobile ? : 'NA'}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="rank" class="col-sm-5 col-form-label">Rank</label>
                            <div class="col-sm-7">
                                <input type="text" id="rank" class="form-control" value="{{ $loan->membership->ranks->rank_name ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="name" class="col-sm-5 col-form-label">Name</label>
                            <div class="col-sm-7">
                                <input type="text" id="name" class="form-control" value="{{ $loan->membership->name ?? '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <h5>Account Details</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="bank_acc_no" class="col-sm-4 col-form-label">Bank Account</label>
                            <div class="col-sm-8">
                                <input type="text" name="bank_acc_no" class="form-control" value="{{$membership->account_no ?? '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="bank_code" class="col-sm-4 col-form-label">Bank code</label>
                            <div class="col-sm-8">
                                <input type="text" name="bank_code" class="form-control" value="{{$membership->bank_code ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="bank_name" class="col-sm-4 col-form-label">Bank Name </label>
                            <div class="col-sm-8">
                                <input type="text" name="bank_name" class="form-control" value="{{$membership->bank_name ?? '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="branch_code" class="col-sm-4 col-form-label">Branch Code</label>
                            <div class="col-sm-8">
                                <input type="text" name="branch_code" class="form-control" value="{{$membership->branch_code ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="bank_branch" class="col-sm-4 col-form-label">Branch Name</label>
                            <div class="col-sm-8">
                                <input type="text" name="bank_branch" class="form-control" value="{{$membership->branch_name ?? '-' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <h5>Suwasahana Loan Details</h5>
                </div>
                <div class="card-body">
                    @if ($suwasahana)
                        @if($suwasahana->settled==1)
                            <div class="col-6 row">
                                <div class="col-sm-5 text-warning">
                                    <span>Suwasahana Loan Already Settled</span>
                                    <input type="hidden" name="suwasahana_amount" value="0">
                                </div>
                            </div>
                        @else
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-6 col-form-label">Loan Amount</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value=" {{ number_format($suwasahanaAmount,2) ? : 0}}" readonly>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-6 col-form-label">Recovered Amount</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" value=" {{ number_format($recoveredAmount,2) ? : 0}}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="suwasahana_amount" class="col-sm-6 col-form-label">Total Due Amount</label>
                                    <div class="col-sm-6">
                                        <input type="hidden" name="suwasahana_amount" class="form-control" value=" {{ $dueSuwasahana ? : 0}}" readonly>
                                        <input type="text" class="form-control" value=" {{ number_format($dueSuwasahana,2 ?: 'No')}}" readonly>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="col-6 row">
                            <div class="col-sm-12 text-warning">
                                <span>Did not register for a suwasahana loan</span>
                                <input type="hidden" name="suwasahana_amount" value="0">
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-header">
                    <h5>Salary Details</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="total_salary" class="col-sm-5 col-form-label">Total Salary</label>
                            <div class="col-sm-7">
                                <input type="text" id="total_salary" class="form-control" name="total_salary" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="salary_40" class="col-sm-5 col-form-label">40% Salary</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="salary_40" name="salary_40" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="basic_salary" class="col-sm-5 col-form-label">Basic Salary</label>
                            <div class="col-sm-7">
                                <input type="text" id="basic_salary" class="form-control" name="basic_salary" value="{{ number_format($loan->basic_salary ?: 0, 2)}}">
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="deductions" class="col-sm-5 col-form-label">Total Deductions</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" id="deductions" name="deductions" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="good_conduct" class="col-sm-5 col-form-label">Good Conduct</label>
                            <div class="col-sm-7">
                                <input type="text" id="good_conduct" name="good_conduct" class="form-control" value="{{ number_format($loan->good_conduct ?: 0, 2) }}">
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="ten_month_loan" class="col-sm-5 col-form-label">10 Month loan</label>
                            <div class="col-sm-7">
                                <input type="text" id="ten_month_loan" name="ten_month_loan" class="form-control " value="{{ number_format($loan->ten_month_loan ?: 0, 2)}}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="incentive" class="col-sm-5 col-form-label">Incentive</label>
                            <div class="col-sm-7">
                                <input type="text" id="incentive" name="incentive" class="form-control" value="{{ number_format($loan->incentive ?: 0 , 2) }}">
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="other_loan" class="col-sm-5 col-form-label">Other Loan Deduction</label>
                            <div class="col-sm-7">
                                <input type="text" id="other_loan" name="other_loan" class="form-control" value="{{ number_format($loan->other_loan ?: 0, 2) }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="qualification" class="col-sm-5 col-form-label">Qualification</label>
                            <div class="col-sm-7">
                                <input type="text" id="qualification" name="qualification" class="form-control" value="{{ number_format($loan->qualification ?: 0 , 2) }}">
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="festival_advance" class="col-sm-5 col-form-label">Festival Advance</label>
                            <div class="col-sm-7">
                                <input type="text" id="festival_advance" name="festival_advance" class="form-control" value="{{ number_format($loan->festival_advance ?: 0, 2) }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="ration" class="col-sm-5 col-form-label">Ration value</label>
                            <div class="col-sm-7">
                                <input type="text" id="ration" name="ration" class="form-control" value="{{ number_format($loan->ration ?: 0, 2) }}">
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="special_advance" class="col-sm-5 col-form-label">Special Advance</label>
                            <div class="col-sm-7">
                                <input type="text" id="special_advance" name="special_advance" class="form-control" value="{{ number_format($loan->special_advance ?: 0,2) }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-header">
                    <h5>Loan Details</h5>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="fund_balance" class="col-sm-5 col-form-label">Fund Balance</label>
                            <div class="col-sm-7">
                                <input type="text" name="fund_balance" class="form-control" value="{{ number_format($fundBalance ? : 0, 2, '.', ',') }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="product_id" class="col-sm-5 col-form-label">Loan Product</label>
                            <div class="col-sm-7">
                                @if(isset($loanProducts))
                                    <select id="product_id" name="product_id" class="form-control" data-live-search="true">
                                        <option disabled selected>Select Loan Product</option>
                                        @foreach($loanProducts as $loanProduct)
                                            <option value="{{ $loanProduct->id }}" {{ $loan->product_id == $loanProduct->id ? 'selected' : '' }}
                                            data-percentage="{{ $loanProduct->percentage }}" data-interest="{{ $loanProduct->interest_rate }}">
                                                {{ $loanProduct->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="calculated_amount" class="col-sm-5 col-form-label">Calculated Amount</label>
                            <div class="col-sm-7">
                                <input type="text" id="calculatedAmount" name="allowed_amount_from_fund" class="form-control" value="{{ number_format($loan->allowed_amount_from_fund ?: 0,2)}}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="no_of_installments" class="col-sm-5 col-form-label">No of Installments</label>
                            <div class="col-sm-7">
                                <input type="text" name="no_of_installments" id="installments" class="form-control" value="{{ $loan->no_of_installments ?: 0 }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="suggested_amount" class="col-sm-5 col-form-label">Maximum Amount</label>
                            <div class="col-sm-7">
                                <input type="text" name="suggested_amount" id="maxAmount" class="form-control" value="{{ number_format($loan->suggested_amount ?: 0, 2) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6 row">
                            <label for="total_amount_requested" class="col-sm-5 col-form-label">Requested Amount</label>
                            <div class="col-sm-7">
                                <input type="text" name="total_amount_requested" id="requestedAmount" class="form-control" value="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-6 row">
                            <label for="interest_rate" class="col-sm-5 col-form-label">Interest Rate </label>
                            <div class="col-sm-7">
                                <input type="text" name="interest_rate" class="form-control interest"  value="0.00" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card m-2">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-4 row">
                                <label for="fwd_to" class="col-sm-4 col-form-label">Forward To</label>
                                <div class="col-sm-8">
                                    @if(isset($usersForward))
                                        <select name="fwd_to" class="form-control" data-live-search="true" required>
                                            <option disabled selected>Assign a Officer</option>
                                            @foreach($usersForward as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                            <div class="col-8 row">
                                <label for="fwd_to_reason" class="col-sm-2 col-form-label">Remark</label>
                                <div class="col-sm-10">
                                    <input type="text" name="fwd_to_reason" class="form-control" placeholder="Please provide a remark" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" id="submitBtn" class="btn btn-sm btn-outline-info m-2">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            function formatNumberInput(inputId) {
                document.getElementById(inputId).addEventListener('input', function() {
                    var value = this.value.replace(/[^\d.]/g, ''); // Remove non-digit and non-decimal characters
                    var parts = value.split('.');
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Add commas for thousands
                    if (parts[1] && parts[1].length > 2) {
                        parts[1] = parts[1].slice(0, 2); // Truncate decimal part to two digits
                    }
                    this.value = parts.join('.');
                });
            }

            formatNumberInput('basic_salary');
            formatNumberInput('good_conduct');
            formatNumberInput('ten_month_loan');
            formatNumberInput('qualification');
            formatNumberInput('festival_advance');
            formatNumberInput('incentive');
            formatNumberInput('other_loan');
            formatNumberInput('ration');
            formatNumberInput('special_advance');

            var loanProductSelect = document.getElementById('product_id');
            var installmentsInput = document.getElementById('installments');
            var salary40Input = document.getElementById('salary_40');
            var interest;
            var calculatedAmount;

            function calculateCalculatedAmount() {
                var selectedOption = loanProductSelect.options[loanProductSelect.selectedIndex];
                var percentage = selectedOption.getAttribute('data-percentage');
                interest = selectedOption.getAttribute('data-interest');

                calculatedAmount = ({{$fundBalance}} - {{$dueSuwasahana}}) * (percentage/100);

                $('#calculatedAmount').val(calculatedAmount.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $('.interest').val(interest);

                calculateMaxAmount();
            }
            calculateCalculatedAmount();

            loanProductSelect.addEventListener('change', calculateCalculatedAmount);

            var basic_salaryInput = document.getElementsByName('basic_salary')[0];
            var goodConductInput = document.getElementsByName('good_conduct')[0];
            var incentiveInput = document.getElementsByName('incentive')[0];
            var qualificationInput = document.getElementsByName('qualification')[0];
            var rationInput = document.getElementsByName('ration')[0];
            var totalSalaryInput = document.getElementsByName('total_salary')[0];
            var festivalAdvanceInput = document.getElementsByName('festival_advance')[0];
            var otherLoanInput = document.getElementsByName('other_loan')[0];
            var specialAdvanceInput = document.getElementsByName('special_advance')[0];
            var tenMonthInput = document.getElementsByName('ten_month_loan')[0];
            var deductionsInput = document.getElementsByName('deductions')[0];

            basic_salaryInput.addEventListener('input', updateSalary);
            goodConductInput.addEventListener('input', updateSalary);
            incentiveInput.addEventListener('input', updateSalary);
            qualificationInput.addEventListener('input', updateSalary);
            rationInput.addEventListener('input', updateSalary);

            festivalAdvanceInput.addEventListener('input', updateSalary);
            otherLoanInput.addEventListener('input', updateSalary);
            specialAdvanceInput.addEventListener('input', updateSalary);
            tenMonthInput.addEventListener('input', updateSalary);

            function updateSalary() {
                var basic_salary = parseFloat(basic_salaryInput.value.replace(/,/g, '')) || 0.00;
                var goodConduct = parseFloat(goodConductInput.value.replace(/,/g, '')) || 0.00;
                var incentive = parseFloat(incentiveInput.value.replace(/,/g, '')) || 0.00;
                var qualification = parseFloat(qualificationInput.value.replace(/,/g, '')) || 0.00;
                var ration = parseFloat(rationInput.value.replace(/,/g, '')) || 0.00;

                var festivalAdvance = parseFloat(festivalAdvanceInput.value.replace(/,/g, '')) || 0.00;
                var otherLoan = parseFloat(otherLoanInput.value.replace(/,/g, '')) || 0.00;
                var specialAdvance = parseFloat(specialAdvanceInput.value.replace(/,/g, '')) || 0.00;
                var tenMonth = parseFloat(tenMonthInput.value.replace(/,/g, '')) || 0.00;

                var totalSalary = basic_salary + goodConduct + incentive + qualification + ration;
                var totalDeduction = festivalAdvance + otherLoan + specialAdvance + tenMonth;

                salary40 = totalSalary*0.4 - totalDeduction;

                totalSalaryInput.value = totalSalary.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                deductionsInput.value = totalDeduction.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                salary40Input.value = salary40.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                calculateMaxAmount();
            }
            updateSalary();

            installmentsInput.addEventListener('input', function () {
                calculateMaxAmount();
            });

            var maxAmount;
            function calculateMaxAmount() {
                var installments = parseFloat(installmentsInput.value) || 0;
                var salary40 = parseFloat(salary40Input.value.replace(/,/g, '')) || 0;
                // console.log(salary40)
                maxAmount = ((salary40 * installments * 1200) / (1200 + interest * installments));

                maxAmount = Math.floor(maxAmount / 100) * 100;

                if (maxAmount > calculatedAmount) {
                    maxAmount = Math.floor(calculatedAmount / 100) * 100;
                    $('#maxAmount').val(maxAmount.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                } else {
                    $('#maxAmount').val(maxAmount.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                }
            }

            calculateMaxAmount();
            var requestedAmountInput = document.getElementById('requestedAmount');
            var submitBtn = document.getElementById('submitBtn');

            requestedAmountInput.addEventListener('input', function () {
                var requestedAmount = parseFloat(requestedAmountInput.value) || 0.00;

                submitBtn.disabled = requestedAmount > maxAmount;

            });

            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);

        });
    </script>
@endsection


