@extends('layout')

@section('content')
    <div class="container-fluid">
        <resource-component :resources="{{$resource}}"></resource-component>
    </div>
@endsection