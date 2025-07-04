
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
                <td class="text-center"><strong>{{ $grandTotal['full'] }}</strong></td>
                <td class="text-center"><strong>{{ number_format($partialTotal, 2) }}</strong></td>
                <td class="text-center"><strong>{{ $grandTotal['partial'] }}</strong></td>
                <td class="text-center"><strong>{{ number_format($fullTotal, 2) }}</strong></td>
                <td class="text-center"><strong>{{ $grandTotal['loan'] + $grandTotal['partial'] + $grandTotal['full'] }}</strong></td>
                <td class="text-center"><strong>{{ number_format($loanTotal + $partialTotal + $fullTotal, 2) }}</strong></td>
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
