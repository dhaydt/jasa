@extends('frontend.frontend-master')
@section('site-title')
    {{__('Lupa Password')}}
@endsection
@section('content')
<div class="signup-area padding-top-70 padding-bottom-100">
    <div class="container">
        <div class="signup-wrapper">
            <div class="signup-contents">
                <h3 class="signup-title"> {{ __('Lupa Password.')}} </h3>
                <h6 class="text-center">{{ __('Masukan nomor whatsapp anda untuk password baru.') }}</h6>
                
                <x-session-msg/>
                <x-msg.error/>

                <form class="signup-forms" action="{{ route('user.forget.password')}}" method="post">
                    @csrf
                    <div class="single-signup margin-top-30">
                        <label class="signup-label"> {{'Masukan nomor handphone*'}} </label>
                        <input class="form--control" type="number" name="email" placeholder="{{ __('Masukan nomor handphone') }}">
                    </div>
                    <button type="submit">{{ __('Buat password baru') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

