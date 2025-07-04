
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Outstanding Report</title>
    <style>
        @font-face {
            font-family: SourceSansPro;
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        body {
            font-size: 14px;
        }

        header {
            padding: 5px 0;
            margin-bottom: 5px;
            border-bottom: 1px solid #AAAAAA;
        }

        #logo {
            float: left;
        }

        #logo img {
            height: 80px;
        }

        #abf {
            float: right;
            text-align: right;
        }

        h2.name {
            font-size: 1.5em;
            font-weight: normal;
            margin: 0;
        }
        .text-center{
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-top: 30px;
        }
        table tr{
            font-size: 1.1em;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #555;
        }
        .pageNumber::after {
            content: counter(page);
        }
    </style>
</head>
<body>
<header class="clearfix">
    <div id="logo">
        <img class="image" src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/sri-lanka-army-logo.jpg'))) }}" alt="Army Logo">
    </div>
    <div id="abf">
        <h2 class="name">Directorate of Army Benevolent Fund</h2>
        <div>Army Cantonment, Homagama, Panagoda</div>

    </div>
</header>
<main>
    <h2 class="text-center name"><u>Outstanding Report of Registered Applications</u></h2>
    <div class="col-md-12">
        <table border="1" class="table table-bordered table-secondary">
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
    <h3 id="abf">Date: {{ now()->format('Y-m-d') }}</h3>
</main>
<div class="footer">
    Page <span class="pageNumber"></span>
    <div>--System generated file (AFMS)--</div>
</div>
</body>
</html>
