
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Full Withdrawal Voucher</title>
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

        .blank-left, .appointment-left {
            text-align: left;
        }
        .blank-center, .appointment-center {
            text-align: center;
        }
        .blank-right {
            text-align: right;
        }
        .appointment-right {
            padding-right: 20px;
            text-align: right;
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
            width: 20%;
        }
        table .col_two {
            width: 25%;
            text-align: left;
        }
        table .col_three {
            width: 20%;
        }
        table .col_four {
            text-align: left;
        }

        table tr{
            border: none;
            font-size: 1.1em;
        }

        .section {
            border-top: 1px solid #AAAAAA;
        }
        .sign {
            margin-top: 30px;
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
        <h2 class="name">Full Withdrawal Voucher</h2>
    </div>
</header>
<main>
    <div class="withdrawal">
        <h3><u>Personal Details</u></h3>
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="col_one">Registration No  </td>
                <td class="col_two">: {{ $fullWithdrawal->application_reg_no ?? '-'}}</td>
                <td class="col_three">Regimental No</td>
                <td class="col_four">: {{ $fullWithdrawal->membership->regimental_number ?? '-'}}</td>
            </tr>
            <tr>
                <td class="col_one">Rank</td>
                <td class="col_two">: {{ $fullWithdrawal->membership->ranks->rank_name ?? '-'}}</td>
                <td class="col_three">Name</td>
                <td class="col_four">: {{ $fullWithdrawal->membership->name ?? '-'}}</td>
            </tr>
            <tr>
                <td class="col_one">Regiment</td>
                <td class="col_two">: {{ $fullWithdrawal->membership->regiments->regiment_name ?? '-'}}</td>
                <td class="col_three">Unit</td>
                <td class="col_four">: {{ $fullWithdrawal->membership->units->unit_name ?? '-'}}</td>
            </tr>
            <tr>
                <td class="col_one">Suwasahana Amount</td>
                <td class="col_two">: Rs. {{ number_format($fullWithdrawal->fullWithdrawal->suwasahana_amount ?? 0,2)}}</td>
                <td class="col_three">10 Month Loan</td>
                <td class="col_four">: {{ $fullWithdrawal->membership->loan10month == 1 ? 'Yes' : 'No'}}</td>
            </tr>
            </tbody>
        </table>
        <h3><u>Account Details</u></h3>
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="col_one">Account No</td>
                <td class="col_two">: {{ $fullWithdrawal->fullWithdrawal->account_no ?? '-'}}</td>
            </tr>
            <tr>
                <td class="col_one">Bank</td>
                <td class="col_one">: {{ $fullWithdrawal->fullWithdrawal->bank_name ?? '-'}}</td>
                <td class="col_three">Branch</td>
                <td class="col_four">: {{ $fullWithdrawal->fullWithdrawal->branch_name ?? '-'}}</td>
            </tr>
            </tbody>
        </table>
        @if ($fullWithdrawal->membership->member_status_id == 3)
            <h3><u>Nominee Details</u></h3>
            <table border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td class="col_one">KIA Reported Date</td>
                    <td class="col_two">: {{ $fullWithdrawal->fullWithdrawal->become_kia ?? '-'}}</td>
                </tr>
                </tbody>
            </table>
            <table border="1" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td class="col">Name</td>
                    <td class="col">Relationship</td>
                    <td class="col">Account No</td>
                    <td class="col">Bank</td>
                    <td class="col">Amount</td>
                </tr>
                @foreach ($fullWithdrawal->membership->nominees as $nominee)
                    <tr>
                        <td>{{ $nominee->name ?? '-' }}</td>
                        <td>{{ $nominee->relationship ? $nominee->relationship->relationship_name : '-' }}</td>
                        <td>{{ $nominee->details->account_number ?? '-' }}</td>
                        <td>{{ $nominee->details->bank_name ?? '-' }}</td>
                        <td>Rs. {{ number_format($nominee->details->paid_amount ?? 0,2)}} ({{ $nominee->percentage ?? '-' }}%)</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
        <h3><u>Loan Details</u></h3>
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="col_one">Loan Due Capital</td>
                <td class="col_two">: Rs. {{ number_format($fullWithdrawal->fullWithdrawal->loan_due_cap ?? 0,2)}}</td>
                <td class="col_three">Arrears Interest</td>
                <td class="col_four">: Rs. {{ number_format($fullWithdrawal->fullWithdrawal->arrest_interest ?? 0,2)}}</td>
            </tr>
            </tbody>
        </table>
        <h3><u>Withdrawal Details</u></h3>
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="col_one">Fund Balance</td>
                <td class="col_two">: Rs. {{ number_format($fullWithdrawal->fullWithdrawal->fund_balance ?? 0,2)}}</td>
                <td class="col_three">Other Deductions</td>
                <td class="col_four">: Rs. {{ number_format($fullWithdrawal->fullWithdrawal->other_deduction ?? 0,2)}}</td>
            </tr>
            <tr>
                <td class="col_one">Requested Amount</td>
                <td class="col_two">: Rs. {{ number_format($fullWithdrawal->fullWithdrawal->requested_amount ?? 0,2)}}</td>
                <td class="col_three">Approved Amount</td>
                <td class="col_four">: Rs. {{ number_format($fullWithdrawal->approvedAmount ?? 0,2)}}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <h3 class="section">Loan Recovery</h3>
    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank-left">....................................</td>
                <td class="blank-center">....................................</td>
                <td class="blank-right">....................................</td>
            </tr>
            <tr>
                <td class="appointment-left">Clerk - Loan Recovery</td>
                <td class="appointment-center">Clerk - Suwasahana</td>
                <td class="appointment-right">Clerk - Distress</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank-left"></td>
                <td class="blank-center">......................................................</td>
                <td class="blank-right">.......................................................</td>
            </tr>
            <tr>
                <td class="appointment-left"></td>
                <td class="appointment-center">IC - Loan Recovery/Suwasahana/Distress</td>
                <td class="appointment-right">OC - Loan Recovery/Suwasahana/Distress</td>
            </tr>
            </tbody>
        </table>
    </div>
    <h3 class="section">Ledger Section</h3>
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
    <h3 class="section">Audit Section</h3>
    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank-left">....................................</td>
                <td class="blank-center">....................................</td>
                <td class="blank-right">....................................</td>
            </tr>
            <tr>
                <td class="appointment-left">Clerk - Audit</td>
                <td class="appointment-center">IC - Audit</td>
                <td class="appointment-right">OC - Audit</td>
            </tr>
            </tbody>
        </table>
    </div>
    <h3 class="section">Account Section</h3>
    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank-left">....................................</td>
                <td class="blank-center">....................................</td>
                <td class="blank-right">....................................</td>
            </tr>
            <tr>
                <td class="appointment-left">Clerk - Account</td>
                <td class="appointment-center">IC - Account</td>
                <td class="appointment-right">OC - Account</td>
            </tr>
            </tbody>
        </table>
    </div>
    <h3 class="section">CEO Recommended/Not Recommended</h3>
    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank-left">....................................</td>
                <td class="blank"></td>
                <td class="blank-right">....................................</td>
            </tr>
            <tr>
                <td class="appointment-left">Date</td>
                <td class="appointment"></td>
                <td class="appointment-right">CEO - DABF</td>
            </tr>
            </tbody>
        </table>
    </div>
    <h3 class="section">Director Recommended/Not Recommended</h3>
    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank-left">....................................</td>
                <td class="blank"></td>
                <td class="blank-right">....................................</td>
            </tr>
            <tr>
                <td class="appointment-left">Date</td>
                <td class="appointment"></td>
                <td class="appointment-right">Director - DABF</td>
            </tr>
            </tbody>
        </table>
    </div>
</main>
<div class="footer">
    Page <span class="pageNumber"></span>
    <div>--System generated file (AFMS)--</div>
</div>
</body>
</html>
