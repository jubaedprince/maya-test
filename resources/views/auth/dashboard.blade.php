@extends('layouts.app')


@section('content')
    <h1><a href="/">Go to Login Page</a></h1>

    <h1>You are logged in</h1>
    <p>ID: {{$response['id']}}</p>

    @if(isset($response['email']['address']))
        <p> Email Address: {{$response['email']['address'] }}</p>
    @endif

    @if(isset($response['phone']['number'])) <br>
        <p> Phone Number: {{$response['phone']['number'] }}</p>
    @endif

    @if(isset($response['facebook']['name'])) <br>
        <p> Facebook Name: {{$response['facebook']['name'] }}</p>
    @endif
@endsection
