<a href="{{ url('pharmacy') }}" class="{{ Request::segment(1)=='pharmacy' && empty(Request::segment(2))? $a_current : $a_default }}  px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
<a href="{{ url('pharmacy/master-data') }}" class="{{ Request::segment(1)=='pharmacy' && Request::segment(2) == 'master-data' ? $a_current : $a_default }}  px-3 py-2 rounded-md text-sm font-medium">Master Data</a>
{{--<a href="{{ url('accounts/chart-of-accounts') }}" class="{{ Request::segment(1)=='accounts' && Request::segment(2)=='chart-of-accounts' ? $a_current : $a_default }}  px-3 py-2 rounded-md text-sm font-medium">Chart of Accounts</a>--}}
{{--<a href="{{ url('accounts/journal') }}" class="{{ Request::segment(1)=='accounts' && Request::segment(2)=='journal' ? $a_current : $a_default }}  px-3 py-2 rounded-md text-sm font-medium">General Journal</a>--}}
{{--<a href="{{ url('accounts/reports') }}" class="{{ Request::segment(1)=='accounts' && Request::segment(2)=='reports' ? $a_current : $a_default }}  px-3 py-2 rounded-md text-sm font-medium">Reports</a>--}}