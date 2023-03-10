@extends('frontend.frontend-page-master')

@section('site-title')
@if($category !='')
{{ $category->name }}
@endif
@endsection

@section('page-title')
@if($category !='')
{{ $category->name }}
@endif
@endsection
@section('page-meta-data')
    {!! render_site_title($category->name) !!}
    <meta name="title" content="{{$category->name}}">
@endsection

@section('inner-title')

@if($category !='')
{{ $category->name }}
@endif

@endsection 
@push('css_or_js')
    <style>
        .category-child {
            margin-right: 20px;
        }
        
        @media(max-width: 500px){
            #services_sub_category_load_wrap .category-child {
                margin-right: 14px;
                margin-bottom: 14px;
            }

            #services_sub_category_load_wrap .row  {
                margin-left: 0;
                margin-right: 0;
            }
        }
    </style>
@endpush
@section('content')
    <section class="category-services-area padding-top-70 padding-bottom-100">
        <div class="container">
            <div class="mb-4">
                <div class="col-lg-12">
                    <h2 class="section-sub-title">{{ sprintf(__('Jasa yang tersedia dalam %s'),$category->name)  }}</h2>
                </div>
              
              <div id="services_sub_category_load_wrap">
                <div class="row">
                @foreach($subcategory_under_category as $sub_cat)
                    <div class=" margin-top-30 category-child">
                        <div class="single-category style-02 wow fadeInUp" data-wow-delay=".2s">
                            <div class="icon category-bg-thumb-format" {!! render_background_image_markup_by_attachment_id($sub_cat->image) !!}></div>
                            <div class="category-contents">
                                <h4 class="category-title"> <a href="{{ route('service.list.subcategory',$sub_cat->slug) }}">{{ $sub_cat->name }}</a> </h4>
                                {{-- <span class="category-para">  {{sprintf(__('%s Service'),$sub_cat->total_service)}} </span> --}}
                            </div>
                        </div>
                    </div>
                @endforeach
               </div>
           <div class="load_more_button_warp"><a href="#" id="load_more_btn" data-total="12">{{__('Load More')}}</a></div>
          </div>
          </div>
            <div class="row row-service">
                <div class="col-lg-12">
                    <h2 class="section-sub-title margin-top-80">{{ sprintf(__('Jasa yang tersedia dalam %s'),$category->name)  }}</h2>
                    @php $current_page_url = URL::current(); @endphp
                    @php $cities = \App\ServiceCity::with('service')->get(); @endphp
                    {{-- {{ dd($city) }} --}}
                    <div class="category-service-search-form margin-top-50">
                        <form method="get" action="{{ $current_page_url }}" id="search_service_list_form">
                        <div class="row">
                            <div class="col-lg-3 col-sm-3">
                               <div class="form-group">
                                   <input type="text" class="search-input form-control" id="search_by_query" placeholder="Ketik minimal 3 huruf untuk cari" name="q" value="{{request()->get('q')}}">
                               </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="single-category-service">
                                    <div class="single-select">
                                        <select id="search_by_city" name="city">
                                            <option value="">{{ __('Pilih kota') }}</option>
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
                                            <option value="">{{ __('Pilih rating bintang') }}</option>
                                            <option value="1" @if(!empty(request()->get('rating')) && request()->get('rating') == 1 ) selected @endif>{{ __('Satu Bintang') }}</option>
                                            <option value="2" @if(!empty(request()->get('rating')) && request()->get('rating') == 2 ) selected @endif>{{ __('Dua Bintang') }}</option>
                                            <option value="3" @if(!empty(request()->get('rating')) && request()->get('rating') == 3 ) selected @endif>{{ __('Tiga Bintang') }}</option>
                                            <option value="4" @if(!empty(request()->get('rating')) && request()->get('rating') == 4 ) selected @endif>{{ __('Empat Bintang') }}</option>
                                            <option value="5" @if(!empty(request()->get('rating')) && request()->get('rating') == 5 ) selected @endif>{{ __('Lima Bintang') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div class="single-category-service flex-category-service">
                                    <div class="single-select">
                                        <select id="search_by_sorting" name="sortby">
                                            <option value="">{{ __('Urutkan dari') }}</option>
                                            <option value="latest_service" @if(!empty(request()->get('sortby')) && request()->get('sortby') == 'latest_service') selected @endif>{{ __('Jasa Terakhir') }}</option>
                                            <option value="lowest_price" @if(!empty(request()->get('sortby')) && request()->get('sortby') == 'lowest_price') selected @endif>{{ __('Termurah') }}</option>
                                            <option value="highest_price" @if(!empty(request()->get('sortby')) && request()->get('sortby') == 'highest_price') selected @endif>{{ __('Termahal') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                    </form>
                    </div>
                </div>
                @if($all_services->count() >= 1)
                    @foreach($all_services as $service)
                        
                        <div class="col-lg-4 col-md-6 col-6 margin-top-30 all-services service-custom">
                            <div class="single-service no-margin wow fadeInUp" data-wow-delay=".2s">
                                <a href="{{ route('service.list.details',$service->slug) }}" class="service-thumb service-bg-thumb-format" {!! render_background_image_markup_by_attachment_id($service->image) !!}>

                                    @if($service->featured == 1)
                                    {{-- <div class="award-icons">
                                        <i class="las la-award"></i>
                                    </div> --}}
                                    @endif
                                    <div class="country_city_location">
                                        {{-- <span class="single_location"> <i class="las la-map-marker-alt"></i> {{ optional($service->serviceCity)->service_city }} </span> --}}
                                        <span class="single_location"> <i class="las la-map-marker-alt"></i> {{ getAreaService($service) }} </span>
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
                                        <a href="{{ route('service.list.book',$service->slug) }}" class="cmn-btn btn-small btn-bg-1 w-100"> {{ __('Pesan') }} </a>
                                        {{-- <a href="{{ route('service.list.details',$service->slug) }}" class="cmn-btn btn-small btn-outline-1 ml-auto"> {{ __('View Details') }} </a> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    @if($all_services->count() >= 9)
                        <div class="col-lg-12">
                            <div class="blog-pagination margin-top-55">
                                <div class="custom-pagination mt-4 mt-lg-5">
                                    {!! $all_services->links() !!}
                                </div>
                            </div>
                        </div>
                    @endif
                @else 
                    <div class="alert alert-warning">{{sprintf(__('No services found in %s'),optional($category)->name)}}</div>
                @endif

            </div>

        </div>
    </section>

@endsection
@section("scripts")
      <script>
      	(function($){
          "use strict";
          
          $(document).on('click','#load_more_btn',function(e){
            e.preventDefault();
            
            let totalNo = $(this).data('total');
            let el = $(this);
            let container = $('#services_sub_category_load_wrap > .row');
            
            $.ajax({
              type: "POST",
              url: "{{route('service.list.load.more.subcategories')}}",
              beforeSend: function(e){
                el.text("{{__('loading...')}}")
              },
              data : {
                _token: "{{csrf_token()}}",
                total: totalNo,
                catId: "{{$category->id}}"
              },
              success: function(data){
                
                 el.text("{{__('Load More')}}");
                if(data.markup === ''){
                  el.hide();
                  container.append("<div class='col-lg-12'><div class='text-center text-warning mt-3'>{{__('no more subcategory found')}}</div></div>"); 
                  return;
                }
                
                $('#load_more_btn').data('total',data.total);
                 
                container.append(data.markup); 
              }
            });
            
          });
          
          
        })(jQuery);
      
      </script>
@endsection
