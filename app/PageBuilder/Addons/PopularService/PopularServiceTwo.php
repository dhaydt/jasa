<?php


namespace App\PageBuilder\Addons\PopularService;

use App\Category;
use App\PageBuilder\Fields\ColorPicker;
use App\Service;
use App\PageBuilder\Fields\Slider;
use App\PageBuilder\Fields\Number;
use App\PageBuilder\Fields\Text;
use App\PageBuilder\Traits\LanguageFallbackForPageBuilder;


class PopularServiceTwo extends \App\PageBuilder\PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'home_three/popular_service_2.png';
    }

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();
        $widget_saved_values = $this->get_settings();


        $output .= Text::get([
            'name' => 'title',
            'label' => __('Title'),
            'value' => $widget_saved_values['title'] ?? null,
        ]);
        $output .= Text::get([
            'name' => 'explore_all',
            'label' => __('Explore Text'),
            'value' => $widget_saved_values['explore_all'] ?? null,
        ]);
        $output .= Text::get([
            'name' => 'explore_link',
            'label' => __('Explore Link'),
            'value' => $widget_saved_values['explore_link'] ?? null,
        ]);
        $output .= Number::get([
            'name' => 'items',
            'label' => __('Items'),
            'value' => $widget_saved_values['items'] ?? null,
            'info' => __('enter how many item you want to show in frontend'),
        ]);

        $output .= Slider::get([
            'name' => 'padding_top',
            'label' => __('Padding Top'),
            'value' => $widget_saved_values['padding_top'] ?? 260,
            'max' => 500,
        ]);
        $output .= Slider::get([
            'name' => 'padding_bottom',
            'label' => __('Padding Bottom'),
            'value' => $widget_saved_values['padding_bottom'] ?? 190,
            'max' => 500,
        ]);
        $output .= ColorPicker::get([
            'name' => 'section_bg',
            'label' => __('Background Color'),
            'value' => $widget_saved_values['section_bg'] ?? null,
            'info' => __('select color you want to show in frontend'),
        ]);
        $output .= Text::get([
            'name' => 'book_appointment',
            'label' => __('Book Appoinment Button Text'),
            'value' => $widget_saved_values['book_appointment'] ?? 'Book Now',
        ]);

        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }


    public function frontend_render(): string
    {

        $settings = $this->get_settings();
        $section_title = $settings['title'];
        $explore_text = $settings['explore_all'];
        $explore_link = $settings['explore_link'];
        $explore_link = $settings['explore_link'] ?? route('service.all.popular');

        $items = $settings['items'];

        $padding_top = $settings['padding_top'];
        $padding_bottom = $settings['padding_bottom'];
        $section_bg = $settings['section_bg'];
        // $book_appoinment = $settings['book_appointment'] ?? 'Book Now';
        $book_appoinment = 'Pesan';


        //static text helpers
        $static_text = static_text();

        $services = Service::select('id', 'title', 'image', 'description', 'price', 'slug', 'seller_id', 'featured', 'service_city_id', 'is_service_online')
            ->where(['status' => 1, 'is_service_on' => 1])
            ->when(subscriptionModuleExistsAndEnable('Subscription'), function ($q) {
                $q->whereHas('seller_subscription');
            })
            ->orderBy('view', 'DESC')
            ->take($items)
            ->inRandomOrder()
            ->get();

        $service_markup = '';
        foreach ($services as $service) {

            $image =  render_background_image_markup_by_attachment_id($service->image, '', '', 'thumb');
            $title =  $service->title;
            $route = route('service.list.details', $service->slug);
            $book_now = route('service.list.book', $service->slug);
            $price =  amount_with_currency_symbol($service->price);
            $seller_image =  render_image_markup_by_attachment_id(optional($service->seller)->image, '', '', 'thumb');
            $seller_name =  optional($service->seller)->name;
            if ($service->is_service_online == 1) {
                $service_city =  __('Service');
                $service_country =  __('Online');
            } else {
                $service_city =  optional($service->serviceCity)->service_city;
                $service_country =  optional(optional($service->serviceCity)->countryy)->country;
            }

            //calculate each service rating and count review
            $total_review = optional($service->reviews);
            $total_count = $total_review->count();
            $rating = round($total_review->avg('rating'), 1);
            $seller_profile = route('about.seller.profile', optional($service->seller)->username);

            $rating_and_review = '';
            if ($rating >= 1) {
                $rating_and_review .= '<a href="javascript:void(0)">
                    <span class="reviews">' . ratting_star($rating) . '(' . $total_count . ')' . '</span>
                </a>';
            }
            $featured = '';
            if ($service->featured == 1) {
                $featured .= '';
                // $featured .= '<div class="award-icons">
                // <i class="las la-award"></i>
                // </div>';
            }

            $old_service_markup = '';

            $old_service_markup .= <<<SERVICE
                <div class="col-xl-3 col-lg-4 col-md-6 margin-top-30 wow fadeInUp" data-wow-delay=".2s">
                    <div class="single-service service-two style-03 section-bg-2">
                        <a href="{$route}" class="service-thumb service-bg-thumb-format" {$image}>
                        {$featured}
                        <div class="country_city_location color-three">
                        <span class="single_location"> <i class="las la-map-marker-alt"></i>{$service_city} </span>
                        </div>
                        </a>
                        <div class="services-contents">
                        <h5 class="common-title"> <a href="{$route}">{$title} </a> </h5>
                            <div class="service-price flex-column align-items-start">
                                <span class="category-para text-dark">Harga mulai</span>
                                <span class="prices style-02 text-danger"> {$price} </span>
                            </div>
                            <ul class="author-tag flex-column align-items-start">
                                <li class="tag-list">
                                    <a href="{$seller_profile}">
                                        <div class="authors">
                                            <div class="thumb">
                                            {$seller_image}
                                                <!-- <span class="notification-dot"></span> -->
                                            </div>
                                            <span class="author-title"> {$seller_name} </span>
                                        </div>
                                    </a>
                                </li>
                                <li class="tag-list">
                                    {$rating_and_review}
                                </li>
                            </ul>
                            <div class="service-price-wrapper">
                                <div class="btn-wrapper w-100">
                                    <a href="{$book_now}" class="cmn-btn w-100 btn-book btn-small btn-outline-3"> {$book_appoinment} </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            SERVICE;

            $service_markup .= <<<SERVICE
            <a href="{$route}" class="badge badge-custom mx-2 mb-3">{$title}</a>
            SERVICE;
        }

        $city_markup = '';
        $serv = Service::where('status', 1)->get();
        $cities = [];
        $ids = [];
        $merged = [];
        foreach ($serv as $s) {
            if($s->serviceCity){
                $id_city = $s->serviceCity->id;
                $name = $s->serviceCity->service_city;
                array_push($ids, $id_city);
                array_push($cities, $name);
            }
        }

        foreach(array_unique($cities) as $key => $cit){
            $data = [
                'id' => $ids[$key],
                'name' => $cities[$key]
            ];
            array_push($merged, $data);
        }

        $cit = session()->get('city_id');
        
        foreach ($merged as $city) {
            $cityN = $city['name'];
            if($cit == $city['id']){
                $active = 'active';
            }else{
                $active = '';
                $all = 'active';
            }
            // $setCity = route('set_city', $city['id']);
            $setCity = route('set_city', $city['id']);
            $city_markup .= <<<CITY
            <a href="service-list?cat=&city={$city['id']}&rating=&sortby=" class="badge badge-custom mx-2 mb-3">{$cityN}</a>
            <!-- <a href="{$setCity}" class="badge badge-custom mx-2 mb-3 {$active}">{$cityN}</a> -->
            CITY;
        }

        $cat = Category::with('services')->where('status', 1)->get();
        if($cit == null){
            $cat = Category::with('services')->where('status', 1)->get();
        }else{
            // $cat = Category::with('services')->whereHas('services', function($q)use($cit){
            //     $q->where('service_city_id', $cit);
            // })->get();
            $cat = Category::with('services')->where('status', 1)->get();
            $cat->map(function ($data) use($cit){
                $data['services'] = Service::where(['status' => 1, 'category_id' => $data['id'], 'service_city_id' => $cit])->inRandomOrder()->take(12)->get();
            });
        }

        $category_markup = '';

        foreach ($cat as $ca) {
            $catTitle = $ca->name;
            $servis_markup = '';
            $i = 0;
            foreach ($ca->services as $k => $servis) {
                $servisTitle = $servis->title;
                $servisImage =  render_background_image_markup_by_attachment_id($servis->image, '', '', 'thumb');
                $servisRroute = route('service.list.details', $servis->slug);
                $servisBook_now = route('service.list.book', $servis->slug);
                $servisPrice =  amount_with_currency_symbol($servis->price);
                $servisSeller_image =  render_image_markup_by_attachment_id(optional($servis->seller)->image, '', '', 'thumb');
                $servisSeller_name =  optional($servis->seller)->name;
                if ($servis->is_service_online == 1) {
                    $servisService_city =  __('Service');
                    $servisService_country =  __('Online');
                } else {
                    $servisService_city =  optional($servis->serviceCity)->service_city;
                    $servisService_country =  optional(optional($servis->serviceCity)->countryy)->country;
                }

                //calculate each service rating and count review
                $servisTotal_review = optional($servis->reviews);
                $servisTotal_count = $servisTotal_review->count();
                $servisRating = round($servisTotal_review->avg('rating'), 1);
                $servisSeller_profile = route('about.seller.profile', optional($servis->seller)->username);

                $servisRating_and_review = '';
                if ($servisRating >= 1) {
                    $servisRating_and_review .= '<a href="javascript:void(0)">
                        <span class="reviews">' . ratting_star($servisRating) . '(' . $servisTotal_count . ')' . '</span>
                    </a>';
                }
                $servisFeatured = '';
                if ($servis->featured == 1) {
                    // $servisFeatured .= '<div class="award-icons">
                    // <i class="las la-award"></i>
                    // </div>';
                }

                $servis_markup .= <<<SERVIS
                <div class="col-xl-3 col-lg-4 col-md-6 col-6 margin-top-30 pb-2 wow fadeInUp service-card" data-wow-delay=".2s">
                    <div class="single-service service-two style-03 section-bg-2">
                        <a href="{$servisRroute}" class="service-thumb service-bg-thumb-format" {$servisImage}>
                        {$servisFeatured}
                        <div class="country_city_location color-three">
                        <span class="single_location"> <i class="las la-map-marker-alt"></i>{$servisService_city} </span>
                        </div>
                        </a>
                        <div class="services-contents">
                        <h5 class="common-title"> <a href="{$servisRroute}">{$servisTitle} </a> </h5>
                            <div class="service-price flex-column align-items-start">
                                <span class="category-para text-dark">Harga mulai</span>
                                <span class="prices style-02 text-danger"> {$servisPrice} </span>
                            </div>
                            <ul class="author-tag flex-column align-items-start">
                                <li class="tag-list">
                                    <a href="{$servisSeller_profile}">
                                        <div class="authors">
                                            <div class="thumb">
                                            {$servisSeller_image}
                                                <!-- <span class="notification-dot"></span> -->
                                            </div>
                                            <span class="author-title"> {$servisSeller_name} </span>
                                        </div>
                                    </a>
                                </li>
                                <li class="tag-list">
                                    {$servisRating_and_review}
                                </li>
                            </ul>
                            <div class="service-price-wrapper mb-4">
                                <div class="btn-wrapper w-100">
                                    <a href="{$servisBook_now}" class="cmn-btn w-100 btn-book btn-small btn-outline-3"> {$book_appoinment} </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                SERVIS;
                if (++$i == 4) break;
            }

            $routeCat = route('service.list.category', $ca->slug);

            $category_markup .= <<<CATEGORY
                <section class="services-area"  data-padding-top="100" data-padding-bottom="{$padding_bottom}" style="background-color:{$section_bg}">
                    <div class="container container-two">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="section-title-two">
                                    <h3 class="title">{$catTitle}</h3>
                                    <a href="{$routeCat}" class="section-btn">Telusuri semua</a>
                                </div>
                            </div>
                        </div>
                        <div class="row margin-top-20 pl-2">
                            {$servis_markup}
                        </div>
                    </div>
                </section>

            CATEGORY;
        }

        if($cit == null){
            $all = 'active';
        }else{
            $all= '';
        }

        $routeAll = route('set-city-auto');
        // $default= route('set_default');
        $default= '/service-list?cat=&subcat=&child_cat=&rating=&sortby=latest_service';


        return <<<HTML
    <!-- Popular Service area starts -->
    <section class="services-area"  data-padding-top="{$padding_top}" data-padding-bottom="{$padding_bottom}" style="background-color:{$section_bg}">
        <div class="container container-two">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-two">
                        <h3 class="title">{$section_title}</h3>
                        <a href="{$explore_link}" class="section-btn">{$explore_text}</a>
                    </div>
                </div>
            </div>
            <div class="row margin-top-20 pl-2">
                {$service_markup}
            </div>
        </div>
    </section>
    
    <section class="services-area"  data-padding-top="100" data-padding-bottom="{$padding_bottom}" style="background-color:{$section_bg}">
        <div class="container container-two">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-two">
                        <h3 class="title">Pilihan Kota</h3>
                        <!-- <a href="{$explore_link}" class="section-btn">{$explore_text}</a> -->
                    </div>
                </div>
            </div>
            <div class="row margin-top-20 pl-2">
                <a href="{$default}" target="_blank" class="badge badge-custom mx-2 mb-3 {$all}">Semua Kota</a>
                {$city_markup}
                <!-- <a href="{$routeAll}" class="badge badge-custom mx-2 mb-3">Deteksi Kota</a> -->
            </div>
        </div>
    </section>
    {$category_markup}
    <!-- Popular Service area end -->
    
HTML;
    }

    public function addon_title()
    {
        return __('Popular Service: 02');
    }
}
