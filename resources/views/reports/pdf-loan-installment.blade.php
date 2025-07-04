
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Full Withdrawal</title>
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
            margin-top: 100px;
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
            <h2 class="name"> <u>Full Withdrawals - Bank Deposit List</u></h2>
        </div>
    </div>
    <table class="border">
        <thead>
        <tr>
            <th class="center border" scope="col">Ser No.</th>
            <th class="center border" scope="col">Regimental number</th>
            <th class="center border" scope="col">Name</th>
            <th class="center border" scope="col">Regiment</th>
            <th class="center border" scope="col">Amount</th>
        </tr>
        </thead>
        <tbody>
        @php
            $i = 0;
        @endphp
        @foreach ($fullWithdrawals as $fullWithdrawal)
            <tr>
                <td class="center border">{{ ++$i }}</td>
                <td class="border">{{ $fullWithdrawal->membership->regimental_number }}</td>
                <td class="border">{{ $fullWithdrawal->membership->ranks->rank_name ?? '--' }} {{ $fullWithdrawal->membership->name }}</td>
                <td class="border">{{ $fullWithdrawal->membership->regiments->regiment_name ?? '--' }}</td>
                <td class="right border">{{ number_format($fullWithdrawal->fullWithdrawal->withdrawal_amount ?? '--', 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td class="center border" colspan="4">Total</td>
            <td class="right border">{{ number_format($total, 2) }}</td>
        </tr>
        </tbody>
    </table>

    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank">.....................................</td>
                <td class="blank">.....................................</td>
                <td class="blank">.....................................</td>
            </tr>
            <tr>
                <td class="appointment">Prepared By</td>
                <td class="appointment">Checked By</td>
                <td class="appointment">Approved By</td>
            </tr>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>
