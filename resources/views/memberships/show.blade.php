@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @elseif ($message = Session::get('error'))
            <div class="alert alert-danger">
                <p>{{ $message }}</p>
            </div>
        @endif
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3> {{$membership->ranks->rank_name ?? '-'}} {{ $membership->name}}
                            <label class="badge badge-success">{{ $membership->status->status_name ?? '-'}}</label></h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('memberships.index') }}" class="btn btn-sm btn-dark">Back</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <div class="card">
            <div class="card-header">
                <button class="tab-link" onclick="openPage('Profile', this, 'whitesmoke')" id="defaultOpen">View Profile</button>
                <button class="tab-link" onclick="openPage('Contribution', this, 'whitesmoke')">Contribution</button>
                <button class="tab-link" onclick="openPage('Withdrawals', this, 'whitesmoke')">Withdrawals</button>
                <button class="tab-link" onclick="openPage('Loans', this, 'whitesmoke')">Loans</button>
                <div id="Profile" class="tab-content">
                    <div class="container-fluid">
                        <div class="card-header">
                            <div class="card-title">Personal Details</div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">Regimental Number :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->regimental_number ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">Decorations :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->decorations ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">Type :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->type ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">Member Category :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->category->category_name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">Employee Number :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->comment ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">E Number :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->enumber ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">Date of Birth :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->dob ? \Carbon\Carbon::parse($membership->dob)->format('Y-m-d') : 'Date not specified' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">NIC :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->nic ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="district">District :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->district->district_name ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="email">Email :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->email ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="army_enlist">Army Enlisted :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $armyEnlisted ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="abf_joined">Retirement Date:</label>
                                    <div class="col-sm-6">
                                        <span>{{ $retirement ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="abf_joined">ABF Joined:</label>
                                    <div class="col-sm-6">
                                        <span>{{ $abfJoined ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="army_enlist">Contribution:</label>
                                    <div class="col-sm-6">
                                        <span>{{ number_format($membership->contribution_amount,2) ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="regiment_name">Regiment :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->regiments->regiment_name ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="unit_name">Unit :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->units->unit_name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="suwasahana">Suwasahana Loan :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->suwasahana == 1 ? 'Yes' : 'No' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="loan10month">10 Month Loan :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->loan10month == 1 ? 'Yes' : 'No' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="telephone_mobile">Mobile Number :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->telephone_mobile ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header">
                            <div class="card-title">Bank Details</div>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="account_no">Bank Account :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->account_no ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="account_no">Bank :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->bank_name ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="bank_name">Branch :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->branch_name ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header">
                            <div class="card-title">Changed History</div>
                        </div>
                        <div class="card-body">
                            @if ($membership->transfers->count() > 0)
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="width: 110px">Date</th>
                                        <th>New No</th>
                                        <th>Old No</th>
                                        <th>New Category</th>
                                        <th>Old Category</th>
                                        <th>New Rank</th>
                                        <th>Old Rank</th>
                                        <th>New Unit</th>
                                        <th>Old Unit</th>
                                        <th>New Status</th>
                                        <th>Old Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($membership->transfers as $transfer)
                                        <tr>
                                            <td>{{ $transfer->date_of_transfer ? \Carbon\Carbon::parse($transfer->date_of_transfer)->format('Y-m-d') : 'Date not specified' }}</td>
                                            <td>{{ $transfer->new_regimental_number ?: '-' }}</td>
                                            <td>{{ $transfer->old_regimental_number ?: '-' }}</td>
                                            <td>{{ $transfer->newCategory ? $transfer->newCategory->category_name : '-' }}</td>
                                            <td>{{ $transfer->oldCategory ? $transfer->oldCategory->category_name : '-' }}</td>
                                            <td>{{ $transfer->newRank ? $transfer->newRank->rank_name : '-' }}</td>
                                            <td>{{ $transfer->oldRank ? $transfer->oldRank->rank_name : '-' }}</td>
                                            <td>{{ $transfer->newUnit ? $transfer->newUnit->unit_name : '-' }}</td>
                                            <td>{{ $transfer->oldUnit ? $transfer->oldUnit->unit_name : '-' }}</td>
                                            <td>{{ $transfer->newStatus ? $transfer->newStatus->status_name : '-' }}</td>
                                            <td>{{ $transfer->oldStatus ? $transfer->oldStatus->status_name : '-' }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="col-6 row">
                                    <div class="col-sm-5">
                                        <span>No transfer history available</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-header">
                            <div class="card-title">Absent History</div>
                        </div>
                        <div class="card-body">
                            <div class="pull-left mb-2">
                                @can('memberships-registered-absent-data-add')
                                <a class="btn btn-sm btn-outline-success" href="{{ route('absents.create', $membership->id) }}"> Add Absent Data</a>
                                @endcan
                            </div>
                            @if ($membership->absents->count() > 0)
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="width: 50px">No</th>
                                        <th>Absent From</th>
                                        <th>Absent To</th>
                                        <th>Absent Days</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $i=0;
                                    @endphp
                                    @foreach ($membership->absents as $absent)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $absent->from ? \Carbon\Carbon::parse($absent->from)->format('Y-m-d') : 'Date not specified' }}</td>
                                            <td>{{ $absent->to ? \Carbon\Carbon::parse($absent->to)->format('Y-m-d') : 'Date not specified' }}</td>
                                            <td>{{ $absent->days ?: '-' }}</td>
                                            <td class="text-center">
                                                <a class="btn" href="{{ route('absents.edit',$absent->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                                                <button class="btn" data-toggle="modal" data-target="#confirmDeleteAbsent" data-id="{{ $absent->id }}">
                                                    <i class="fas fa-trash-alt" style="color: red;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="col-6 row">
                                    <div class="col-sm-10 text-warning">
                                        <span>No absent history available</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="modal fade" id="confirmDeleteAbsent" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteAbsentLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmDeleteAbsentLabel">Confirm Delete</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to delete this member?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <form id="deleteAbsentForm" method="POST" action="">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header">
                            <div class="card-title">Nominee Details</div>
                        </div>
                        <div class="card-body">
                            <div class="pull-left mb-2">
                                @can('memberships-registered-nominee-add')
                                <a class="btn btn-sm btn-outline-success" href="{{ route('nominees.create', ['membership_id' => $membership->id]) }}"> Add Nominee</a>
                                @endcan
                            </div>
                            @if ($membership->nominees->count() > 0)
                                <table class="table table-bordered" id="nominee">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>NIC</th>
                                        <th>Relationship</th>
                                        <th>Percentage</th>
                                        <th>Year</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($membership->nominees as $nominee)
                                        @if($nominee->accepted==1)
                                        <tr>
                                            <td>{{ $nominee->name ?? '-' }}</td>
                                            <td>{{ $nominee->nomineenic ?? '-' }}</td>
                                            <td>{{ $nominee->relationship ? $nominee->relationship->relationship_name : '-' }}</td>
                                            <td>{{ $nominee->percentage ?? '-' }}</td>
                                            <td>{{ $nominee->year ? \Carbon\Carbon::parse($nominee->year)->format('Y-m-d'): '-' }}</td>
                                            <td class="text-center">
                                                @if($nominee->enabled==0 )
                                                    <i class="fas fa-user-slash" title="Nominee Inactive"></i>
                                                @endif
                                                    @can('nominees-edit')
                                                        <a class="btn" href="{{ route('nominees.edit',$nominee->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                                                    @endcan
                                                    @can('nominees-delete')
                                                        <button class="btn" data-toggle="modal" data-target="#confirmDeleteModal" data-id="{{ $nominee->id }}">
                                                            <i class="fas fa-trash-alt" style="color: red;"></i>
                                                        </button>
                                                    @endcan
                                            </td>
                                        </tr>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="col-6 row">
                                    <div class="col-sm-5 text-warning">
                                        <span>Not nominated</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete this member?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <form id="deleteNomineeForm" method="POST" action="">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="Contribution" class="tab-content">
                    <div class="container-fluid">
                        <div class="card-header">
                            <div class="card-title">Contribution</div>
                            <div class="float-right">
                                @can('memberships-registered-contribution-add')
                                    <a class="btn btn-sm btn-outline-success" href="{{ route('monthlyDeductions.create', ['id' => $membership->id]) }}"> Add / Deduct</a>
                                @endcan
                                @if ($membership->status->id != 8)
                                    @can('memberships-registered-contribution-add')
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('correctionCreate', ['id' => $membership->id]) }}"> Correction</a>
                                    @endcan
                                @endif
                            </div>
                        </div>
                        <div class="card mt-2">
                            <div class="card-body">
                                @if ($fundBalance)
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Fund Balance</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>{{ now()->format('Y-m-d') }}</td>
                                            <td>LKR {{ number_format($fundBalance,2)  ? : '-' }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-6 row">
                                        <div class="col-sm-5 text-warning">
                                            <span>No data</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div id="transaction">
                            <div class="card">
                                <div class="card-header">
                                    <a class="card-link" data-toggle="collapse" href="#collapseData" id="transaction">
                                        All Transaction Details
                                        <span class="float-right"><i class="fas fa-chevron-right"></i></span>
                                    </a>
                                </div>
                                <div id="collapseData" class="collapse" data-parent="#transaction">
                                    <div class="col-md-12 text-right">
                                        <a class="btn" href="{{ route('ledger-sheet', $membership->id) }}"><i class="fas fa-file-pdf text-red"></i></a>
                                    </div>
                                    <div class="card-body">
                                        @if ($transactions->count() > 0)
                                            <table class="table table-bordered" id="contributionTable">
                                                <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Transaction Type</th>
                                                    <th>Remark</th>
                                                    <th>Transaction Amount</th>
                                                    <th>Balance</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($transactions as $transaction)
                                                    <tr>
                                                        <td>{{ $transaction->transaction_date ? : '-' }}</td>
                                                        <td>{{ $transaction->type ? : '-' }}</td>
                                                        <td>{{ $transaction->remark ? : '-' }}</td>
                                                        <td>{{ number_format($transaction->transaction_amount,2)  ? : '-' }}</td>
                                                        <td>{{ number_format($transaction->balance,2)  ? : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="col-6 row">
                                                <div class="col-sm-5 text-warning">
                                                    <span>No data</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="yearlSummary">
                            <div class="card">
                                <div class="card-header">
                                    <a class="card-link" data-toggle="collapse" href="#collapseSummary" id="summary">
                                        Contribution Yearly Summary
                                        <span class="float-right"><i class="fas fa-chevron-right"></i></span>
                                    </a>
                                </div>
                                <div id="collapseSummary" class="collapse" data-parent="#yearlSummary">
                                    <div class="card-body">
                                        @if ($membership->contributionsSummary->count() > 0)
                                            @can('memberships-registered-opening-balance-edit')
                                            <a class="btn btn-sm btn-outline-info float-right" href="{{ route('edit-calculation', ['id' => $membership->id]) }}">Edit Opening Balance</a>
                                            @endcan
{{--                                            <a class="btn btn-sm btn-outline-info float-right" href="{{ route('edit-calculation', ['id' => $membership->id]) }}">Edit Contribution</a>--}}
                                            <table class="table table-bordered" id="summaryTable">
                                                <thead>
                                                <tr>
                                                    <th>Year</th>
                                                    <th>Opening Balance</th>
                                                    <th>Deposited Amount</th>
                                                    <th>Interest</th>
                                                    <th>Closing balance</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach ($membership->contributionsSummary as $summary)
                                                    <tr>
                                                        <td>{{ $summary->year ? : '-' }}</td>
                                                        <td>{{ number_format($summary->opening_balance,2) ? : '-' }}</td>
                                                        <td>{{ number_format($summary->contribution_amount,2)  ? : '-' }}</td>
                                                        <td>{{ number_format($summary->yearly_interest,2) ? : '-' }}</td>
                                                        <td>{{ number_format($summary->closing_balance,2) ? : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <div class="row">
                                                <div class="col-sm-6 text-warning">
                                                    <span>No data</span>
                                                </div>
                                                <div class="col-sm-6">
                                                    @can('memberships-registered-opening-balance-edit')
                                                        <a class="btn btn-sm btn-outline-warning float-right" href="{{ route('create-yearly-contribution', ['id' => $membership->id]) }}">Create History</a>
                                                    @endcan
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="Withdrawals" class="tab-content">
                    <div class="container-fluid">
                        <div class="card-header">
                            <div class="card-title">Withdrawals</div>
                        </div>
                        <div class="card-body">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h6 class="mb-0">Partial Withdrawals</h6>
                                        </div>
                                        @can('withdrawals')
                                            @if ($membership->member_status_id != 8 && $membership->member_status_id != 16 && $membership->member_status_id != 3)
                                                <div class="col-auto ms-auto">
                                                    @can('memberships-registered-withdrawal-special-add')
                                                        <a class="btn btn-sm btn-outline-warning" href="{{ route('withdrawals.createSpecial', ['id' => $membership->id]) }}">Special Withdrawal</a>
                                                    @endcan
                                                    @can('memberships-registered-withdrawal-partial-add')
                                                        <a class="btn btn-sm btn-outline-success" href="{{ route('withdrawals.create', ['id' => $membership->id]) }}">New Withdrawal</a>
                                                    @endcan
                                                </div>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if ($membership->partialWithdrawalApplication->count() > 0)
                                        <table class="table table-bordered" id="PartialWithdrawalTable">
                                            <thead>
                                            <tr>
                                                <th>Application No</th>
                                                <th>Registered Date</th>
                                                <th>Status</th>
                                                <th>Withdraw Amount</th>
                                                <th>Voucher Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($membership->partialWithdrawalApplication as $partialWithdraw)
                                                <tr>
                                                    <td>{{ $partialWithdraw->application_reg_no ? : '-' }}</td>
                                                    <td>{{ $partialWithdraw->registered_date ? (new DateTime($partialWithdraw->registered_date))->format('Y-m-d') : '-' }}</td>
                                                    <td>
                                                        @if($partialWithdraw->processing == 0 & $partialWithdraw->status_id == 614)
                                                            Paid
                                                        @elseif($partialWithdraw->processing == 1)
                                                            Registered
                                                        @elseif($partialWithdraw->processing == 2 | $partialWithdraw->status_id == 490)
                                                            Rejected
                                                        @elseif($partialWithdraw->processing == 3)
                                                            Processing
                                                        @elseif($partialWithdraw->processing == 4 | $partialWithdraw->processing == 5)
                                                            Approved
                                                        @elseif($partialWithdraw->processing == 6)
                                                            Disburse
                                                        @else
                                                            Incomplete
                                                        @endif
                                                    </td>
                                                    <td>LKR {{ $partialWithdraw->withdrawal ? number_format($partialWithdraw->withdrawal->total_withdraw_amount, 2) : '-' }}</td>
                                                    <td>LKR {{ $partialWithdraw->withdrawal ? number_format($partialWithdraw->withdrawal->voucher_amount, 2) : '-' }}</td>
                                                    <td>
                                                        @if($partialWithdraw)
                                                            <a class="btn" href="{{ route('withdrawals.show',$partialWithdraw->id) }}"><i class="fas fa-eye" style="color: lightseagreen;"></i></a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="col-6 row">
                                            <div class="col-sm-5 text-warning">
                                                <span>No data</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <h6 class="mb-0">Full Withdrawals</h6>
                                        @can('withdrawals')
                                        @if ($membership->status->id != 8 && $membership->status->id != 16)
                                            @can('memberships-registered-withdrawal-full-add')
                                            <a class="btn btn-sm btn-outline-success ml-auto" href="{{ route('withdrawals.createFull', ['id' => $membership->id]) }}">Withdraw</a>
                                            @endcan
                                        @endif
                                        @endcan
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if ($membership->fullWithdrawalApplication->count() > 0)
                                        <table class="table table-bordered" id="PartialWithdrawalTable">
                                            <thead>
                                            <tr>
                                                <th>Application No</th>
                                                <th>Registered Date</th>
                                                <th>Status</th>
                                                <th>Withdraw Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($membership->fullWithdrawalApplication as $fullWithdraw)
                                                <tr>
                                                    <td>{{ $fullWithdraw->application_reg_no ? : '-' }}</td>
                                                    <td>{{ $fullWithdraw->registered_date ? (new DateTime($fullWithdraw->registered_date))->format('Y-m-d') : '-' }}</td>
                                                    <td>
                                                        @if($fullWithdraw->processing == 0)
                                                            Paid
                                                        @elseif($fullWithdraw->processing == 1)
                                                            Registered
                                                        @elseif($fullWithdraw->processing == 2)
                                                            Rejected
                                                        @elseif($fullWithdraw->processing == 3)
                                                            Processing
                                                        @elseif($fullWithdraw->processing == 4)
                                                            Approved
                                                        @elseif($fullWithdraw->processing == 5)
                                                            Disburse
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td>LKR {{ $fullWithdraw->fullWithdrawal ? number_format($fullWithdraw->fullWithdrawal->withdrawal_amount, 2) : '-' }}</td>
                                                    <td>
                                                        @can('withdrawals-full-show')
                                                            <a class="btn" href="{{ route('withdrawals.showFull',$fullWithdraw->id) }}"><i class="fas fa-eye" style="color: lightseagreen;"></i></a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="col-6 row">
                                            <div class="col-sm-10 text-warning">
                                                <span>No data for full withdrawal</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                <div id="Loans" class="tab-content">
                    <div class="container-fluid">
                        <div class="card-header">
{{--                            <div class="card-title">Loans</div>--}}
                            <div class="float-right">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <h6 class="mb-0">Loans</h6>
                                        @can('loans')
                                        @if ($membership->member_status_id != 8 && $membership->member_status_id != 16 && $membership->member_status_id != 3)
                                            @can('memberships-registered-loan-add')
                                            <a class="btn btn-sm btn-outline-success ml-auto" href="{{ route('loan.create', ['id' => $membership->id]) }}">New Loan</a>
                                            @endcan
                                        @endif
                                        @endcan
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if ($membership->loanApplications->count() > 0)
                                        <table class="table table-bordered" id="PartialWithdrawalTable">
                                            <thead>
                                            <tr>
                                                <th>Application No</th>
                                                <th>Registered Date</th>
                                                <th>Status</th>
                                                <th>Approved Amount</th>
                                                <th>Recovered Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($membership->loanApplications as $loanApplication)
                                                @if($loanApplication->status_id!=3210 & $loanApplication->processing != 2)
                                                    <tr>
                                                        <td>{{ $loanApplication->application_reg_no ? : '-' }}</td>
                                                        <td>{{ $loanApplication->registered_date ? (new DateTime($loanApplication->registered_date))->format('Y-m-d') : '-' }}</td>
                                                        <td>
                                                            @if ($loanApplication->loan->settled == 1)
                                                                Setteled @if($loanApplication->directSettlement)<label class="badge badge-info">Direct</label>
                                                                @elseif($loanApplication->absentSettlement)<label class="badge badge-primary">Absent</label>
                                                                @endif
                                                            @elseif($loanApplication->processing == 0)
                                                                Recovering
                                                            @elseif($loanApplication->processing == 1)
                                                                Registered
                                                            @elseif($loanApplication->processing == 2)
                                                                Rejected
                                                            @elseif($loanApplication->processing == 3)
                                                                Processing
                                                            @elseif($loanApplication->processing == 4)
                                                                Approved
                                                            @elseif($loanApplication->processing == 5)
                                                                Disburse
                                                            @elseif($loanApplication->processing == 6)
                                                                To be banked
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                        <td>LKR {{ number_format($loanApplication->approved_amount, 2) ? : '-' }}</td>
                                                        <td>LKR {{ $loanApplication->loan ? number_format($loanApplication->loan->total_recovered_capital, 2) : '-' }}</td>
                                                        <td>
                                                            {{--@if($loanApplication->processing==0)--}}
                                                                {{--<a class="btn" href="{{ route('loan-ledger', $loanApplication->id) }}"><i class="fas fa-file-pdf text-red"></i></a>--}}
                                                            {{--@endif--}}
                                                            @can('memberships-registered-loan-show')
                                                            <a class="btn" href="{{ route('loan.show',$loanApplication->id) }}"><i class="fas fa-eye" style="color: #85C1E9;"></i></a>
                                                            @endcan
                                                            @can('loans-direct-settlement-edit')
                                                            <a class="btn" href="{{ route('loan.editSettlement',$loanApplication->id) }}"><i class="fas fa-wrench" style="color: #6E2C00;"></i></a>
                                                            @endcan
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="col-6 row">
                                            <div class="col-sm-5 text-warning">
                                                <span>No data</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <h6 class="mb-0">Suwasahana Loans</h6>
                                        @can('loans')
                                            @if ($membership->status->id != 8 && $membership->status->id != 16)
                                                @can('memberships-registered-loan-add')
                                                    <a class="btn btn-sm btn-outline-success ml-auto" href="{{ route('suwasahana.create', ['id' => $membership->id]) }}">Add Suwasahana</a>
                                                @endcan
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if ($suwasahana->count() > 0)
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Voucher No</th>
                                                <th>Issued Date</th>
                                                <th>Status</th>
                                                <th>Settled Type</th>
                                                <th>Total Amount</th>
                                                <th>Recovered Amount</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($suwasahana as $suwa)
                                                <tr>
                                                    <td>{{ $suwa->ABFvoucherno ? : '-' }}</td>
                                                    <td>{{ $suwa->Issue_Date ? (new DateTime($suwa->Issue_Date))->format('Y-m-d') : '-' }}</td>
                                                    <td>@if ($suwa->settled == 1)
                                                            Setteled
                                                        @elseif($suwa->settled == 0 & $suwa->accepted == 0)
                                                            Registration Pending
                                                        @else
                                                            Recovering
                                                        @endif
                                                    </td>
                                                    <td>{{ $suwa->settled_type ? : '-' }}</td>
                                                    <td>LKR {{ number_format($suwa->total_capital + $suwa->total_interest, 2) ? : '-' }}</td>
                                                    <td>LKR {{ number_format($suwa->total_recovered_capital + $suwa->total_recovered_interest, 2) ? : '-' }}</td>
                                                    <td>
                                                        @can('memberships-registered-suwasahana-edit')
                                                        <a class="btn" href="{{ route('suwasahana.edit',$suwa->id) }}"><i class="fas fa-pen" style="color: lightseagreen;"></i></a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="col-6 row">
                                            <div class="col-sm-5 text-warning">
                                                <span>No data</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#contributionTable').DataTable({
                responsive: true,
                // paging: false,
                searching: false,
                // info: false,
                buttons: []
            });
            $('#summaryTable').DataTable({
                responsive: true,
                // paging: false,
                searching: false,
                // info: false,
                buttons: []
            });

            setTimeout(function () {
                $('.alert').fadeOut();
            }, 5000);

            $('#nominee').DataTable({
                responsive: true,
                paging: false,
                searching: false,
                info: false,
                buttons: []
            });

            $('#confirmDeleteModal').on('show.bs.modal', function (e) {
                var memberId = $(e.relatedTarget).data('id');
                var form = $('#deleteNomineeForm');
                var action = '{{ route('nominees.destroy', '') }}/' + memberId;
                form.attr('action', action);
            });
            $('#confirmDeleteAbsent').on('show.bs.modal', function (e) {
                var absentId = $(e.relatedTarget).data('id');
                var form = $('#deleteAbsentForm');
                var action = '{{ route('absents.destroy', '') }}/' + absentId;
                form.attr('action', action);
            });

            $('#transaction').on('show.bs.collapse', function (e) {
                $(e.target).prev('.card-header').find('.fas').removeClass('fa-chevron-right').addClass('fa-chevron-down');
            });
            $('#transaction').on('hide.bs.collapse', function (e) {
                $(e.target).prev('.card-header').find('.fas').removeClass('fa-chevron-down').addClass('fa-chevron-right');
            });

            $('#yearlSummary').on('show.bs.collapse', function (e) {
                $(e.target).prev('.card-header').find('.fas').removeClass('fa-chevron-right').addClass('fa-chevron-down');
            });
            $('#yearlSummary').on('hide.bs.collapse', function (e) {
                $(e.target).prev('.card-header').find('.fas').removeClass('fa-chevron-down').addClass('fa-chevron-right');
            });
        });
    </script>
@endsection
@push('scripts')
    <script src="{{ asset('/js/membership-js.js') }}"> </script>
@endpush

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('/css/member-profile.css') }}"/>
@endpush

