<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') - {{ config('app.name') }}</title>
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <link href="{{ asset('pharma/css/app.css') }}" rel="stylesheet">
        @livewireStyles
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
        <style>
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            input[type="number"] {
                -moz-appearance: textfield;
            }
        </style>

        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"/>

        @livewireScripts
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>

        <script src="{{ asset('pharma/js/app.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    </head>
    <body class="bg-gray-200">
        <div x-data="{menu:false,dropdown:false,activeIndex:-1}">
            @include('pharmacy::include.header')
            @if(!empty(env('PAYMENT_DUE_MODAL')))
                @include('include.payment-due-modal')
            @endif
            <main>
                @yield('content')
            </main>


        </div>
    </body>
</html>
