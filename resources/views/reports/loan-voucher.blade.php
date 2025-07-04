
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Loan Voucher</title>
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
            width: 22%;
        }
        table .col_two {
            width: 30%;
            text-align: left;
        }
        table .col_three {
            width: 22%;
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
            margin-top: 40px;
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
        <h2 class="name"> Loan Voucher</h2>
    </div>
</header>
<main>
    <div class="loan">
{{--        <div id="details" class="clearfix">--}}
{{--            <div id="topic">--}}
{{--                <h2 class="name"> <u>Loan Voucher</u></h2>--}}
{{--            </div>--}}
{{--        </div>--}}
        <h3><u>Personal Details</u></h3>
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="col_one">Registration No  </td>
                <td class="col_two">: {{ $loan->application_reg_no ?? '-'}}</td>
                <td class="col_three">Regimental No</td>
                <td class="col_four">: {{ $loan->membership->regimental_number ?? '-'}}</td>
            </tr>
            <tr>
                <td class="col_one">Rank</td>
                <td class="col_two">: {{ $loan->membership->ranks->rank_name ?? '-'}}</td>
                <td class="col_three">Name</td>
                <td class="col_four">: {{ $loan->membership->name ?? '-'}}</td>
            </tr>
            <tr>
                <td class="col_one">Regiment</td>
                <td class="col_two">: {{ $loan->membership->regiments->regiment_name ?? '-'}}</td>
                <td class="col_three">Unit</td>
                <td class="col_four">: {{ $loan->membership->units->unit_name ?? '-'}}</td>
            </tr>
            </tbody>
        </table>
        <h3><u>Account Details</u></h3>
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="col_one">Account No</td>
                <td class="col_two">: {{ $loan->bank_acc_no ?? '-'}}</td>
            </tr>
            <tr>
                <td class="col_one">Bank</td>
                <td class="col_one">: {{ $loan->bank_name ?? '-'}}</td>
                <td class="col_three">Branch</td>
                <td class="col_four">: {{ $loan->bank_branch ?? '-'}}</td>
            </tr>
            </tbody>
        </table>
        <h3><u>Loan Details</u></h3>
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="col_one">Total Salary</td>
                <td class="col_two">: Rs. {{ number_format($loan->total_salary ?? 0,2)  }}</td>
                <td class="col_three">40% of Basic</td>
                <td class="col_four">: Rs. {{ number_format($loan->salary_40 ?? 0,2)}}</td>
            </tr>
            <tr>
                <td class="col_one">Basic Salary</td>
                <td class="col_one">: Rs. {{ number_format($loan->basic_salary ?? 0,2)}}</td>
                <td class="col_three">Total Deductions :</td>
                <td class="col_four">: Rs. {{ number_format($loan->ten_month_loan+$loan->other_loan+
                                                    $loan->festival_advance+$loan->special_advance ?? 0,2)}}</td>
            </tr>
            <tr>
                <td class="col_one">Good Conduct</td>
                <td class="col_one">: Rs. {{ number_format($loan->good_conduct ?? 0,2)}}</td>
                <td class="col_three">10 Month Loan</td>
                <td class="col_four">: Rs. {{ $loan->ten_month_loan ?? 0}}</td>
            </tr>
            <tr>
                <td class="col_one">Incentive</td>
                <td class="col_two">: Rs. {{ number_format($loan->incentive ?? 0,2)}}</td>
                <td class="col_three">Other Loan Deduction</td>
                <td class="col_four">: Rs. {{ number_format($loan->other_loan ?? 0,2)}}</td>
            </tr>
            <tr>
                <td class="col_one">Qualification</td>
                <td class="col_two">: Rs. {{ number_format($loan->qualification ?? 0,2)}}</td>
                <td class="col_three">Festival Advance</td>
                <td class="col_four">: Rs. {{ number_format($loan->festival_advance ?? 0,2)}}</td>
            </tr>
            <tr>
                <td class="col_one">Ration Value</td>
                <td class="col_two">: Rs. {{ number_format($loan->ration ?? 0,2)}}</td>
                <td class="col_three">Special Advance</td>
                <td class="col_four">: Rs. {{ number_format($loan->special_advance ?? 0,2)}}</td>
            </tr>
            <tr>
                <td class="col_one">Fund Balance</td>
                <td class="col_two">: Rs. {{ number_format($loan->fund_balance ?? 0,2)}}</td>
                <td class="col_three">Calculated 85%</td>
                <td class="col_four">: Rs. {{ number_format($loan->allowed_amount_from_fund ?? 0,2)}}</td>
            </tr>
            <tr>
                <td class="col_one">No of Installments</td>
                <td class="col_two">: Rs. {{ number_format($loan->no_of_installments ?? 0,2)}}</td>
                <td class="col_three">Suggested With Basic</td>
                <td class="col_four">: Rs. {{ number_format($loan->suggested_amount ?? 0,2)}}</td>
            </tr>
            <tr>
                <td class="col_one">Requested Amount</td>
                <td class="col_two">: Rs. {{ number_format($loan->total_amount_requested ?? 0,2)}}</td>
                <td class="col_three">Approved Amount</td>
                <td class="col_four">: Rs. {{ number_format($loan->approved_amount ?? 0,2)}}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <h3 class="section">Loan Section</h3>
    <div class="sign">
        <table border="0" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td class="blank-left">....................................</td>
                <td class="blank-center">....................................</td>
                <td class="blank-right">....................................</td>
            </tr>
            <tr>
                <td class="appointment-left">Clerk - Loan</td>
                <td class="appointment-center">IC - Loan</td>
                <td class="appointment-right">OC - Loan</td>
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
{{--<a class="btn" href="{{ route('loan-settlement-pdf', ['id' => $loan->id, 'download' => 'pdf']) }}">Download PDF</a>--}}
<div class="footer">
    Page <span class="pageNumber"></span>
    <div>--System generated file (AFMS)--</div>
</div>
</body>
</html>
