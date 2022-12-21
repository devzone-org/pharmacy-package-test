<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        * {

            font-family: monospace;
        }

        .center {
            margin: auto;
            width: 300px;
            /*padding: 10px;*/
        }


        td {
            font-size: 13px;
        }

        th {

            font-size: 50px;
        }

        table {

            text-align: center;

            margin-left: 60px;
        }


        p {
            font-size: 12px;
            margin: 1px;
        }

        h1 {
            margin: 10px;
            font-size: 50px;
        }

        h2 {

            margin: 5px;
        }

        h5 {
            font-size: 12px;
            margin: 5px;
        }


    </style>
</head>
<body onload="window.print()">
<div class="center">
    <h2 style="text-align: center">{{$print['app_name']}}</h2>
    <h5 style="text-align: center">{{$print['address_1']}}</h5>
    <h5 style="text-align: center">{{$print['address_2']}}</h5>
    @if(!empty(env('RECEIPT_LICENSE_NO')))
        <h5 style="text-align: center">{{$print['license_no']}}</h5>
    @endif
    <h5 style="text-align: center">{{$print['invoice_no']}}</h5>
    <h5 style="text-align: center">{{$print['reprint']}}</h5>

    <p>{{$print['patient_name']}}</p>


    <p>{{$print['father_husband_name']}}</p>
    <p>{{$print['sale_by']}}</p>

    <p>=============================================</p>
    <p>{!! $print['heading'] !!}</p>
    <p>=============================================</p>
    {!!$print['inner']!!}


    <p>---------------------------------------------</p>
    <p style="text-align: right">{!! $print['sub_total'] !!}</p>
    <p style="text-align: right">{!! $print['discount'] !!}</p>
    <p style="text-align: right">{!! $print['gross_total'] !!}</p>
    <p style="text-align: right">{!! $print['refund'] !!}</p>
    <p style="text-align: right">{!! $print['net_total'] !!}</p>

    <p style="text-align: center">--------------------------</p>

    <p style="margin: 3px">{!! $print['note']!!}</p>
    <p style="text-align: center">{!! $print['note2'] !!}</p>


    <p style="margin: 7px;"> {{$print['developer']}}  {{$print['developer_phone']}}</p>
</div>
</body>
</html>

