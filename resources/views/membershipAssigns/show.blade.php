@extends('layouts.app')

@section('content')
    <div class="container-fluid">

        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h3> {{$membership->ranks->rank_name}} {{ $membership->name}}
                            <label class="badge badge-success">{{ $membership->status->status_name}}</label></h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right m-1">
                            <li class="breadcrumb-item">
                                <a href="{{ route('membership-assigns') }}" class="btn btn-sm btn-dark">Back</a>
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
                <button class="tab-link" onclick="openPage('Loans', this, 'whitesmoke')">Loans</button>
                <button class="tab-link" onclick="openPage('Withdrawals', this, 'whitesmoke')">Withdrawals</button>
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
                                        <span>{{ $membership->regimental_number ? : '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">Decorations :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->decorations ? : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">Type :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->type ? : '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">Member Category :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->category->category_name ? : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">Employee Number :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->comment ? : '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="title_name">E Number :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $membership->enumber ? : '-' }}</span>
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
                                        <span>{{$membership->nic ? : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="district">District :</label>
                                    <div class="col-sm-6">
{{--                                        <span>{{$membership->district->district_name ? : '-' }}</span>--}}
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="email">Email :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->email ? : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="army_enlist">Army Enlisted :</label>
                                    <div class="col-sm-6">
                                        <span>{{ $armyEnlisted ? : '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="abf_joined">ABF Joined:</label>
                                    <div class="col-sm-6">
                                        <span>{{ $abfJoined ? : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="regiment_name">Regiment :</label>
                                    <div class="col-sm-6">
{{--                                        <span>{{$membership->regiments->regiment_name ? : '-' }}</span>--}}
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="unit_name">Unit :</label>
                                    <div class="col-sm-6">
{{--                                        <span>{{$membership->units->unit_name ? : '-' }}</span>--}}
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
                                    <label class="col-sm-5" for="regiment_name">Bank Account :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->account_no ? : '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-6 row">
                                    <label class="col-sm-5" for="unit_name">Bank :</label>
                                    <div class="col-sm-6">
                                        <span>{{$membership->bank_name ? : '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-header">
                            <div class="card-title">Unit Transfer Details</div>
                        </div>
                        <div class="card-body">
                            @if ($membership->transfers->count() > 0)
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th width="110px">Date</th>
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
                            <div class="card-title">Nominee Details</div>
                        </div>
                        <div class="card-body">
                            @if ($membership->nominees->count() > 0)
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Nic</th>
                                        <th>Relationship</th>
                                        <th>Percentage</th>
                                        <th>Year</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($membership->nominees as $nominee)
                                        <tr>
                                            <td>{{ $nominee->name ? : '-' }}</td>
                                            <td>{{ $nominee->nomineenic ? : '-' }}</td>
                                            <td>{{ $nominee->relationship ? $nominee->relationship->relationship_name : '-' }}</td>
                                            <td>{{ $nominee->percentage ? : '-' }}</td>
                                            <td>{{ $nominee->year ? \Carbon\Carbon::parse($nominee->year)->format('Y-m-d'): '-' }}</td>
                                        </tr>
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
                </div>
                <div id="Contribution" class="tab-content">
                    <div class="container-fluid">
                        <div class="card-header">
                            <div class="card-title">Contribution</div>
                        </div>
                        <div class="card-body">
                            @if ($membership->contributionsSummary->count() > 0)
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Year</th>
                                        <th>Amount</th>
                                        <th>Opening Balance</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($membership->contributionsSummary as $summary)
                                        <tr>
                                            <td>{{ $summary->year ? : '-' }}</td>
                                            <td>{{ $summary->contribution_amount ? : '-' }}</td>
                                            <td>{{ $summary->opening_balance ? : '-' }}</td>
                                        </tr>
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

                        {{--<div class="card-body overflow-auto">
                            @if ($membership->contributions->count() > 0)
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Year</th>
                                        @for ($month = 1; $month <= 12; $month++)
                                            <th>{{ date('F', mktime(0, 0, 0, $month, 1)) }}</th>
                                        @endfor
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($yearlyContribution as $year => $contributions)
                                        <tr>
                                            <td>{{ $year }}</td>
                                            @for ($month = 1; $month <= 12; $month++)
                                                @php
                                                    $contribution = $contributions->firstWhere('month', $month);
                                                @endphp
                                                <td>{{ $contribution ? number_format($contribution->amount, 2) : '-' }}</td>
                                            @endfor
                                        </tr>
                                        <tr>
                                            <td>Interest</td>
                                            <td>values</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>


                            @else
                                <div class="col-6 row">
                                    <div class="col-sm-5 text-warning">
                                        <span>Not Assigned</span>
                                    </div>
                                </div>
                            @endif
                        </div>--}}
                    </div>
                </div>
                <div id="Loans" class="tab-content">
                    <h3>Loans</h3>
                    <p>Loans!</p>
                </div>
                <div id="Withdrawals" class="tab-content">
                    <h3>Withdrawals</h3>
                    <p>Withdrawals!</p>
                </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('/js/membership-js.js') }}"> </script>
@endpush

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('/css/member-profile.css') }}"/>
@endpush

