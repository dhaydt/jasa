@php
if(request()->is('/')){
$page__id = get_static_option('home_page');
$page_details = App\Page::find($page__id);
$page_post = isset($page_post) && is_null($page_details) ? $page_post : $page_details;
}
@endphp
<style>
    .navbar.navbar-area.navbar-two.nav-absolute {
        background-color: var(--primary-custom);
    }

</style>
<nav class="navbar justify-content-center navbar-area navbar-two nav-absolute navbar-expand-lg p-0">
    <div class="container p-0 container-two nav-container px-4 mx-0" style="background: var(--primary-custom)">
        <div class="responsive-mobile-menu">
            <div class="logo-wrapper">
                <a href="{{ route('homepage') }}" class="logo">
                    {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
                </a>
            </div>

            <div class="onlymobile-device-account-navbar navtwo">
                <x-frontend.user-menu />
            </div>
            <button class="navbar-toggler black-color" type="button" data-toggle="collapse"
                data-target="#bizcoxx_main_menu_navabar_two" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="bizcoxx_main_menu_navabar_two">
            <ul class="navbar-nav text-end" style="text-align: end;">
                <li class="text-light @if (isset($active)) {{ $active == 'home' ? 'current-menu-item' : '' }}" @endif>
                    <a href="{{ route('homepage') }}">Beranda</a>
                </li>
                <li class="text-light @if (isset($active)) {{ $active == 'service-list' ? 'current-menu-item' : '' }}"
                    @endif">
                    <a href="/service-list?cat=&subcat=&child_cat=&rating=&sortby=latest_service">List Jasa Terbaru</a>
                </li>
            </ul>
        </div>

        <div class="nav-right-content">
            <div class="navbar-right-inner">
                <div class="info-bar-item">
                    @if(auth('web')->check() && Auth()->guard('web')->user()->unreadNotifications()->count() > 0)
                    @if(Auth::guard('web')->check() && Auth::guard('web')->user()->user_type==0)
                    <div class="notification-icon icon">
                        @if(Auth::guard('web')->check())
                        <i class="las la-bell"></i>
                        <span class="notification-number style-02">
                            {{ Auth()->user()->unreadNotifications()->where('data->order_message' , 'You have a new
                            order')->count() }}
                        </span>
                        @endif

                        <div class="notification-list-item mt-2">
                            <h5 class="notification-title">{{ __('Notifications') }}</h5>
                            <div class="list">
                                @if(Auth::guard('web')->check() &&
                                Auth::guard('web')->user()->unreadNotifications()->where('data->order_message' , 'You
                                have a new order')->count() >=1)
                                <span>
                                    @foreach(Auth::user()->unreadNotifications->take(5) as $notification)
                                    @if(isset($notification->data['order_id']))
                                    <a class="list-order"
                                        href="{{ route('seller.order.details',$notification->data['order_id']) }}">
                                        <span class="order-icon"> <i class="las la-check-circle"></i> </span>
                                        {{ $notification->data['order_message'] }} #{{ $notification->data['order_id']
                                        }}
                                    </a>
                                    @endif
                                    @endforeach
                                </span>
                                <a class="p-2 text-center d-block" href="{{ route('seller.notification.all') }}">{{
                                    __('View All Notification') }}</a>
                                @else
                                <p class="text-center padding-3">{{ __('No New Notification') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
                <x-frontend.user-menu />
                <div class="user-location accounts text-light ml-4" id="location-div" data-toggle="tooltip" title="">
                    <i class="las la-map-marker" style="font-size: 18px;"></i>
                    <span class="city-name" id="city-name">Indonesia</span>
                </div>
            </div>
        </div>
    </div>
</nav>
@if (\Request::route()->getName() == 'homepage')
<div class="bottom-bar">
    <div class="container container-two">
        <div class="banner-bottom-content custom-search" style="margin-top: 100px;">
            <form action="{$search_route}" class="banner-search-form" method="get">
                <div class="single-input" style="border-radius: 50px;">
                    <input class="form--control" name="home_search" id="home_search" type="text" style="height: 45px;"
                        placeholder="Cari jasa apa?" autocomplete="off">
                    <div class="icon-search">
                        <i class="las la-search"></i>
                    </div>
                    <button type="submit"> Cari </button>
                </div>
            </form>

            <span id="all_search_result"></span>
        </div>
    </div>
</div>
@endif