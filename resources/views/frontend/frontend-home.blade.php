@extends('frontend.frontend-master')
@section('content')
    @include('frontend.partials.pages-portion.dynamic-page-builder-part',['page_post' => $page_details])
@endsection
@push('scripts')
<script>
    $('#category-slide').owlCarousel({
            loop: false,
            autoplay: false,
            margin: 5,
            nav: false,
            // navText: ["<i class='czi-arrow-left'></i>","<i class='czi-arrow-right'></i>"],
            dots: true,
            autoplayHoverPause: true,
            // center: true,
            responsive: {
                //X-Small
                0: {
                    items: 1
                },
                360: {
                    items: 1
                },
                375: {
                    items: 4
                },
                540: {
                    items: 1.1
                },
                //Small
                576: {
                    items: 1
                },
                //Medium
                768: {
                    items: 8
                },
                //Large
                992: {
                    items: 8
                },
                //Extra large
                1200: {
                    items: 10
                },
                //Extra extra large
                1400: {
                    items: 12
                }
            }
        })

    $('#banner-slider-custom').owlCarousel({
            loop: true,
            autoplay: true,
            margin: 15,
            nav: false,
            // navText: ["<i class='czi-arrow-left'></i>","<i class='czi-arrow-right'></i>"],
            dots: true,
            autoplayHoverPause: true,
            // center: true,
            responsive: {
                //X-Small
                0: {
                    items: 1
                },
                360: {
                    items: 1
                },
                375: {
                    items: 1.1
                },
                540: {
                    items: 1.1
                },
                //Small
                576: {
                    items: 1
                },
                //Medium
                768: {
                    items: 1.2
                },
                //Large
                992: {
                    items: 1.3
                },
                //Extra large
                1200: {
                    items: 1.4
                },
                //Extra extra large
                1400: {
                    items: 1.5
                }
            }
        })
</script>
@endpush