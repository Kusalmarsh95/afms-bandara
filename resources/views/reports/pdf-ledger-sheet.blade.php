
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Ledger Sheet</title>
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
            margin-top: 20px;
        }
        table .col_one {
            width: 12%;
        }
        table .col_two {
            width: 20%;
            text-align: left;
        }
        table .col_three {
            width: 12%;
        }
        table .col_four {
            text-align: left;
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
        <h2 class="name">Ledger Sheet</h2>
    </div>
</header>
<main>
    <div class="col-md-12">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="col_one">Regimental No</td>
                <td class="col_two">: {{ $membership->regimental_number ?? '-'}}</td>
                <td class="col_three">Name</td>
                <td class="col_four">: {{ $membership->ranks->rank_name ?? '-'}} {{ $membership->name ?? '-'}}</td>
            </tr>
            <tr>
                <td class="col_one">Regiment</td>
                <td class="col_two">: {{ $membership->regiments->regiment_name ?? '-'}}</td>
                <td class="col_three">Unit</td>
                <td class="col_four">: {{ $membership->units->unit_name ?? '-'}}</td>
            </tr>
            </tbody>
        </table>
        <table border="1" class="table table-bordered table-secondary">
            <thead>
            <tr>
                <th class="text-center">Year</th>
                <th class="text-center">Jan</th>
                <th class="text-center">Feb</th>
                <th class="text-center">Mar</th>
                <th class="text-center">Apr</th>
                <th class="text-center">May</th>
                <th class="text-center">Jun</th>
                <th class="text-center">Jul</th>
                <th class="text-center">Aug</th>
                <th class="text-center">Sep</th>
                <th class="text-center">Oct</th>
                <th class="text-center">Nov</th>
                <th class="text-center">Dec</th>
                <th class="text-center">Opening Balance</th>
                <th class="text-center">Contribution Total</th>
                <th class="text-center">Interest Total</th>
                <th class="text-center">Withdrawal</th>
                <th class="text-center">Direct Settlement</th>
                <th class="text-center">Closing Balance</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($ledgerData as $data)
                <tr>
                    <td class="text-center">{{ $data['year'] }}</td>
                    <td class="text-center">{{ number_format($data['Jan'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['Feb'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['Mar'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['Apr'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['May'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['Jun'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['Jul'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['Aug'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['Sep'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['Oct'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['Nov'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['Dec'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['opening_balance'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['contribution_amount'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['yearly_interest'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['withdrawal_amount'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['settlement_amount'], 2) }}</td>
                    <td class="text-center">{{ number_format($data['closing_balance'], 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @if($loans->count() > 0)
            <h3 class="section"><u>Loan Details</u></h3>
            <table border="1"  class="table table-bordered">
                <thead>
                <tr>
                    <th>Application No</th>
                    <th>Registered Date</th>
                    <th>Status</th>
                    <th>Approved Amount</th>
                    <th>Recovered Amount</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($loans as $loan)
                    @if($loan->status_id!=3210 & $loan->processing != 2)
                        <tr>
                            <td>{{ $loan->application_reg_no ? : '-' }}</td>
                            <td>{{ $loan->registered_date ? (new DateTime($loan->registered_date))->format('Y-m-d') : '-' }}</td>
                            <td>
                                @if ($loan->loan->settled == 1)
                                    Setteled
                                @elseif($loan->processing == 0)
                                    Recovering
                                @elseif($loan->processing == 1)
                                    Registered
                                @elseif($loan->processing == 2)
                                    Rejected
                                @elseif($loan->processing == 3)
                                    Processing
                                @elseif($loan->processing == 4)
                                    Approved
                                @elseif($loan->processing == 5)
                                    Disburse
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ number_format($loan->approved_amount, 2) ? : '-' }}</td>
                            <td>{{ $loan->loan ? number_format($loan->loan->total_recovered_capital, 2) : '-' }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        @endif
        <h3 class="section"><u>Data Monitoring</u></h3>
        <div class="sign">
            <table border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td class="blank-left">....................................</td>
                    <td class="blank-center">....................................</td>
                    <td class="blank-right">....................................</td>
                </tr>
                <tr>
                    <td class="appointment-left">Clerk - Data Monitoring</td>
                    <td class="appointment-center">IC - Data Monitoring</td>
                    <td class="appointment-right">OC - Data Monitoring</td>
                </tr>
                </tbody>
            </table>
        </div>
        <h3 class="section"><u>Ledger Section</u></h3>
        <p>Fund Balance at 2011-21-31 : ....................................</p>
        <p>80% Deductions : ....................................</p>
        <p>Other Deductions : ....................................</p>
        <p>Fund Balance at {{ now()->format('Y-m-d') }} : ....................................</p>
        <div class="sign">
            <table border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td class="blank-left">....................................</td>
                    <td class="blank-center">....................................</td>
                    <td class="blank-right">....................................</td>
                </tr>
                <tr>
                    <td class="appointment-left">Clerk - Ledger</td>
                    <td class="appointment-center">IC - Ledger</td>
                    <td class="appointment-right">OC - Ledger</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>
<div class="footer">
    Page <span class="pageNumber"></span>
    <div>--System generated file (AFMS)--</div>
</div>
</body>
</html>
