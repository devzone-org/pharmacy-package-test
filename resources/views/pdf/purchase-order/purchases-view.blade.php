<?php

// Report all errors
error_reporting(E_ALL);
ini_set('display_errors', true);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <style>
        ::after,::before{box-sizing:border-box}  html{font-family:sans-serif;line-height:1.15;-webkit-text-size-adjust:100%;-webkit-tap-highlight-color:transparent}  header{display:block}  body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans","Liberation Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";font-size:1rem;font-weight:400;line-height:1;color:#212529;text-align:left;background-color:#fff}  [tabindex="-1"]:focus:not(:focus-visible){outline:0!important}  h1,h2,h3,h4,h5,h6{margin-top:0;margin-bottom:.5rem}  p{margin-top:0;margin-bottom:1rem}  address{margin-bottom:1rem;font-style:normal;line-height:inherit}  img{vertical-align:middle;border-style:none}  table{border-collapse:collapse}  th{text-align:inherit;text-align:-webkit-match-parent}  select{margin:0;font-family:inherit;font-size:inherit;line-height:inherit}  select{text-transform:none}  [role=button]{cursor:pointer}  select{word-wrap:normal}  [type=button],[type=reset],[type=submit]{-webkit-appearance:button}  [type=button]:not(:disabled),[type=reset]:not(:disabled),[type=submit]:not(:disabled){cursor:pointer}  [type=button]::-moz-focus-inner,[type=reset]::-moz-focus-inner,[type=submit]::-moz-focus-inner{padding:0;border-style:none}  [type=number]::-webkit-inner-spin-button,[type=number]::-webkit-outer-spin-button{height:auto}  [type=search]{outline-offset:-2px;-webkit-appearance:none}  [type=search]::-webkit-search-decoration{-webkit-appearance:none}  ::-webkit-file-upload-button{font:inherit;-webkit-appearance:button}  template{display:none}  [hidden]{display:none!important}  .h1,.h2,.h3,.h4,.h5,.h6,h1,h2,h3,h4,h5,h6{margin-bottom:.5rem;font-weight:500;line-height:1.2}  .h1,h1{font-size:2.5rem}  .h2,h2{font-size:2rem}  .h3,h3{font-size:1.75rem}  .h4,h4{font-size:1.5rem}  .h5,h5{font-size:1.25rem}  .h6,h6{font-size:1rem}  .row{display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;margin-right:-15px;margin-left:-15px}  .col,.col-1,.col-10,.col-11,.col-12,.col-2,.col-3,.col-4,.col-5,.col-6,.col-7,.col-8,.col-9,.col-sm,.col-sm-1,.col-sm-10,.col-sm-11,.col-sm-12,.col-sm-2,.col-sm-3,.col-sm-4,.col-sm-5,.col-sm-6,.col-sm-7,.col-sm-8,.col-sm-9{position:relative;width:100%;padding-right:15px;padding-left:15px}  .col{-ms-flex-preferred-size:0;flex-basis:0;-ms-flex-positive:1;flex-grow:1;max-width:100%}  .col-1{-ms-flex:0 0 8.333333%;flex:0 0 8.333333%;max-width:8.333333%}  .col-2{-ms-flex:0 0 16.666667%;flex:0 0 16.666667%;max-width:16.666667%}  .col-3{-ms-flex:0 0 25%;flex:0 0 25%;max-width:25%}  .col-4{-ms-flex:0 0 33.333333%;flex:0 0 33.333333%;max-width:33.333333%}  .col-5{-ms-flex:0 0 41.666667%;flex:0 0 41.666667%;max-width:41.666667%}  .col-6{-ms-flex:0 0 50%;flex:0 0 50%;max-width:50%}  .col-7{-ms-flex:0 0 58.333333%;flex:0 0 58.333333%;max-width:58.333333%}  .col-8{-ms-flex:0 0 66.666667%;flex:0 0 66.666667%;max-width:66.666667%}  .col-9{-ms-flex:0 0 75%;flex:0 0 75%;max-width:75%}  .col-10{-ms-flex:0 0 83.333333%;flex:0 0 83.333333%;max-width:83.333333%}  .col-11{-ms-flex:0 0 91.666667%;flex:0 0 91.666667%;max-width:91.666667%}  .col-12{-ms-flex:0 0 100%;flex:0 0 100%;max-width:100%}@media (min-width:576px){.col-sm{-ms-flex-preferred-size:0;flex-basis:0;-ms-flex-positive:1;flex-grow:1;max-width:100%}.col-sm-1{-ms-flex:0 0 8.333333%;flex:0 0 8.333333%;max-width:8.333333%}.col-sm-2{-ms-flex:0 0 16.666667%;flex:0 0 16.666667%;max-width:16.666667%}.col-sm-3{-ms-flex:0 0 25%;flex:0 0 25%;max-width:25%}.col-sm-4{-ms-flex:0 0 33.333333%;flex:0 0 33.333333%;max-width:33.333333%}.col-sm-5{-ms-flex:0 0 41.666667%;flex:0 0 41.666667%;max-width:41.666667%}.col-sm-6{-ms-flex:0 0 50%;flex:0 0 50%;max-width:50%}.col-sm-7{-ms-flex:0 0 58.333333%;flex:0 0 58.333333%;max-width:58.333333%}.col-sm-8{-ms-flex:0 0 66.666667%;flex:0 0 66.666667%;max-width:66.666667%}.col-sm-9{-ms-flex:0 0 75%;flex:0 0 75%;max-width:75%}.col-sm-10{-ms-flex:0 0 83.333333%;flex:0 0 83.333333%;max-width:83.333333%}.col-sm-11{-ms-flex:0 0 91.666667%;flex:0 0 91.666667%;max-width:91.666667%}.col-sm-12{-ms-flex:0 0 100%;flex:0 0 100%;max-width:100%}}  .table{width:100%;color:#212529;border-top:1px solid #000;border-bottom:1px solid #000}  .table td,.table th{padding:2px}  .table thead th{vertical-align:bottom;border-bottom:2px solid #dee2e6}  .table tbody+tbody{border-top:2px solid #dee2e6}  .table-sm td,.table-sm th{padding:.3rem}  .table-bordered{border:1px solid #dee2e6}  .table-bordered td,.table-bordered th{border:1px solid #dee2e6}  .table-bordered thead td,.table-bordered thead th{border-bottom-width:2px}  .table-dark,.table-dark>td,.table-dark>th{background-color:#c6c8ca}  .table-dark tbody+tbody,.table-dark td,.table-dark th,.table-dark thead th{border-color:#95999c}  .table .thead-dark th{color:#fff;background-color:#343a40;border-color:#454d55}  .table-dark{color:#fff;background-color:#343a40}  .table-dark td,.table-dark th,.table-dark thead th{border-color:#454d55}  .table-dark.table-bordered{border:0}  @-webkit-keyframes progress-bar-stripes{from{background-position:1rem 0}to{background-position:0 0}}  @keyframes progress-bar-stripes{from{background-position:1rem 0}to{background-position:0 0}}  @-webkit-keyframes spinner-border{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}  @keyframes spinner-border{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}  @-webkit-keyframes spinner-grow{0%{-webkit-transform:scale(0);transform:scale(0)}50%{opacity:1;-webkit-transform:none;transform:none}}  @keyframes spinner-grow{0%{-webkit-transform:scale(0);transform:scale(0)}50%{opacity:1;-webkit-transform:none;transform:none}}  .bg-dark{background-color:#343a40!important}  .bg-white{background-color:#fff!important}  .d-table{display:table!important}  .d-table-row{display:table-row!important}@media (min-width:576px){.d-sm-table{display:table!important}.d-sm-table-row{display:table-row!important}}@media print{.d-print-table{display:table!important}.d-print-table-row{display:table-row!important}}  .float-left{float:left!important}@media (min-width:576px){.float-sm-left{float:left!important}}@supports ((position:-webkit-sticky) or (position:sticky)){.sticky-top{position:-webkit-sticky;position:sticky;top:0;z-index:1020}}  .h-25{height:25%!important}  .h-50{height:50%!important}  .h-75{height:75%!important}  .h-100{height:100%!important}  .m-0{margin:0!important}  .mt-0{margin-top:0!important}  .mr-0{margin-right:0!important}  .mb-0{margin-bottom:0!important}  .ml-0{margin-left:0!important}  .m-1{margin:.25rem!important}  .mt-1{margin-top:.25rem!important}  .mr-1{margin-right:.25rem!important}  .mb-1{margin-bottom:.25rem!important}  .ml-1{margin-left:.25rem!important}  .m-2{margin:.5rem!important}  .mt-2{margin-top:.5rem!important}  .mr-2{margin-right:.5rem!important}  .mb-2{margin-bottom:.5rem!important}  .ml-2{margin-left:.5rem!important}  .m-3{margin:1rem!important}  .mt-3{margin-top:1rem!important}  .mr-3{margin-right:1rem!important}  .mb-3{margin-bottom:1rem!important}  .ml-3{margin-left:1rem!important}  .m-4{margin:1.5rem!important}  .mt-4{margin-top:1.5rem!important}  .mr-4{margin-right:1.5rem!important}  .mb-4{margin-bottom:1.5rem!important}  .ml-4{margin-left:1.5rem!important}  .m-5{margin:3rem!important}  .mt-5{margin-top:3rem!important}  .mr-5{margin-right:3rem!important}  .mb-5{margin-bottom:3rem!important}  .ml-5{margin-left:3rem!important}  .p-0{padding:0!important}  .px-0{padding-right:0!important}  .px-0{padding-left:0!important}  .p-1{padding:.25rem!important}  .px-1{padding-right:.25rem!important}  .px-1{padding-left:.25rem!important}  .p-2{padding:.5rem!important}  .px-2{padding-right:.5rem!important}  .px-2{padding-left:.5rem!important}  .p-3{padding:1rem!important}  .px-3{padding-right:1rem!important}  .px-3{padding-left:1rem!important}  .p-4{padding:1.5rem!important}  .px-4{padding-right:1.5rem!important}  .px-4{padding-left:1.5rem!important}  .p-5{padding:3rem!important}  .px-5{padding-right:3rem!important}  .px-5{padding-left:3rem!important}@media (min-width:576px){.m-sm-0{margin:0!important}.mt-sm-0{margin-top:0!important}.mr-sm-0{margin-right:0!important}.mb-sm-0{margin-bottom:0!important}.ml-sm-0{margin-left:0!important}.m-sm-1{margin:.25rem!important}.mt-sm-1{margin-top:.25rem!important}.mr-sm-1{margin-right:.25rem!important}.mb-sm-1{margin-bottom:.25rem!important}.ml-sm-1{margin-left:.25rem!important}.m-sm-2{margin:.5rem!important}.mt-sm-2{margin-top:.5rem!important}.mr-sm-2{margin-right:.5rem!important}.mb-sm-2{margin-bottom:.5rem!important}.ml-sm-2{margin-left:.5rem!important}.m-sm-3{margin:1rem!important}.mt-sm-3{margin-top:1rem!important}.mr-sm-3{margin-right:1rem!important}.mb-sm-3{margin-bottom:1rem!important}.ml-sm-3{margin-left:1rem!important}.m-sm-4{margin:1.5rem!important}.mt-sm-4{margin-top:1.5rem!important}.mr-sm-4{margin-right:1.5rem!important}.mb-sm-4{margin-bottom:1.5rem!important}.ml-sm-4{margin-left:1.5rem!important}.m-sm-5{margin:3rem!important}.mt-sm-5{margin-top:3rem!important}.mr-sm-5{margin-right:3rem!important}.mb-sm-5{margin-bottom:3rem!important}.ml-sm-5{margin-left:3rem!important}.p-sm-0{padding:0!important}.px-sm-0{padding-right:0!important}.px-sm-0{padding-left:0!important}.p-sm-1{padding:.25rem!important}.px-sm-1{padding-right:.25rem!important}.px-sm-1{padding-left:.25rem!important}.p-sm-2{padding:.5rem!important}.px-sm-2{padding-right:.5rem!important}.px-sm-2{padding-left:.5rem!important}.p-sm-3{padding:1rem!important}.px-sm-3{padding-right:1rem!important}.px-sm-3{padding-left:1rem!important}.p-sm-4{padding:1.5rem!important}.px-sm-4{padding-right:1.5rem!important}.px-sm-4{padding-left:1.5rem!important}.p-sm-5{padding:3rem!important}.px-sm-5{padding-right:3rem!important}.px-sm-5{padding-left:3rem!important}}  .text-left{text-align:left!important}  .text-center{text-align:center!important}@media (min-width:576px){.text-sm-left{text-align:left!important}.text-sm-center{text-align:center!important}}  .font-weight-normal{font-weight:400!important}  .font-weight-bold{font-weight:700!important}  .text-white{color:#fff!important}  .text-dark{color:#343a40!important}  .text-body{color:#212529!important}  .text-white-50{color:rgba(255,255,255,.5)!important}  .text-break{word-break:break-word!important;word-wrap:break-word!important}@media print{*,::after,::before{text-shadow:none!important;box-shadow:none!important}thead{display:table-header-group}img,tr{page-break-inside:avoid}h2,h3,p{orphans:3;widows:3}h2,h3{page-break-after:avoid}@page{size:a3}body{min-width:992px!important}.table{border-collapse:collapse!important}.table td,.table th{background-color:#fff!important}.table-bordered td,.table-bordered th{border:1px solid #dee2e6!important}.table-dark{color:inherit}.table-dark tbody+tbody,.table-dark td,.table-dark th,.table-dark thead th{border-color:#dee2e6}.table .thead-dark th{color:inherit;border-color:#dee2e6}}
    </style>
</head>

<body>

<div class="bg-white">

    @include('include.pdf-header')

    <table class=" table p-0  " style="font-size: 13px ">
        <tbody class="">
        <tr>
            <td class="text-sm font-weight-bold ">
                PO #
            </td>
            <td class=" text-sm">
                {{ $purchase_id }}
            </td>
            <td class="text-sm font-weight-bold">
                Supplier Name
            </td>
            <td class=" text-sm">
                {{ $purchase->supplier_name }}
            </td>
        </tr>

        <tr>

            @php
                if ($purchase->status == 'received'){
                    $received=$purchase_receive->total_receive;
                    $tax=($purchase->advance_tax/100)*$received;
                    $received_after_tax=$received+$tax;
                }else{
                    $received=$details->sum('total_cost');
                    $tax=($purchase->advance_tax/100)*$received;
                    $received_after_tax=$received+$tax;
                }
            @endphp

            <td class=" text-sm font-weight-bold ">
                PO Approved By

            </td>
            <td class=" text-sm">
                @if(!empty($purchase->approved_by))
                    {{ $purchase->approved_by }} <br>
                    {{ date('d M Y h:i A',strtotime($purchase->approved_at)) }}
                @else
                    -
                @endif
            </td>

            <td></td>
            <td></td>
        </tr>

        </tbody>
    </table>
</div>
<div>

    <div class="mt-5 font-weight-bold mb-1" style="text-decoration: underline !important">
        Purchase Order
    </div>

    <table class="table pt-5" style="font-size: 13px ">
        <thead>
        <tr>
            <th>
                #
            </th>
            <th>
                Name
            </th>
            <th>
                Generic
            </th>
            <th>
                Qty
            </th>
            <th>
                Bonus
            </th>
            <th>
                Pieces
            </th>
            <th>
                Total Qty
            </th>
            <th>
                Supplier Cost
            </th>
            <th>
                Discount
            </th>

            <th>
                Total Cost
            </th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        @php
            $bonus = 0 ;
            $qty = 0 ;
        @endphp
        @foreach($details as $key => $m)
            <tr class="text-center">
                <td>
                    {{ $loop->iteration }}
                </td>
                <td>
                    {{ $m->name }}
                </td>
                <td>
                    {{ $m->salt }}
                </td>
                <td>
                    @php
                        if($loose_purchase == 't'){
                           $qty += $m->qty;
                           $bonus += $m->bonus;
                        }
                        else{
                             $qty += $m->qty/$m->packing;
                             $bonus += $m->bonus/$m->packing;
                        }
                    @endphp
                    @if($loose_purchase == 't')
                        {{$m->qty}}
                    @else
                        {{$m->qty/$m->packing}}
                    @endif
                </td>
                <td>
                    @if($loose_purchase == 't')
                        {{$m->bonus}}
                    @else
                        {{$m->bonus/$m->packing}}
                    @endif
                </td>
                <td>
                    {{$m->packing}}
                </td>
                <td>
                    @if($loose_purchase == 't')
                        {{$m->qty + $m->bonus}}
                    @else
                        {{($m->qty) + ($m->bonus)}}
                    @endif
                </td>
                <td>
                    {{ number_format($m->cost_of_price,2) }}
                </td>
                <td>
                    {{ number_format($m->discount,2) }}
                </td>
                <td>
                    {{ number_format($m->total_cost,2) }}
                </td>
            </tr>
        @endforeach
        <tr>
            <th>
            </th>
            <th>
            </th>
            <th>
                Total
            </th>
            <th>
                {{ $qty }}
            </th>
            <th>
                {{$bonus}}
            </th>

            <th>
                {{ number_format($details->sum('packing'),2) }}
            </th>
            <th>
                @if($loose_purchase == 't')
                    {{ number_format($details->sum('qty') + $details->sum('bonus'), 2) }}
                @else
                    {{  number_format($details->sum('qty')  + $details->sum('bonus'),2) }}
                @endif
            </th>

            <th>
                {{ number_format($details->sum('cost_of_price'),2) }}
            </th>
            <th>
                {{ number_format($details->sum('discount'),2) }}
            </th>

            <th>
                {{ number_format($details->sum('total_cost'),2) }}
            </th>

        </tr>
        </tbody>
    </table>

</div>
</div>
</body>
</html>