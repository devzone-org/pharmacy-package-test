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

    <p>---------------------------------------------</p>
    <p>{{$print['pos_id']}}</p>
    <p>{{$print['ntn_no']}}</p>
    <p>{{$print['strn_no']}}</p>
    <p>{{$print['patient_name']}}</p>
    <p>{{$print['father_husband_name']}}</p>
    <p>{{$print['sale_by']}}</p>

    @if(!empty($print['inner']))
        <br>
        <p>=============== TAXABLE ITEMS ===============</p>
        <br>
        <p>{!! $print['heading'] !!}</p>
        <p>---------------------------------------------</p>
        <p>{!!$print['inner']!!}</p>
        <p>---------------------------------------------</p>
        <p style="text-align: right">{!! $print['taxable_amount'] !!}</p>
        <p style="text-align: right">{!! $print['sales_tax'] !!}</p>
        <p style="text-align: right">{!! $print['taxable_total'] !!}</p>
    @endif

    @if(!empty($print['inner2']))
        <br>
        <p>============ EXEMPT / ZERO RATED ============</p>
        <br>
        <p>{!! $print['heading2'] !!}</p>
        <p>---------------------------------------------</p>
        <p>{!!$print['inner2']!!}</p>
        <p>---------------------------------------------</p>
        <p style="text-align: right">{!! $print['exempt_total'] !!}</p>
    @endif

    @if(!empty($print['inner']) || !empty($print['inner2']))
        <br>
        <p>==============================================</p>
        <p style="text-align: center;">PAYMENT SUMMARY</p>
        <p>==============================================</p>
        @if(!empty($print['inner']))
            <p style="text-align: right">{!! $print['taxable_total'] !!}</p>
        @endif

        @if(!empty($print['inner2']))
            <p style="text-align: right">{!! $print['exempt_total'] !!}</p>
        @endif

        @if(!empty($print['inner']) && !empty($print['inner2']))
            <p style="text-align: right">{!! $print['sub_total'] !!}</p>
        @endif

        <p style="text-align: right">{!! $print['discount'] !!}</p>
        <p style="text-align: right">{!! $print['gross_total'] !!}</p>
        <p style="text-align: right">{!! $print['refund'] !!}</p>

        <p>---------------------------------------------</p>
        <p style="text-align: right">{!! $print['net_total'] !!}</p>
        <p>---------------------------------------------</p>
        @if(!empty($print['inner']))
            <p style="text-align: right">{!! $print['total_sales_tax_collected'] !!}</p>
            <p>---------------------------------------------</p>
        @endif
    @endif
    <p style="margin: 3px; text-align: center;">
        {!! $print['note']!!}
    </p>
    <p style="margin: 3px; text-align: center;">
        {!! $print['note2'] !!} {{$print['developer']}} <br> {{$print['developer_phone']}}
    </p>
</div>
</body>
</html>

