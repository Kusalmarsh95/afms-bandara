
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Partial Withdrawal</title>
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
            margin-bottom: 10px;
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
        #topic {
            text-align: center;
            margin-bottom: 15px;
        }

        #details {
            margin-bottom: 20px;
        }

        h2.name {
            font-size: 1.5em;
            font-weight: normal;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .border {
            border: 1px solid black;;
            font-size: 1.1em;
        }
        .center {
            text-align: center;
        }
        .right {
            text-align: right;
        }
        .sign {
            margin-top: 50px;
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
    <div id="details" class="clearfix">
        <div id="topic">
            <h2 class="name"> <u>Partial Withdrawals - Bank Deposit List</u></h2>
        </div>
    </div>
    <table class="border">
        <thead>
        <tr>
            <th class="center border" scope="col">Ser No.</th>
            <th class="center border" scope="col">Reg No</th>
            <th class="center border" scope="col">Name</th>
            <th class="center border" scope="col">Regiment</th>
            <th class="center border" scope="col">Approved Amount</th>
            <th class="center border" scope="col">Loan Arrears</th>
            <th class="center border" scope="col">Withdraw Amount</th>
        </tr>
        </thead>
        <tbody>
        @php
            $i = 0;
        @endphp
        @foreach ($partialWithdrawals as $partialWithdrawal)
            <tr>
                <td class="center border">{{ ++$i }}</td>
                <td class="border">{{ $partialWithdrawal->membership->regimental_number }}</td>
                <td class="border">{{ $partialWithdrawal->membership->ranks->rank_name ?? '--' }} {{ $partialWithdrawal->membership->name }}</td>
                <td class="border">{{ $partialWithdrawal->membership->regiments->regiment_name ?? '--' }}</td>
                <td class="right border">{{ number_format($partialWithdrawal->withdrawal->approved_amount ?? '0', 2) }}</td>
                @if($partialWithdrawal->withdrawal->purpose==1)
                    <td class="right border">{{ number_format(($partialWithdrawal->withdrawal->loan_due_cap + $partialWithdrawal->withdrawal->arrest_interest) ?? '0', 2) }}</td>
                @elseif($partialWithdrawal->withdrawal->purpose==2)
                    <td class="right border">{{ number_format(($partialWithdrawal->withdrawal->suwasahana_amount + $partialWithdrawal->withdrawal->suwasahana_arreas) ?? '0', 2) }}</td>
                @else
                    <td class="right border">0.00</td>
                @endif
                <td class="right border">{{ number_format($partialWithdrawal->withdrawal->total_withdraw_amount ?? '0', 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="center border" colspan="6">Total</td>
            <td class="right border">{{ number_format($total ?? '0', 2) }}</td>
        </tr>
        </tbody>
    </table>

    <h3 class="section"><u>Account Section</u></h3>
    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank-left">....................................</td>
                <td class="blank-center">....................................</td>
                <td class="blank-right">....................................</td>
            </tr>
            <tr>
                <td class="appointment-left">Clerk - Account Section</td>
                <td class="appointment-center">IC - Account Section</td>
                <td class="appointment-right">OC - Account Section</td>
            </tr>
            </tbody>
        </table>
    </div>
    <h3 class="section"><u>Ledger Section</u></h3>
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
    <h3 class="section"><u>Recommendation of CEO</u></h3>
    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank-left"></td>
                <td class="blank-center"></td>
                <td class="blank-right">....................................</td>
            </tr>
            <tr>
                <td class="appointment-left"></td>
                <td class="appointment-center"></td>
                <td class="appointment-right">CEO</td>
            </tr>
            </tbody>
        </table>
    </div>
    <h3 class="section"><u>Approval of Director</u></h3>
    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank-left"></td>
                <td class="blank-center"></td>
                <td class="blank-right">....................................</td>
            </tr>
            <tr>
                <td class="appointment-left"></td>
                <td class="appointment-center"></td>
                <td class="appointment-right">Director-DABF</td>
            </tr>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>
