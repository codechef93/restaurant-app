@extends('layouts.empty_layout')

@section("content")
<resetpasswordcomponent user_slack="{{ $user_slack }}" password_reset_token="{{ $password_reset_token }}" :company_logo = {{ json_encode($company_logo)}}></resetpasswordcomponent>
@endsection