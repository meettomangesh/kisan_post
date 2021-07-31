@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card mx-4">
            <div class="card-body p-4">


                @if(session('message'))
                <div class="alert alert-info" role="alert">
                    {{ session('message') }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="input-group mb-3 login-logo-container">

                        <img src="{!! URL::asset('images/logo.png') !!}" alt="" class="logo-default img-responsive" />

                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fa fa-user"></i>
                            </span>
                        </div>

                        <input id="mobile_number" name="mobile_number" type="text" class="form-control{{ $errors->has('mobile_number') ? ' is-invalid' : '' }}" required autocomplete="mobile_number" autofocus placeholder="{{ trans('global.login_mobile_number') }}" value="{{ old('mobile_number', null) }}">

                        @if($errors->has('mobile_number'))
                        <div class="invalid-feedback">
                            {{ $errors->first('mobile_number') }}
                        </div>
                        @endif
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                        </div>

                        <input id="password" name="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" required placeholder="{{ trans('global.login_password') }}">

                        @if($errors->has('password'))
                        <div class="invalid-feedback">
                            {{ $errors->first('password') }}
                        </div>
                        @endif
                    </div>

                    <div class="input-group mb-4">
                        <div class="form-check checkbox">
                            <input class="form-check-input" name="remember" type="checkbox" id="remember" style="vertical-align: middle;" />
                            <label class="form-check-label" for="remember" style="vertical-align: middle;">
                                {{ trans('global.remember_me') }}
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary px-4">
                                {{ trans('global.login') }}
                            </button>
                        </div>
                        <div class="col-6 text-right">
                            @if(Route::has('password.request'))
                            <a class="btn btn-link px-0" href="{{ route('password.request') }}">
                                {{ trans('global.forgot_password') }}
                            </a><br>
                            @endif

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection