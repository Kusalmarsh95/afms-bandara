
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
    <h2 class="text-center name"><u>Processing {{ $name }} Applications Registered Before {{ $dateRange }}</u></h2>
    <div class="col-md-12">
        <table border="1" class="table table-bordered table-secondary">
            <thead>
            <tr>
                <th class="text-center">No</th>
                <th class="text-center">Application Number</th>
                <th class="text-center">Regimental Number</th>
                <th class="text-center">Name</th>
                <th class="text-center">Status</th>
                <th class="text-center">Registered Date</th>
            </tr>
            </thead>

            @php
                $i=0;
            @endphp
            <tbody>
            @foreach ($applications as $application)
                <tr>
                    <td>{{ ++$i }}</td>
                    <td>{{ $application->application_reg_no }}</td>
                    <td>{{ $application->membership->regimental_number }}</td>
                    <td>{{ $application->membership->ranks->rank_name ?? '-'}} {{ $application->membership->name ?? '-'}}</td>
                    <td>{{ $application->userName ?? '-'}}</td>
                    <td>{{ date('Y-m-d', strtotime($application->registered_date)) ?? '-'}}</td>
                </tr>
            @endforeach
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
