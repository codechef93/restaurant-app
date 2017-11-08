@extends('layouts.layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kitchen_view.css') }}">
    <link rel="stylesheet" href="{{ asset('css/labels.css') }}">
@endpush

@section('content')
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Edit area {{ $restoarea->name }}</h3>
                            </div>
                            
                            <div class="col-4 text-right">
                                <a href="/tables" class="btn btn-sm btn-primary"> Back </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form action="/tables/{{ $restoarea->id }}/update" method="POST" enctype="multipart/form-data">
                            @csrf
                            @include('partials.fields',['fiedls'=>$fields])
                            <input type="hidden" name="store_slack" value="{{ $store_slack }}" />
                            <button type="submit" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div> 
    </div>
@endsection
