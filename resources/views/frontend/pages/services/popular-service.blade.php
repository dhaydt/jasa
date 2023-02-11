@extends('frontend.frontend-page-master')

@section('page-meta-data')
  <title> {{ __('Popular Services') }}</title>
@endsection

@section('page-title')
{{ __('Popular Service') }}
@endsection 

@section('inner-title')
{{ __('All Popular Service') }}
@endsection

@section('content')

    <!-- Category Service area starts -->
    <section class="category-services-area padding-top-10 padding-bottom-100">
        <div class="container">
            <div class="row row-service">
                <div class="col-lg-12">
                    @php $current_page_url = URL::current(); @endphp
                    @php $cities = \App\ServiceCity::with('service')->get(); @endphp
                    {{-- {{ dd($city) }} --}}
                    <div class="category-service-search-form margin-top-50">
                        <form method="get" action="{{ $current_page_url }}" id="search_service_list_form">
                        <div class="row justify-content-center">
                            <div class="col-lg-3 col-sm-3">
                               <div class="form-group">
                                   <input type="text" class="search-input form-control" id="search_by_query" placeholder="{{__('write minimum 3 character to search')}}" name="q" value="{{request()->get('q')}}">
                               </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="single-category-service">
                                    <div class="single-select">
                                        <select id="search_by_city" name="city">
                                            <option value="">{{ __('Select City') }}</option>
                                            @foreach ($cities as $city)
                                            @if (count($city['service']) != 0)
                                                <option value="{{ $city['id'] }}" @if(!empty(request()->get('city')) && request()->get('city') == $city['id'] ) selected @endif>{{ $city['service_city'] }}</option>
                                            @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-6">
                                <div class="single-category-service">
                                    <div class="single-select">
                                        <select id="search_by_rating" name="rating">
                                            <option value="">{{ __('Select Rating Star') }}</option>
                                            <option value="1" @if(!empty(request()->get('rating')) && request()->get('rating') == 1 ) selected @endif>{{ __('One Star') }}</option>
                                            <option value="2" @if(!empty(request()->get('rating')) && request()->get('rating') == 2 ) selected @endif>{{ __('Two Star') }}</option>
                                            <option value="3" @if(!empty(request()->get('rating')) && request()->get('rating') == 3 ) selected @endif>{{ __('Three Star') }}</option>
                                            <option value="4" @if(!empty(request()->get('rating')) && request()->get('rating') == 4 ) selected @endif>{{ __('Four Star') }}</option>
                                            <option value="5" @if(!empty(request()->get('rating')) && request()->get('rating') == 5 ) selected @endif>{{ __('Five Star') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="single-category-service flex-category-service">
                                    <div class="single-select">
                                        <select id="search_by_sorting" name="sortby">
                                            <option value="">{{ __('Sort By') }}</option>
                                            <option value="latest_service" @if(!empty(request()->get('sortby')) && request()->get('sortby') == 'latest_service') selected @endif>{{ __('Latest Service') }}</option>
                                            <option value="lowest_price" @if(!empty(request()->get('sortby')) && request()->get('sortby') == 'lowest_price') selected @endif>{{ __('Lowest Price') }}</option>
                                            <option value="highest_price" @if(!empty(request()->get('sortby')) && request()->get('sortby') == 'highest_price') selected @endif>{{ __('Highest Price') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                    </form>
                    </div>
                </div>
            <div class="row px-2">
                @if(!empty($all_popular_service))
                    @foreach($all_popular_service as $service)
                        <div class="col-lg-4 col-md-6 col-6 margin-top-30 all-services">
                            <div class="single-service no-margin wow fadeInUp" data-wow-delay=".2s">
                                <a href="{{ route('service.list.book',$service->slug) }}" class="service-thumb service-bg-thumb-format" {!! render_background_image_markup_by_attachment_id($service->image) !!}>

                                    @if($service->featured == 1)
                                    {{-- <div class="award-icons">
                                        <i class="las la-award"></i>
                                    </div> --}}
                                    @endif
                                    <div class="country_city_location">
                                        <span class="single_location"> <i class="las la-map-marker-alt"></i> {{ optional($service->serviceCity)->service_city }} </span>
                                    </div>
                                </a>
                                <div class="services-contents">
                                    <h5 class="common-title"> <a href="{{ route('service.list.details',$service->slug) }}"> {{ Str::limit($service->title) }} </a> </h5>
                                    <div class="service-price flex-column align-items-start">
                                        <span class="starting"> {{ __('Mulai dari') }} </span>
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
                                                <span class="reviews">
                                                    {!! ratting_star(round(optional($service->reviews)->avg('rating'),1)) !!}
                                                    ({{ optional($service->reviews)->count() }})
                                                </span>
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                    {{-- <p class="common-para"> {{ Str::limit(strip_tags($service->description),100) }} </p> --}}
                                    <div class="btn-wrapper d-flex flex-wrap">
                                        <a href="{{ route('service.list.book',$service->slug) }}" class="cmn-btn btn-small btn-bg-1 w-100"> {{ __('Book Now') }} </a>
                                        {{-- <a href="{{ route('service.list.details',$service->slug) }}" class="cmn-btn btn-small btn-outline-1 ml-auto"> {{ __('View Details') }} </a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($all_popular_service->count() >= 9)
                        <div class="col-lg-12">
                            <div class="blog-pagination margin-top-55">
                                <div class="custom-pagination mt-4 mt-lg-5">
                                    {!! $all_popular_service->links() !!}
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </section>
    <!-- Category Service area end -->

@endsection
