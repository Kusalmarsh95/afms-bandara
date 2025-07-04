
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Settlement Voucher</title>
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

        table .col_one {
            width: 55%;
        }
        table .col_two {
            text-align: left;
        }

        table tr{
            border: none;
            font-size: 1.1em;
        }

        .ledger, .section {
            border-bottom: 1px solid #AAAAAA;
        }
        .sign, .section{
            margin-top: 100px;
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
    <div class="ledger">
        <div id="details" class="clearfix">
            <div id="topic">
                <h2 class="name"> <u>Loan Direct Settlement</u></h2>
            </div>
        </div>
        <h3>Personal Details</h3>
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="col_one">Registration No : {{ $loan->application_reg_no ?? '-'}}</td>
                <td class="col_two">Regimental No : {{ $loan->membership->regimental_number ?? '-'}}</td>
            </tr>
            <tr>
                <td class="col_one">Rank : {{ $loan->membership->ranks->rank_name ?? '-'}}</td>
                <td class="col_two">Name : {{ $loan->membership->name ?? '-'}}</td>
            </tr>
            <tr>
                <td class="col_one">Regiment : {{ $loan->membership->regiments->regiment_name ?? '-'}}</td>
                <td class="col_two">Unit : {{ $loan->membership->units->unit_name ?? '-'}}</td>
            </tr>
            </tbody>
        </table>
        <h3>Loan Details</h3>
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="col_one">Loan Capital : Rs. {{ number_format($loan->approved_amount ?? 0,2)  }}</td>
                <td class="col_two">Loan Recovered Capital : {{ number_format($loan->loan->total_recovered_capital ?? 0,2)}}</td>
            </tr>
            <tr>
                <td class="col_one">Loan Due Capital : {{ number_format($loan->directSettlement->loan_due_cap ?? 0,2)}}</td>
                <td class="col_two">Loan Recovered Interest : {{ number_format($loan->loan->total_recovered_interest ?? 0,2)}}</td>
            </tr>
            <tr>
                <td class="col_one">No of Installments : {{ $loan->no_of_installments ?? 0}}</td>
                <td class="col_two">Recovered Installments : {{ $loan->loan->no_of_installments_paid ?? 0}}</td>
            </tr>
            <tr>
                <td class="col_one">Arrears Interest : {{ $loan->directSettlement->arrest_interest ?? 0}}</td>
                <td class="col_two">Settlement Amount : {{ number_format($loan->directSettlement->settlement_amount ?? 0,2)}}</td>
            </tr>
            <tr>
                <td class="col_one">Settlement Date : {{ $loan->directSettlement->settlement_date ?? 0}}</td>
            </tr>
            </tbody>
        </table>
        <h3>Ledger Section</h3>
        <div class="sign">
            <table border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td class="blank">................................</td>
                    <td class="blank">................................</td>
                    <td class="blank">................................</td>
                </tr>
                <tr>
                    <td class="appointment">Clerk - Ledger</td>
                    <td class="appointment">IC - Ledger</td>
                    <td class="appointment">OC - Ledger</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <h3>Loan Section</h3>
    <div class="section">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank">................................</td>
                <td class="blank">................................</td>
                <td class="blank">................................</td>
            </tr>
            <tr>
                <td class="appointment">Clerk - Loan</td>
                <td class="appointment">IC - Loan</td>
                <td class="appointment">OC - Loan</td>
            </tr>
            </tbody>
        </table>
    </div>
    <h3>Account Section</h3>
    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank">................................</td>
                <td class="blank">................................</td>
                <td class="blank">................................</td>
            </tr>
            <tr>
                <td class="appointment">Clerk - Account</td>
                <td class="appointment">IC - Account</td>
                <td class="appointment">OC - Account</td>
            </tr>
            </tbody>
        </table>
    </div>
</main>
{{--<a class="btn" href="{{ route('loan-settlement-pdf', ['id' => $loan->id, 'download' => 'pdf']) }}">Download PDF</a>--}}
<div class="footer">
    Page <span class="pageNumber"></span>
    <div>System generated file</div>
</div>
</body>
</html>
