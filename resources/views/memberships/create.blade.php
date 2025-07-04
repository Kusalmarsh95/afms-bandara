@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3>New Member Registration</h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('memberships.index') }}" class="btn btn-sm btn-dark">Back</a>
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
            <script type="text/javascript">
                $(document).ready(function () {
                    setTimeout(function () {
                        $('.alert').fadeOut();
                    }, 2000);
                });
            </script>
        @endif
        <div class="card">
            <div class="card-header">
                <div class="container-fluid">
                    <form action="{{ route('memberships.store') }}" method="POST">
                        @csrf
                        <div class="card-header">
                            <div class="card-title">Personal Details</div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="army_id" class="col-sm-4 col-form-label">Army Id</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="army_id" class="form-control" id="army_id" value="{{ old('army_id') }}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="category_id" class="col-sm-4 col-form-label">Category<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        @if(isset($categories))
                                            <select name="category_id" class="form-control" data-live-search="true" required>
                                                <option selected>Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="emp_number" class="col-sm-4 col-form-label">Employee Number<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="comment" class="form-control" id="comment" value="{{ old('comment') }}" required>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="contribution_amount" class="col-sm-4 col-form-label">Contribution<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="contribution_amount" class="form-control" id="contribution_amount" value="{{ old('contribution_amount') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="date_army_enlisted" class="col-sm-4 col-form-label">Army Enlisted<span style="color: red">*</span></label>
                                    <div class="col-md-8">
                                        <input type="date" name="date_army_enlisted" id="date_army_enlisted" class="form-control" value="{{ old('date_army_enlisted') }}" required>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="dateabfenlisted" class="col-sm-4 col-form-label">ABF Joined<span style="color: red">*</span></label>
                                    <div class="col-md-8">
                                        <input type="date" name="dateabfenlisted" id="dateabfenlisted" class="form-control" value="{{ old('dateabfenlisted') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="dob" class="col-sm-4 col-form-label">Date of Birth<span style="color: red">*</span></label>
                                    <div class="col-md-8">
                                        <input type="date" name="dob" id="dob" class="form-control" value="{{ old('dob') }}" required>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="retirement_date" class="col-sm-4 col-form-label">Retirement Date<span style="color: red">*</span></label>
                                    <div class="col-md-8">
                                        <input type="date" name="retirement_date" id="retirement_date" class="form-control" value="{{ old('retirement_date') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="email" class="col-sm-4 col-form-label">Email</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="email" class="form-control" id="email" value="{{ old('email') }}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="member_status_id" class="col-sm-4 col-form-label">Member Status<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        @if(isset($status))
                                            <select name="member_status_id" class="form-control" data-live-search="true" required>
                                                <option selected value="" {{ old('member_status_id') == '' ? 'selected' : '' }}>Select Member Status</option>
                                                @foreach($status as $state)
                                                    <option value="{{ $state->id }}" {{ old('member_status_id') == $state->id ? 'selected' : '' }}>{{ $state->status_name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="name" class="col-sm-4 col-form-label">Name<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="name" class="form-control" id="name" placeholder="Name" required>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="nic" class="col-sm-4 col-form-label">NIC<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="nic" class="form-control" id="nic" value="{{ old('nic') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="regimental_number" class="col-sm-4 col-form-label">Regimental Number<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="regimental_number" class="form-control" id="regimental_number" value="{{ old('regimental_number') }}" required>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="rank_id" class="col-sm-4 col-form-label">Rank<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        @if(isset($ranks))
                                            <select name="rank_id" class="form-control" data-live-search="true" required>
                                                <option selected value="" {{ old('rank_id') == '' ? 'selected' : '' }}>Select Rank</option>
                                                @foreach($ranks as $rank)
                                                    <option value="{{ $rank->id }}" {{ old('rank_id') == $rank->id ? 'selected' : '' }}>{{ $rank->rank_name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="name" class="col-sm-4 col-form-label">Decorations</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="decorations" class="form-control" id="decorations" value="{{ old('decorations') }}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="serial_number" class="col-sm-4 col-form-label">Serial Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="serial_number" class="form-control" id="serial_number" value="{{ old('serial_number') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="regiment_id" class="col-sm-4 col-form-label">Regiment<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        @if(isset($regiments))
                                            <select name="regiment_id" class="form-control" data-live-search="true">
                                                <option selected value="" {{ old('regiment_id') == '' ? 'selected' : '' }}>Select Regiment</option>
                                                @foreach($regiments as $regiment)
                                                    <option value="{{ $regiment->id }}" {{ old('regiment_id') == $regiment->id ? 'selected' : '' }}>{{ $regiment->regiment_name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="unit_id" class="col-sm-4 col-form-label">Unit<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        @if(isset($units))
                                            <select name="unit_id" class="form-control" data-live-search="true" required>
                                                <option value="" {{ old('unit_id') == '' ? 'selected' : '' }}>Select Unit</option>
                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                                        {{ $unit->unit_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="telephone_home" class="col-sm-4 col-form-label">Mobile Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="telephone_home" class="form-control" id="telephone_home" value="{{ old('telephone_home') }}">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="telephone_mobile" class="col-sm-4 col-form-label">WhatsApp Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="telephone_mobile" class="form-control" id="telephone_mobile" value="{{ old('telephone_mobile') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="type" class="col-sm-4 col-form-label">Member Type<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        <select name="type" class="form-control" required>
                                            <option value="" {{ old('type') == '' ? 'selected' : '' }}>Select Type</option>
                                            <option value="Regular" {{ old('type') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                            <option value="Volunteer" {{ old('type') == 'Volunteer' ? 'selected' : '' }}>Volunteer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="address" class="col-sm-4 col-form-label">Working Place<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="address" class="form-control" id="address" placeholder="Working Place">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="address1" class="col-sm-4 col-form-label">Residence Address</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="address1" class="form-control" id="address1" placeholder="Address Line 1">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="address2" class="col-sm-4 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="address2" class="form-control" id="address2" placeholder="Address Line 2">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="address3" class="col-sm-4 col-form-label"></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="address3" class="form-control" id="address3" placeholder="Address Line 3">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="district_id" class="col-sm-4 col-form-label">District<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        @if(isset($districts))
                                            <select name="district_id" class="form-control" data-live-search="true">
                                                <option selected>Select District</option>
                                                @foreach($districts as $district)
                                                    <option value="{{ $district->id }}">{{ $district->district_name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="suwasahana" class="col-sm-8 col-form-label">Have a Suwasahana Loan</label>
                                    <div class="col-sm-4">
                                        <select name="suwasahana" class="form-control">
                                            <option selected>Please Select</option>
                                            <option value=1>Yes</option>
                                            <option value=0>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="loan10month" class="col-sm-8 col-form-label">Have a 10 Month Loan</label>
                                    <div class="col-sm-4">
                                        <select name="loan10month" class="form-control">
                                            <option selected>Please Select</option>
                                            <option value=1>Yes</option>
                                            <option value=0>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="enumber" class="col-sm-4 col-form-label">E Number<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="enumber" class="form-control" id="enumber" placeholder="E Number">
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="account_no" class="col-sm-4 col-form-label">Bank Account<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" name="account_no" class="form-control" id="account_no" placeholder="Bank Account Number">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="bank_code" class="col-sm-4 col-form-label">Bank Code<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        @if(isset($banks))
                                            <select name="bank_code" class="form-control" data-live-search="true">
                                                <option selected>Select Bank Code</option>
                                                @foreach($banks as $bank)
                                                    <option value="{{ $bank->id }}">{{ $bank->id }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="bank_name" class="col-sm-4 col-form-label">Bank Name<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        @if(isset($banks))
                                            <select name="bank_name" class="form-control" data-live-search="true">
                                                <option selected>Select Bank Name</option>
                                                @foreach($banks as $bank)
                                                    <option value="{{ $bank->bank_name }}">{{ $bank->bank_name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="branch_code" class="col-sm-4 col-form-label">Branch Code<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        @if(isset($branchCodes))
                                            <select name="branch_code" class="form-control" data-live-search="true">
                                                <option selected>Select Branch Code</option>
                                                @foreach($branchCodes as $branchCode)
                                                    <option value="{{ $branchCode->branch_code }}">{{ $branchCode->branch_code }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label for="branch_name" class="col-sm-4 col-form-label">Branch Name<span style="color: red">*</span></label>
                                    <div class="col-sm-8">
                                        @if(isset($branches))
                                            <select name="branch_name" class="form-control" data-live-search="true">
                                                <option selected>Select Branch Name</option>
                                                @foreach($branches as $branch)
                                                    <option value="{{ $branch->bank_branch_name }}">{{ $branch->bank_branch_name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header"></div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label for="fwd_to" class="col-sm-4 col-form-label">For Approval</label>
                                    <div class="col-sm-8">
                                        @if(isset($users))
                                            <select name="fwd_to" class="form-control" data-live-search="true">
                                                <option selected>Assign a Officer</option>
                                                @foreach($users as $user)
                                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


