
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

        table th, td{
            font-size: 1.1em;
            border: 1px solid #AAAAAA;
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
                <h2 class="name"> <u>Closing Balance - {{ $year }}</u></h2>
            </div>
        </div>
        <table class="projectTable">
            <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Regimental Number</th>
                <th class="text-center">Category</th>
                <th class="text-center">Name</th>
                <th class="text-center">Member Status</th>
                <th class="text-center">Regiment</th>
                <th class="text-center">Balance</th>
            </tr>
            </thead>
            <tbody>
            @php
                $i=0;
            @endphp
            @foreach ($contributions as $contribution)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $contribution->membership->regimental_number }}</td>
                    <td>{{ $contribution->membership->category->category_name }}</td>
                    <td>{{ $contribution->membership->ranks->rank_name ?? '-'}} {{ $contribution->membership->name ?? '-'}}</td>
                    <td>{{ $contribution->membership->status->status_name ?? '-'}}</td>
                    <td>{{ $contribution->membership->regiments->regiment_name ?? '-'}}</td>
                    <td class="text-right">{{ number_format($contribution->closing_balance,2) ?? '-'}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</main>
{{--<a class="btn" href="{{ route('loan-settlement-pdf', ['id' => $loan->id, 'download' => 'pdf']) }}">Download PDF</a>--}}

</body>
</html>
