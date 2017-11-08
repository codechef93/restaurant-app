@extends('layouts.empty_layout')

@section("content")
<div class="container pt-3 entry_form">
    <div class="d-flex justify-content-center pt-5">
        <div class="col-sm-12 col-md-6 col-lg-6 col-lg-6">
            <img src="/images/logo_word_mark.png" class="d-block mb-4 entry_logo" alt="appsthing">
            <label class="text-display-0 mb-3">Lockout Password Generator</label>
            <div class="mb-2">
                <p>Copy the code and paste in the update password column against the user in the database: </p>
                <code> {{ $hashed_password }}</code>
            </div>

            <div class="mb-2">
                <p>Your password will be: </p>
                <code>{{ $password }}</code>
            </div>
        </div>
    </div>
</div>
@endsection