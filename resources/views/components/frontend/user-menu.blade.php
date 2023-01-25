@if(Auth::guard('web')->check())
<div class="login-account ml-3">
    <li>
        <div class="info-bar-item-two">
            <a class="accounts loggedin text-light" href="javascript:void(0)">
                <span class="title"> {{Auth::guard('web')->user()->name}} </span>
            </a>
            <div class="author-thumb">
                @if(!empty(Auth::guard('web')->user()->image))
                    {!! render_image_markup_by_attachment_id(Auth::guard('web')->user()->image) !!}
                @else
                    <img src="{{ asset('assets/frontend/img/static/user_profile.png') }}" alt="No Image">
                @endif
                
            </div>
            <ul class="account-list-item mt-2">
                <li class="list"> 
                    @if(Auth::guard('web')->user()->user_type==0)
                    <a href="{{ route('seller.dashboard')}}"> {{ __('Dashboard') }} </a> 
                    @else 
                    <a href="{{ route('buyer.dashboard')}}"> {{ __('Dashboard') }} </a> 
                    @endif
                </li>
                <li class="list"> <a href="{{ route('seller.logout')}}"> {{ __('Logout') }} </a> </li>
            </ul>
        </div>
    </li>
</div>
@else
    <div class="login-account" style="padding: 0 28px;">
        <a class="accounts text-light" href="javascript:void(0)"> <span class="account">{{ __('Account') }}</span> <i class="las la-user"></i> </a>
        <ul class="account-list-item mt-2">
            {{-- <li class="list text-light"> <a href="{{ route('user.register') }}"> {{ __('Sign Up') }} </a> </li> --}}
            <li class="list text-light"> <a href="{{ route('user.login') }}">{{ __('Sign In') }} </a> </li>
        </ul>
    </div>
@endif


