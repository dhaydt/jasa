
@extends('frontend.frontend-page-master')

@section('page-meta-data')
    <title>{{ __('Seller all Services')  }}</title>
@endsection

@section('page-title')
    <?php 
    $page_info = request()->url();
    $str = explode("/",request()->url());
    $page_info = $str[count($str)-2];
    ?>  
    {{ ucfirst($page_info) }}
@endsection 

@section('inner-title')
{{ __('Seller all Services')  }}
@endsection 

@section('content')

    <!-- Category Service area starts -->
    <section class="category-services-area padding-top-100 padding-bottom-100">
        <div class="container">
            <div class="row">
                @if(!empty($categories))
                <div class="col-lg-3 col-sm-6">
                    <div class="single-category-service">
                        <div class="single-select">
                            <select id="search_by_category">
                                <option>{{ __('Pilih kategori') }}</option>
                                @foreach($categories as $category)
                                <option value="{{  $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-lg-3 col-sm-6">
                    <div class="single-category-service">
                        <div class="single-select">
                            <select id="search_by_subcategory">
                                <option>{{ __('Pilih Subkategori') }}</option>
                                @foreach($sub_categories as $subcategory)
                                <option value="{{  $subcategory->id }}">{{ $subcategory->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="single-category-service">
                        <div class="single-select">
                            <select id="search_by_rating">
                                <option>{{ __('Pilih bintang') }}</option>
                                <option value="1">{{ __('Satu Bintang') }}</option>
                                <option value="2">{{ __('Dua Bintang') }}</option>
                                <option value="3">{{ __('Tiga Bintang') }}</option>
                                <option value="4">{{ __('Empat Bintang') }}</option>
                                <option value="5">{{ __('Lima Bintang') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="single-category-service flex-category-service">
                        <div class="single-select">
                            <span class="select-sort">{{ __('Urutkan dari:') }}</span>
                            <select id="search_by_sorting">
                                <option>{{ __('Sort By') }}</option>
                                <option value="latest_service">{{ __('Jasa Terakhir') }}</option>
                                <option value="price_lowest">{{ __('Termurah') }}</option>
                                <option value="price_highest">{{ __('Termahal') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row margin-top-20" id="search_result">

                @if(!empty($single_service))
                <input type="hidden" name="seller_id" id="seller_id" value="{{ $single_service->seller_id }}">
                @endif

                @if(!empty($all_services))
                    @foreach($all_services as $service)
                        
                        <div class="col-lg-4 col-md-6 margin-top-30 all-services">
                            <div class="single-service no-margin wow fadeInUp" data-wow-delay=".2s">
                                <a href="{{ route('service.list.details',$service->slug) }}" class="service-thumb">
                                    {!! render_image_markup_by_attachment_id($service->image) !!}
                                    @if($service->featured == 1)
                                    {{-- <div class="award-icons">
                                        <i class="las la-award"></i>
                                    </div> --}}
                                    @endif
                                    <div class="country_city_location">
                                        <span class="single_location"> <i class="las la-map-marker-alt"></i> {{ optional($service->serviceCity)->service_city }}, {{ optional(optional($service->serviceCity)->countryy)->country }} </span>
                                    </div>
                                </a>
                                <div class="services-contents">
                                    <h5 class="common-title"> <a href="{{ route('service.list.details',$service->slug) }}"> {{ $service->title }} </a> </h5>
                                    <div class="service-price flex-column align-items-start">
                                        <span class="starting"> {{ __('Starting at') }} </span>
                                        <span class="prices"> {{ amount_with_currency_symbol($service->price) }} </span>
                                    </div>

                                    <ul class="author-tag flex-column align-items-start">
                                        <li class="tag-list">
                                            <a href="{{ route('about.seller.profile',optional($service->seller)->username) }}">
                                                <div class="authors">
                                                    <div class="thumb">
                                                        {!! render_image_markup_by_attachment_id(optional($service->seller)->image) !!}
                                                        <span class="notification-dot"></span>
                                                    </div>
                                                    <span class="author-title"> {{ optional($service->seller)->name }} </span>
                                                </div>
                                            </a>
                                        </li>
                                        @if($service->reviews->count() >= 1)
                                        <li class="tag-list">
                                            <a href="javascript:void(0)">
                                                <span class="icon">{{ __('Rating:') }}</span>
                                                <span class="reviews"> 
                                                    {{ round(optional($service->reviews)->avg('rating'),1) }}
                                                    ({{ optional($service->reviews)->count() }})
                                                </span>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                    
                                    {{-- <p class="common-para"> {{ Str::limit(strip_tags($service->description,100)) }} </p> --}}

                                    <div class="btn-wrapper d-flex flex-wrap">
                                        <a href="{{ route('service.list.book',$service->slug) }}" class="cmn-btn btn-small btn-bg-1 w-100"> {{ __('Pesan') }} </a>
                                        {{-- <a href="{{ route('service.list.details',$service->slug) }}" class="cmn-btn btn-small btn-outline-1 ml-auto"> {{ __('View Details') }} </a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="col-lg-12">
                        <div class="blog-pagination margin-top-55">
                            <div class="custom-pagination mt-4 mt-lg-5">
                                {!! $all_services->links() !!}
                            </div>
                        </div>
                    </div>

                @endif
            </div>
        </div>
    </section>
    <!-- Category Service area end -->

@endsection

@section('scripts')
    <script>
        (function($){
            "use strict";

            $(document).ready(function(){

                $(document).on('change','#search_by_category',function(e){
                    e.preventDefault();
                    let category_id = $(this).val();
                    let seller_id = $('#seller_id').val();

                    $.ajax({
                        url:"{{ route('service.search.category') }}",
                        method:"get",
                        data:{
                            category_id:category_id,
                            seller_id:seller_id,
                        },
                        success:function(res){
                            if (res.status == 'success') {
                                $('#search_result').html(res.result);
                            }
                        }
                    });
                })

                $(document).on('change','#search_by_subcategory',function(e){
                    e.preventDefault();
                    let subcategory_id = $(this).val();
                    let seller_id = $('#seller_id').val();

                    $.ajax({
                        url:"{{ route('service.search.subcategory') }}",
                        method:"get",
                        data:{
                            subcategory_id:subcategory_id,
                            seller_id:seller_id,
                        },
                        success:function(res){
                            if (res.status == 'success') {
                                $('#search_result').html(res.result);
                            }
                        }
                    });
                })

                $(document).on('change','#search_by_rating',function(e){
                    e.preventDefault();
                    let rating = $(this).val();
                    let seller_id = $('#seller_id').val();

                    $.ajax({
                        url:"{{ route('service.search.rating') }}",
                        method:"get",
                        data:{
                            rating:rating,
                            seller_id:seller_id,
                        },
                        success:function(res){
                            if (res.status == 'success') {
                                $('#search_result').html(res.result);
                            }
                        }
                    });
                })

                $(document).on('change','#search_by_sorting',function(e){
                    e.preventDefault();
                    let sorting = $(this).val();
                    let seller_id = $('#seller_id').val();

                    $.ajax({
                        url:"{{ route('service.search.sorting') }}",
                        method:"get",
                        data:{
                            sorting:sorting,
                            seller_id:seller_id,
                        },
                        success:function(res){
                            if (res.status == 'success') {
                                $('#search_result').html(res.result);
                            }
                        }
                    });
                })
                
                $(document).on('change','#search_by_city',function(e){
                    console.log('work');
                    e.preventDefault();
                    let city = $(this).val();
                    let seller_id = $('#seller_id').val();

                    $.ajax({
                        url:"{{ route('service.search.city') }}",
                        method:"get",
                        data:{
                            city:city,
                            seller_id:seller_id,
                        },
                        success:function(res){
                            if (res.status == 'success') {
                                $('#search_result').html(res.result);
                            }
                        }
                    });
                })

            });
        })(jQuery);
    </script>
@endsection
