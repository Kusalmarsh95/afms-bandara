@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-11 text-center">
                        <h3><strong>Outstanding Report of Registered Applications</strong></h3>
                    </div>
                    <div class="col-sm-1 text-center">
                        <a class="btn btn-primary mt-1" href="{{ route('reports.index') }}">Back</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="card">
        <div class="card-header">
            <button class="tab-link" onclick="openPage('Summary', this, '#3e7d2c')" id="defaultOpen">Summary</button>
            <button class="tab-link" onclick="openPage('Weekly', this, '#3e7d2c')">Weekly</button>
            <div id="Summary" class="tab-content">
                <div class="col-md-12 text-right">
                    <a class="btn" href="{{ route('pdf-outstanding-summary') }}"><i class="fas fa-file-pdf text-red"></i></a>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered table-secondary">
                        <thead>
                        <tr>
                            <th class="text-center" scope="col">Status</th>
                            <th class="text-center" scope="col">Loan Qty</th>
                            <th class="text-center" scope="col">Loan Amount (LKR)</th>
                            <th class="text-center" scope="col">Withdrawal Qty</th>
                            <th class="text-center" scope="col">Withdrawal Amount (LKR)</th>
                            <th class="text-center" scope="col">Full Withdrawal Qty</th>
                            <th class="text-center" scope="col">Full Withdrawal Amount (LKR)</th>
                            <th class="text-center" scope="col">Total Qty</th>
                            <th class="text-center" scope="col">Total Amount (LKR)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $sections = [
                                'Registration' => [
                                    'loan' => $loanCounts['Registration'] ?? 0,
                                    'partial' => $partialCounts['Registration'] ?? 0,
                                    'full' => $fullCounts['Registration'] ?? 0
                                ],
                                'Ledger' => [
                                    'loan' => $loanCounts['Ledger'] ?? 0,
                                    'partial' => $partialCounts['Ledger'] ?? 0,
                                    'full' => $fullCounts['Ledger'] ?? 0
                                ],
                                'Loan Recovery' => [
                                    'loan' => $loanCounts['Loan Recovery'] ?? 0,
                                    'partial' => $partialCounts['Loan Recovery'] ?? 0,
                                    'full' => $fullCounts['Loan Recovery'] ?? 0
                                ],
                                'Loan Section' => [
                                    'loan' => $loanCounts['Loan Section'] ?? 0,
                                    'partial' => $partialCounts['Loan Section'] ?? 0,
                                    'full' => $fullCounts['Loan Section'] ?? 0
                                ],
                                'Audit' => [
                                    'loan' => $loanCounts['Audit'] ?? 0,
                                    'partial' => $partialCounts['Audit'] ?? 0,
                                    'full' => $fullCounts['Audit'] ?? 0
                                ],
                                'Payment' => [
                                    'loan' => $loanCounts['Payment'] ?? 0,
                                    'partial' => $partialCounts['Payment'] ?? 0,
                                    'full' => $fullCounts['Payment'] ?? 0
                                ],
                                'Account' => [
                                    'loan' => $loanCounts['Account'] ?? 0,
                                    'partial' => $partialCounts['Account'] ?? 0,
                                    'full' => $fullCounts['Account'] ?? 0
                                ],
                                'IT' => [
                                    'loan' => $loanCounts['IT'] ?? 0,
                                    'partial' => $partialCounts['IT'] ?? 0,
                                    'full' => $fullCounts['IT'] ?? 0
                                ],
                                'Other' => [
                                    'loan' => $loanCounts['Other'] ?? 0,
                                    'partial' => $partialCounts['Other'] ?? 0,
                                    'full' => $fullCounts['Other'] ?? 0
                                ],
                            ];

                            $grandTotal = ['loan' => 0, 'partial' => 0, 'full' => 0];
                            $loanTotal = 0;
                            $partialTotal = 0;
                            $fullTotal = 0;
                        @endphp

                        @foreach ($sections as $section => $counts)
                            <tr>
                                <td>{{ $section }}</td>
                                <td class="text-center">{{ array_sum($counts['loan']) }}</td>
                                <td class="text-center">{{ number_format($loanAmounts[$section], 2) }}</td>
                                <td class="text-center">{{ array_sum($counts['partial']) }}</td>
                                <td class="text-center">{{ number_format($partialAmounts[$section], 2) }}</td>
                                <td class="text-center">{{ array_sum($counts['full']) }}</td>
                                <td class="text-center">{{ number_format($fullAmounts[$section], 2) }}</td>
                                @php
                                    $total = array_sum($counts['loan']) + array_sum($counts['partial']) + array_sum($counts['full']);
                                    $totalAmount = $loanAmounts[$section] + $partialAmounts[$section] + $fullAmounts[$section];
                                    $grandTotal['loan'] += array_sum($counts['loan']);
                                    $loanTotal += $loanAmounts[$section];
                                    $grandTotal['partial'] += array_sum($counts['partial']);
                                    $partialTotal += $partialAmounts[$section];
                                    $grandTotal['full'] += array_sum($counts['full']);
                                    $fullTotal += $fullAmounts[$section];
                                @endphp
                                <td class="text-center">{{ $total }}</td>
                                <td class="text-center">{{ number_format($totalAmount,2) }}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td><strong>Grand Total</strong></td>
                            <td class="text-center"><strong>{{ $grandTotal['loan'] }}</strong></td>
                            <td class="text-center"><strong>{{ number_format($loanTotal, 2) }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotal['partial'] }}</strong></td>
                            <td class="text-center"><strong>{{ number_format($partialTotal, 2) }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotal['full'] }}</strong></td>
                            <td class="text-center"><strong>{{ number_format($fullTotal, 2) }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotal['loan'] + $grandTotal['partial'] + $grandTotal['full'] }}</strong></td>
                            <td class="text-center"><strong>{{ number_format($loanTotal + $partialTotal + $fullTotal, 2) }}</strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="Weekly" class="tab-content">
                <div class="col-md-12 text-right">
                    <a class="btn" href="{{ route('pdf-outstanding-weekly') }}"><i class="fas fa-file-pdf text-red"></i></a>
                </div>
                <div class="col-md-12">
                    <table class="table table-bordered table-secondary">
                        <thead>
                        <tr>
                            <th class="text-center" rowspan="2">Status</th>
                            <th class="text-center" colspan="3">1-7 Days</th>
                            <th class="text-center" colspan="3">8-15 Days</th>
                            <th class="text-center" colspan="3">16-30 Days</th>
                            <th class="text-center" colspan="3">1-2 Months</th>
                            <th class="text-center" colspan="3">2-6 Months</th>
                            <th class="text-center" colspan="3">Total</th>
                        </tr>
                        <tr>
                            @foreach(['L', 'W', 'F'] as $application)
                                <th class="text-center">{{ $application }}</th>
                            @endforeach
                            @foreach(['L', 'W', 'F'] as $application)
                                <th class="text-center">{{ $application }}</th>
                            @endforeach
                            @foreach(['L', 'W', 'F'] as $application)
                                <th class="text-center">{{ $application }}</th>
                            @endforeach
                            @foreach(['L', 'W', 'F'] as $application)
                                <th class="text-center">{{ $application }}</th>
                            @endforeach
                            @foreach(['L', 'W', 'F'] as $application)
                                <th class="text-center">{{ $application }}</th>
                            @endforeach
                            @foreach(['L', 'W', 'F'] as $application)
                                <th class="text-center">{{ $application }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $sections = [
                                'Registration' => [
                                    '1-7 Days' => [
                                        'L' => $loanCounts['Registration']['1-7 Days'] ?? 0,
                                        'W' => $partialCounts['Registration']['1-7 Days'] ?? 0,
                                        'F' => $fullCounts['Registration']['1-7 Days'] ?? 0,
                                    ],
                                    '8-15 Days' => [
                                        'L' => $loanCounts['Registration']['8-15 Days'] ?? 0,
                                        'W' => $partialCounts['Registration']['8-15 Days'] ?? 0,
                                        'F' => $fullCounts['Registration']['8-15 Days'] ?? 0,
                                    ],
                                    '16-30 Days' => [
                                        'L' => $loanCounts['Registration']['16-30 Days'] ?? 0,
                                        'W' => $partialCounts['Registration']['16-30 Days'] ?? 0,
                                        'F' => $fullCounts['Registration']['16-30 Days'] ?? 0,
                                    ],
                                    '1-2 Months' => [
                                        'L' => $loanCounts['Registration']['1-2 Months'] ?? 0,
                                        'W' => $partialCounts['Registration']['1-2 Months'] ?? 0,
                                        'F' => $fullCounts['Registration']['1-2 Months'] ?? 0,
                                    ],
                                    '2-6 Months' => [
                                        'L' => $loanCounts['Registration']['2-6 Months'] ?? 0,
                                        'W' => $partialCounts['Registration']['2-6 Months'] ?? 0,
                                        'F' => $fullCounts['Registration']['2-6 Months'] ?? 0,
                                    ],
                                ],
                                'Ledger' => [
                                    '1-7 Days' => [
                                        'L' => $loanCounts['Ledger']['1-7 Days'] ?? 0,
                                        'W' => $partialCounts['Ledger']['1-7 Days'] ?? 0,
                                        'F' => $fullCounts['Ledger']['1-7 Days'] ?? 0,
                                    ],
                                    '8-15 Days' => [
                                        'L' => $loanCounts['Ledger']['8-15 Days'] ?? 0,
                                        'W' => $partialCounts['Ledger']['8-15 Days'] ?? 0,
                                        'F' => $fullCounts['Ledger']['8-15 Days'] ?? 0,
                                    ],
                                    '16-30 Days' => [
                                        'L' => $loanCounts['Ledger']['16-30 Days'] ?? 0,
                                        'W' => $partialCounts['Ledger']['16-30 Days'] ?? 0,
                                        'F' => $fullCounts['Ledger']['16-30 Days'] ?? 0,
                                    ],
                                    '1-2 Months' => [
                                        'L' => $loanCounts['Ledger']['1-2 Months'] ?? 0,
                                        'W' => $partialCounts['Ledger']['1-2 Months'] ?? 0,
                                        'F' => $fullCounts['Ledger']['1-2 Months'] ?? 0,
                                    ],
                                    '2-6 Months' => [
                                        'L' => $loanCounts['Ledger']['2-6 Months'] ?? 0,
                                        'W' => $partialCounts['Ledger']['2-6 Months'] ?? 0,
                                        'F' => $fullCounts['Ledger']['2-6 Months'] ?? 0,
                                    ],
                                ],
                                'Loan Recovery' => [
                                    '1-7 Days' => [
                                        'L' => $loanCounts['Loan Recovery']['1-7 Days'] ?? 0,
                                        'W' => $partialCounts['Loan Recovery']['1-7 Days'] ?? 0,
                                        'F' => $fullCounts['Loan Recovery']['1-7 Days'] ?? 0,
                                    ],
                                    '8-15 Days' => [
                                        'L' => $loanCounts['Loan Recovery']['8-15 Days'] ?? 0,
                                        'W' => $partialCounts['Loan Recovery']['8-15 Days'] ?? 0,
                                        'F' => $fullCounts['Loan Recovery']['8-15 Days'] ?? 0,
                                    ],
                                    '16-30 Days' => [
                                        'L' => $loanCounts['Loan Recovery']['16-30 Days'] ?? 0,
                                        'W' => $partialCounts['Loan Recovery']['16-30 Days'] ?? 0,
                                        'F' => $fullCounts['Loan Recovery']['16-30 Days'] ?? 0,
                                    ],
                                    '1-2 Months' => [
                                        'L' => $loanCounts['Loan Recovery']['1-2 Months'] ?? 0,
                                        'W' => $partialCounts['Loan Recovery']['1-2 Months'] ?? 0,
                                        'F' => $fullCounts['Loan Recovery']['1-2 Months'] ?? 0,
                                    ],
                                    '2-6 Months' => [
                                        'L' => $loanCounts['Loan Recovery']['2-6 Months'] ?? 0,
                                        'W' => $partialCounts['Loan Recovery']['2-6 Months'] ?? 0,
                                        'F' => $fullCounts['Loan Recovery']['2-6 Months'] ?? 0,
                                    ],
                                ],
                                'Loan Section' => [
                                    '1-7 Days' => [
                                        'L' => $loanCounts['Loan Section']['1-7 Days'] ?? 0,
                                        'W' => $partialCounts['Loan Section']['1-7 Days'] ?? 0,
                                        'F' => $fullCounts['Loan Section']['1-7 Days'] ?? 0,
                                    ],
                                    '8-15 Days' => [
                                        'L' => $loanCounts['Loan Section']['8-15 Days'] ?? 0,
                                        'W' => $partialCounts['Loan Section']['8-15 Days'] ?? 0,
                                        'F' => $fullCounts['Loan Section']['8-15 Days'] ?? 0,
                                    ],
                                    '16-30 Days' => [
                                        'L' => $loanCounts['Loan Section']['16-30 Days'] ?? 0,
                                        'W' => $partialCounts['Loan Section']['16-30 Days'] ?? 0,
                                        'F' => $fullCounts['Loan Section']['16-30 Days'] ?? 0,
                                    ],
                                    '1-2 Months' => [
                                        'L' => $loanCounts['Loan Section']['1-2 Months'] ?? 0,
                                        'W' => $partialCounts['Loan Section']['1-2 Months'] ?? 0,
                                        'F' => $fullCounts['Loan Section']['1-2 Months'] ?? 0,
                                    ],
                                    '2-6 Months' => [
                                        'L' => $loanCounts['Loan Section']['2-6 Months'] ?? 0,
                                        'W' => $partialCounts['Loan Section']['2-6 Months'] ?? 0,
                                        'F' => $fullCounts['Loan Section']['2-6 Months'] ?? 0,
                                    ],
                                ],
                                'Audit' => [
                                    '1-7 Days' => [
                                        'L' => $loanCounts['Audit']['1-7 Days'] ?? 0,
                                        'W' => $partialCounts['Audit']['1-7 Days'] ?? 0,
                                        'F' => $fullCounts['Audit']['1-7 Days'] ?? 0,
                                    ],
                                    '8-15 Days' => [
                                        'L' => $loanCounts['Audit']['8-15 Days'] ?? 0,
                                        'W' => $partialCounts['Audit']['8-15 Days'] ?? 0,
                                        'F' => $fullCounts['Audit']['8-15 Days'] ?? 0,
                                    ],
                                    '16-30 Days' => [
                                        'L' => $loanCounts['Audit']['16-30 Days'] ?? 0,
                                        'W' => $partialCounts['Audit']['16-30 Days'] ?? 0,
                                        'F' => $fullCounts['Audit']['16-30 Days'] ?? 0,
                                    ],
                                    '1-2 Months' => [
                                        'L' => $loanCounts['Audit']['1-2 Months'] ?? 0,
                                        'W' => $partialCounts['Audit']['1-2 Months'] ?? 0,
                                        'F' => $fullCounts['Audit']['1-2 Months'] ?? 0,
                                    ],
                                    '2-6 Months' => [
                                        'L' => $loanCounts['Audit']['2-6 Months'] ?? 0,
                                        'W' => $partialCounts['Audit']['2-6 Months'] ?? 0,
                                        'F' => $fullCounts['Audit']['2-6 Months'] ?? 0,
                                    ],
                                ],
                                'Payment' => [
                                    '1-7 Days' => [
                                        'L' => $loanCounts['Payment']['1-7 Days'] ?? 0,
                                        'W' => $partialCounts['Payment']['1-7 Days'] ?? 0,
                                        'F' => $fullCounts['Payment']['1-7 Days'] ?? 0,
                                    ],
                                    '8-15 Days' => [
                                        'L' => $loanCounts['Payment']['8-15 Days'] ?? 0,
                                        'W' => $partialCounts['Payment']['8-15 Days'] ?? 0,
                                        'F' => $fullCounts['Payment']['8-15 Days'] ?? 0,
                                    ],
                                    '16-30 Days' => [
                                        'L' => $loanCounts['Payment']['16-30 Days'] ?? 0,
                                        'W' => $partialCounts['Payment']['16-30 Days'] ?? 0,
                                        'F' => $fullCounts['Payment']['16-30 Days'] ?? 0,
                                    ],
                                    '1-2 Months' => [
                                        'L' => $loanCounts['Payment']['1-2 Months'] ?? 0,
                                        'W' => $partialCounts['Payment']['1-2 Months'] ?? 0,
                                        'F' => $fullCounts['Payment']['1-2 Months'] ?? 0,
                                    ],
                                    '2-6 Months' => [
                                        'L' => $loanCounts['Payment']['2-6 Months'] ?? 0,
                                        'W' => $partialCounts['Payment']['2-6 Months'] ?? 0,
                                        'F' => $fullCounts['Payment']['2-6 Months'] ?? 0,
                                    ],
                                ],
                                'Account' => [
                                    '1-7 Days' => [
                                        'L' => $loanCounts['Account']['1-7 Days'] ?? 0,
                                        'W' => $partialCounts['Account']['1-7 Days'] ?? 0,
                                        'F' => $fullCounts['Account']['1-7 Days'] ?? 0,
                                    ],
                                    '8-15 Days' => [
                                        'L' => $loanCounts['Account']['8-15 Days'] ?? 0,
                                        'W' => $partialCounts['Account']['8-15 Days'] ?? 0,
                                        'F' => $fullCounts['Account']['8-15 Days'] ?? 0,
                                    ],
                                    '16-30 Days' => [
                                        'L' => $loanCounts['Account']['16-30 Days'] ?? 0,
                                        'W' => $partialCounts['Account']['16-30 Days'] ?? 0,
                                        'F' => $fullCounts['Account']['16-30 Days'] ?? 0,
                                    ],
                                    '1-2 Months' => [
                                        'L' => $loanCounts['Account']['1-2 Months'] ?? 0,
                                        'W' => $partialCounts['Account']['1-2 Months'] ?? 0,
                                        'F' => $fullCounts['Account']['1-2 Months'] ?? 0,
                                    ],
                                    '2-6 Months' => [
                                        'L' => $loanCounts['Account']['2-6 Months'] ?? 0,
                                        'W' => $partialCounts['Account']['2-6 Months'] ?? 0,
                                        'F' => $fullCounts['Account']['2-6 Months'] ?? 0,
                                    ],
                                ],
                                'IT' => [
                                    '1-7 Days' => [
                                        'L' => $loanCounts['IT']['1-7 Days'] ?? 0,
                                        'W' => $partialCounts['IT']['1-7 Days'] ?? 0,
                                        'F' => $fullCounts['IT']['1-7 Days'] ?? 0,
                                    ],
                                    '8-15 Days' => [
                                        'L' => $loanCounts['IT']['8-15 Days'] ?? 0,
                                        'W' => $partialCounts['IT']['8-15 Days'] ?? 0,
                                        'F' => $fullCounts['IT']['8-15 Days'] ?? 0,
                                    ],
                                    '16-30 Days' => [
                                        'L' => $loanCounts['IT']['16-30 Days'] ?? 0,
                                        'W' => $partialCounts['IT']['16-30 Days'] ?? 0,
                                        'F' => $fullCounts['IT']['16-30 Days'] ?? 0,
                                    ],
                                    '1-2 Months' => [
                                        'L' => $loanCounts['IT']['1-2 Months'] ?? 0,
                                        'W' => $partialCounts['IT']['1-2 Months'] ?? 0,
                                        'F' => $fullCounts['IT']['1-2 Months'] ?? 0,
                                    ],
                                    '2-6 Months' => [
                                        'L' => $loanCounts['IT']['2-6 Months'] ?? 0,
                                        'W' => $partialCounts['IT']['2-6 Months'] ?? 0,
                                        'F' => $fullCounts['IT']['2-6 Months'] ?? 0,
                                    ],
                                ],
                                'Other' => [
                                    '1-7 Days' => [
                                        'L' => $loanCounts['Other']['1-7 Days'] ?? 0,
                                        'W' => $partialCounts['Other']['1-7 Days'] ?? 0,
                                        'F' => $fullCounts['Other']['1-7 Days'] ?? 0,
                                    ],
                                    '8-15 Days' => [
                                        'L' => $loanCounts['Other']['8-15 Days'] ?? 0,
                                        'W' => $partialCounts['Other']['8-15 Days'] ?? 0,
                                        'F' => $fullCounts['Other']['8-15 Days'] ?? 0,
                                    ],
                                    '16-30 Days' => [
                                        'L' => $loanCounts['Other']['16-30 Days'] ?? 0,
                                        'W' => $partialCounts['Other']['16-30 Days'] ?? 0,
                                        'F' => $fullCounts['Other']['16-30 Days'] ?? 0,
                                    ],
                                    '1-2 Months' => [
                                        'L' => $loanCounts['Other']['1-2 Months'] ?? 0,
                                        'W' => $partialCounts['Other']['1-2 Months'] ?? 0,
                                        'F' => $fullCounts['Other']['1-2 Months'] ?? 0,
                                    ],
                                    '2-6 Months' => [
                                        'L' => $loanCounts['Other']['2-6 Months'] ?? 0,
                                        'W' => $partialCounts['Other']['2-6 Months'] ?? 0,
                                        'F' => $fullCounts['Other']['2-6 Months'] ?? 0,
                                    ],
                                ],
                            ];
                        @endphp

                        @foreach($sections as $section => $counts)
                            <tr>
                                <td>{{ $section }}</td>
                                {{-- 1-7 Days --}}
                                @foreach(['L', 'W', 'F'] as $application)
                                    <td class="text-center">{{ $counts['1-7 Days'][$application] }}</td>
                                @endforeach
                                {{-- 8-15 Days --}}
                                @foreach(['L', 'W', 'F'] as $application)
                                    <td class="text-center">{{ $counts['8-15 Days'][$application] }}</td>
                                @endforeach
                                {{-- 16-30 Days --}}
                                @foreach(['L', 'W', 'F'] as $application)
                                    <td class="text-center">{{ $counts['16-30 Days'][$application] }}</td>
                                @endforeach
                                {{-- 1-2 Months --}}
                                @foreach(['L', 'W', 'F'] as $application)
                                    <td class="text-center">{{ $counts['1-2 Months'][$application] }}</td>
                                @endforeach
                                {{-- 2-6 Months --}}
                                @foreach(['L', 'W', 'F'] as $application)
                                    <td class="text-center">{{ $counts['2-6 Months'][$application] }}</td>
                                @endforeach
                                {{-- Total --}}
                                @php
                                    $totalL = $counts['1-7 Days']['L'] + $counts['8-15 Days']['L'] + $counts['16-30 Days']['L'] + $counts['1-2 Months']['L'] + $counts['2-6 Months']['L'];
                                    $totalW = $counts['1-7 Days']['W'] + $counts['8-15 Days']['W'] + $counts['16-30 Days']['W'] + $counts['1-2 Months']['W'] + $counts['2-6 Months']['W'];
                                    $totalF = $counts['1-7 Days']['F'] + $counts['8-15 Days']['F'] + $counts['16-30 Days']['F'] + $counts['1-2 Months']['F'] + $counts['2-6 Months']['F'];
                                @endphp
                                <td class="text-center">{{ $totalL }}</td>
                                <td class="text-center">{{ $totalW }}</td>
                                <td class="text-center">{{ $totalF }}</td>
                            </tr>
                        @endforeach

                        <tr>
                            <td><strong>Grand Total</strong></td>
                            @php
                                $grandTotalL1 = 0;
                                $grandTotalW1 = 0;
                                $grandTotalF1 = 0;
                                $grandTotalL2 = 0;
                                $grandTotalW2 = 0;
                                $grandTotalF2 = 0;
                                $grandTotalL3 = 0;
                                $grandTotalW3 = 0;
                                $grandTotalF3 = 0;
                                $grandTotalL4 = 0;
                                $grandTotalW4 = 0;
                                $grandTotalF4 = 0;
                                $grandTotalL5 = 0;
                                $grandTotalW5 = 0;
                                $grandTotalF5 = 0;
                                $grandTotalL = 0;
                                $grandTotalW = 0;
                                $grandTotalF = 0;
                                foreach($sections as $counts) {
                                    $grandTotalL1 += $counts['1-7 Days']['L'];
                                    $grandTotalW1 += $counts['1-7 Days']['W'];
                                    $grandTotalF1 += $counts['1-7 Days']['F'];
                                    $grandTotalL2 += $counts['8-15 Days']['L'];
                                    $grandTotalW2 += $counts['8-15 Days']['W'];
                                    $grandTotalF2 += $counts['8-15 Days']['F'];
                                    $grandTotalL3 += $counts['16-30 Days']['L'];
                                    $grandTotalW3 += $counts['16-30 Days']['W'];
                                    $grandTotalF3 += $counts['16-30 Days']['F'];
                                    $grandTotalL4 += $counts['1-2 Months']['L'];
                                    $grandTotalW4 += $counts['1-2 Months']['W'];
                                    $grandTotalF4 += $counts['1-2 Months']['F'];
                                    $grandTotalL5 += $counts['2-6 Months']['L'];
                                    $grandTotalW5 += $counts['2-6 Months']['W'];
                                    $grandTotalF5 += $counts['2-6 Months']['F'];
                                    $grandTotalL += $counts['1-7 Days']['L'] + $counts['8-15 Days']['L'] + $counts['16-30 Days']['L'] + $counts['1-2 Months']['L'] + $counts['2-6 Months']['L'];
                                    $grandTotalW += $counts['1-7 Days']['W'] + $counts['8-15 Days']['W'] + $counts['16-30 Days']['W'] + $counts['1-2 Months']['W'] + $counts['2-6 Months']['W'];
                                    $grandTotalF += $counts['1-7 Days']['F'] + $counts['8-15 Days']['F'] + $counts['16-30 Days']['F'] + $counts['1-2 Months']['F'] + $counts['2-6 Months']['F'];
                                }
                            @endphp
                            <td class="text-center"><strong>{{ $grandTotalL1 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalW1 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalF1 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalL2 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalW2 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalF2 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalL3 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalW3 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalF3 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalL4 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalW4 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalF4 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalL5 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalW5 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalF5 }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalL }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalW }}</strong></td>
                            <td class="text-center"><strong>{{ $grandTotalF }}</strong></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="{{ asset('/js/tab-index.js') }}"> </script>
@endpush

@push('custom-css')
    <link rel="stylesheet" href="{{ asset('/css/tab-index.css') }}"/>
@endpush
