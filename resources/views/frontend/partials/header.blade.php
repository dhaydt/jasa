<!DOCTYPE html>
<html lang="{{get_user_lang()}}" dir="{{get_user_lang_direction()}}">

<head>
    @if(!empty(get_static_option('site_google_analytics')))
    {!! get_static_option('site_google_analytics') !!}
    @endif
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! render_favicon_by_id(get_static_option('site_favicon')) !!}

    @php
    $custom_body_font_get = \App\CustomFontImport::select('status','file','path')->where('status',
    1)->latest()->first();
    $custom_heading_font_get = \App\CustomFontImport::select('status','file','path')->where('status',
    2)->latest()->first();
    @endphp
    @if(!empty($custom_body_font_get) || !empty($custom_heading_font_get))
    <style>
        /*heading font*/
        @font-face {
            font-family: {{ optional($custom_heading_font_get)->file }};
            src: url('{{optional($custom_heading_font_get)->path}}') format('woff');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        /*body font*/
        @font-face {
            font-family: {{ optional($custom_body_font_get)->file }};
            src: url('{{optional($custom_body_font_get)->path}}') format('woff');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        :root {
            --heading-font: '{{optional($custom_heading_font_get)->file}}',
            sans-serif !important;
            --body-font: '{{optional($custom_body_font_get)->file}}',
            sans-serif !important;
        }

    </style>
    @else
    {!! load_google_fonts() !!}
    @endif

    <link rel="stylesheet" href="{{asset('assets/frontend/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/line-awesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/slick.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/nice-select.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/helpers.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/dynamic-style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/jquery.ihavecookies.css')}}">
    <link rel="stylesheet" href="{{asset('assets/frontend/css/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/common/css/toastr.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">


    @if( get_user_lang_direction() === 'rtl')
    <link rel="stylesheet" href="{{asset('assets/common/css/rtl.css')}}">
    @endif

    <link rel="canonical" href="{{canonical_url()}}" />
    <script src="{{asset('assets/common/js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('assets/common/js/jquery-migrate-3.3.2.min.js')}}"></script>

    @php
    $page_post = isset($page_post) ? $page_post : [];
    $page_type = isset($page_type) ? $page_post : [];
    @endphp

    @include('frontend.partials.root-style')
    @yield('style')


    @if(request()->routeIs('homepage'))
    <title>{{get_static_option('site_title')}} - {{get_static_option('site_tag_line')}}</title>

    {!! render_site_meta() !!}

    @elseif( request()->routeIs('frontend.dynamic.page') && $page_type === 'page' )

    {!! render_site_title(optional($page_post)->title ) !!}

    {!! render_site_meta() !!}

    @else
    @yield('page-meta-data')
    @endif
    @if(!empty( get_static_option('site_third_party_tracking_code')))
    {!! get_static_option('site_third_party_tracking_code') !!}
    @endif
    <style>
        .banner-bottom-content.custom-search .banner-search-form .single-input{
            border: 2px solid var(--primary-custom);
        }
        #location-div{
            cursor: pointer;
        }
        .banner-card{
            border-radius: 20px;
            overflow: hidden;
        }
        .banner-bottom-content.custom-search .banner-search-form .single-input button{
            background: var(--primary-custom);
            font-size: 20px;
            width: 100px;
        }
        .btn-wrapper a.cmn-btn.btn-book{
            background-color: var(--main-color-three);
            color: #fff;
        }
        .btn-wrapper a.cmn-btn.btn-book:hover{
            color: var(--main-color-three);
            background-color: unset;
        }
        .badge-custom{
            font-size: 16px;
            text-transform: capitalize;
            border: 2px solid var(--primary-custom);
            color: var(--primary-custom);
            border-radius: 50px;
            padding: 12px 30px;
        }
        .badge-custom.active{
            border: 2px solid var(--success);
            color: var(--success);
        }

        @media(max-width: 500px){
            .banner-slider{
                margin-top: 5px !important;
            }
            .banner-slider .owl-dots{
                margin-top: -20px !important;
            }
            .service-bg-thumb-format{
                height: 125px !important;
            }
            .single-service.style-03 .services-contents{
                padding: 1px 5px !important;
            }
            #category-slide{
                padding-left: 16px !important;
            }
            .services-area .service-card{
                padding: 0 10px 0 0 !important;
                margin-top: 10px !important;
            }
            .section-title-two{
                padding-bottom: 10px;
            }
            .section-title-two h3.title{
                font-size: 18px;
            }
            .section-title-two .section-btn{
                font-size: 14px;
            }
            section.category-area, section.services-area{
                padding-top: 10px !important;
                padding-bottom: 0 !important;
            }
            .category-contents h4.category-title{
                font-size: 14px !important;
            }
            .badge-custom{
                font-size: 14px !important;
            }
            .row-service{
                padding-left:  10px;
            }
            .service-custom{
                padding: 0 10px 0 0;
            }
            .single-service .services-contents{
                padding: 0 10px 0 10px;
            }
            .common-title{
                font-size: 16px;
            }

            .all-services{
                padding: 0 10px 0 0 !important;
            }

            #all_search_result{
                padding: 0 5px;
            }

            .search-service{
                padding: 0 10px 0 0 !important;
            }

            h2.banner-inner-title{
                font-size: 20px !important;
                line-height: 30px !important;
            }
            .service-details-area{
                padding-top: 0 !important;
            }
            .banner-inner-area.section-bg-2{
                padding-bottom: 12px !important;
            }
            .service-details-background-image{
                height: 210px;
                width: 100%;
                background-size: cover;
            }
            .suggest-services{
                padding: 0 10px 0 0;
            }
            .row-suggest{
                padding: 0 10px;
            }
        }
    </style>
</head>


<body>

    @include('frontend.partials.preloader')
    @include('frontend.partials.navbar',$page_post)