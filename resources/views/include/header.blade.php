@php
    $a_default = "text-gray-300 hover:bg-gray-700 hover:text-white";
    $a_current = "bg-gray-900 text-white";
@endphp

<nav class="bg-gray-800">
    <div class=" mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <img class="h-8 w-8" src="https://tailwindui.com/img/logos/workflow-mark-indigo-500.svg" alt="Workflow">
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex  space-x-4">
                        @include('pharmacy::include.header-links')
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">

                    <a href="{{ url('welcome') }}"
                       class="{{ Request::segment(1)=='pharmacy' && empty(Request::segment(2))? $a_current : $a_default }}  px-3 py-2 rounded-md text-sm font-medium">Home</a>


                    <!-- Profile dropdown -->
                    <div class="ml-3 relative" @click.away="dropdown=false;">
                        <div>
                            <button @click="dropdown=!dropdown;" type="button"
                                    class="max-w-xs flex items-center text-sm focus:outline-none   lg:p-2 hover:bg-gray-900"
                                    id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <img class="h-10 w-10 rounded-full border-2 border-gray-300 hover:border-white"
                                     src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80"
                                     alt="">
                                <span class="hidden ml-3 text-left text-white  font-medium lg:block">
                                    <span
                                        class="sr-only">Open user menu for </span>
                                    {{ env('APP_NAME') }} <br>
                                    <span class="text-xs">{{ Auth::user()->name }}</span>
                                </span>
                                <svg class="hidden flex-shrink-0 ml-2 h-5 w-5 text-gray-400 lg:block"
                                     x-description="Heroicon name: solid/chevron-down"
                                     xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                     aria-hidden="true">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                        <div x-show="dropdown" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md z-10 shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                            <a href="#" @click="dropdown=false" class="block px-4 py-2 text-sm text-gray-700   " @mouseenter="activeIndex = 0" @mouseleave="activeIndex = -1" :class="{ 'bg-gray-100': activeIndex === 0 }" role="menuitem" tabindex="-1" id="user-menu-item-0">Your Profile</a>
                            <a href="#" @click="dropdown=false" class="block px-4 py-2 text-sm text-gray-700" @mouseenter="activeIndex = 1" @mouseleave="activeIndex = -1" :class="{ 'bg-gray-100': activeIndex === 1 }"  role="menuitem" tabindex="-1" id="user-menu-item-1">Settings</a>
                            <a href="{{ url('logout') }}" @click="dropdown=false" class="block px-4 py-2 text-sm text-gray-700" @mouseenter="activeIndex = 2" @mouseleave="activeIndex = -1" :class="{ 'bg-gray-100': activeIndex === 2 }"  role="menuitem" tabindex="-1" id="user-menu-item-2">Sign out</a>

                        </div>
                    </div>
                </div>
            </div>
            <div class="-mr-2 flex md:hidden">
                <!-- Mobile menu button -->
                <button @click="menu=!menu" type="button" class="bg-gray-800 inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white" aria-controls="mobile-menu" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>

                    <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>

                    <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="md:hidden" id="mobile-menu" x-show="menu">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
        @include('pharmacy::include.header-links-mobile')
        </div>
        <div class="pt-4 pb-3 border-t border-gray-700">
            <div class="flex items-center px-5">
                <div class="flex-shrink-0">
                    <img class="h-10 w-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixqx=7vUgSUO79Y&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                </div>
                <div class="ml-3">
                    <div class="text-base font-medium text-white">Tom Cook</div>
                    <div class="text-sm font-medium text-gray-400">tom@example.com</div>
                </div>
            </div>
            <div class="mt-3 px-2 space-y-1">
                <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700">Your Profile</a>

                <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700">Settings</a>

                <a href="{{ url('logout') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-400 hover:text-white hover:bg-gray-700">Sign out</a>
            </div>
        </div>
    </div>
</nav>

{{--<header class="  shadow-sm">--}}
{{--    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">--}}
{{--        <h1 class="text-lg leading-6 font-semibold text-gray-900">--}}
{{--            SOme info--}}
{{--        </h1>--}}
{{--    </div>--}}
{{--</header>--}}

{{--<!-- This example requires Tailwind CSS v2.0+ -->--}}
{{--<nav class="bg-white border-b border-gray-200 flex" aria-label="Breadcrumb">--}}
{{--    <ol class=" w-full mx-auto px-4 flex space-x-4 sm:px-6 lg:px-8">--}}
{{--        <li class="flex">--}}
{{--            <div class="flex items-center">--}}
{{--                <a href="{{ url('dashboard') }}" class="text-gray-400 hover:text-gray-500">--}}
{{--                    <!-- Heroicon name: solid/home -->--}}
{{--                    <svg class="flex-shrink-0 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">--}}
{{--                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />--}}
{{--                    </svg>--}}
{{--                    <span class="sr-only">Home</span>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </li>--}}

{{--        <li class="flex">--}}
{{--            <div class="flex items-center">--}}
{{--                <svg class="flex-shrink-0 text-white w-6 h-full " viewBox="0 0 24 44" preserveAspectRatio="none" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">--}}
{{--                    <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />--}}
{{--                </svg>--}}
{{--                <a href="{{ url('hospital') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Reception</a>--}}
{{--            </div>--}}
{{--        </li>--}}

{{--        <li class="flex">--}}
{{--            <div class="flex items-center">--}}
{{--                <svg class="flex-shrink-0 w-6 h-full text-white" viewBox="0 0 24 44" preserveAspectRatio="none" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">--}}
{{--                    <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />--}}
{{--                </svg>--}}
{{--                <a href="{{ url('pharmacy') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700" aria-current="page">Pharmacy</a>--}}
{{--            </div>--}}
{{--        </li>--}}

{{--        <li class="flex">--}}
{{--            <div class="flex items-center">--}}
{{--                <svg class="flex-shrink-0 w-6 h-full text-white" viewBox="0 0 24 44" preserveAspectRatio="none" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">--}}
{{--                    <path d="M.293 0l22 22-22 22h1.414l22-22-22-22H.293z" />--}}
{{--                </svg>--}}
{{--                <a href="{{ url('accounts') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700" aria-current="page">Accounts</a>--}}
{{--            </div>--}}
{{--        </li>--}}
{{--    </ol>--}}
{{--</nav>--}}


