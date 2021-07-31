@extends('layouts.app')
@section('content')

<div class="email-main-wrapper">
    <div class="email-inner" style="text-align: center;">
        <div class="input-group mb-3 login-logo-container">

            <img src="{!! URL::asset('images/logo.png') !!}" alt="" class="logo-default img-responsive" />

        </div>
        @if($emailVerified == 1)
        <h3>Congratulations!</h3>
        <p>You have successfully verified your email address.</p>
        @elseif($emailVerified == 2)
        <h3></h3>
        <p>Your email address is already verified.</p>
        @else
        <h3></h3>
        <p>Invalid email verification key.</p>
        @endif
    </div>
</div>
@endsection