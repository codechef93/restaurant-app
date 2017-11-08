@extends('layouts.layout')

@section("content")
<editappsettingcomponent :date_time_formats ="{{ json_encode($date_time_formats) }}" :date_formats ="{{ json_encode($date_formats) }}" :setting_data="{{ json_encode($setting_data) }}" :timezones="{{ json_encode($timezones) }}" :deactivation_eligible="{{ json_encode($deactivation_eligible) }}" :chost="{{ json_encode($chost) }}" :cip="{{ json_encode($cip) }}"></editappsettingcomponent>
@endsection