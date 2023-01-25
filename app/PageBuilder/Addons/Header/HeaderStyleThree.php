<?php


namespace App\PageBuilder\Addons\Header;

use App\Country;
use App\PageBuilder\Fields\IconPicker;
use App\PageBuilder\Fields\Image;
use App\PageBuilder\Fields\Slider;
use App\PageBuilder\Fields\Text;
use App\PageBuilder\Traits\LanguageFallbackForPageBuilder;
use App\Category;
use App\ServiceCity;
use App\User;

class HeaderStyleThree extends \App\PageBuilder\PageBuilderBase
{
    use LanguageFallbackForPageBuilder;

    public function preview_image()
    {
        return 'home_three/header_3.png';
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
            'name' => 'subtitle',
            'label' => __('Subtitle'),
            'value' => $widget_saved_values['subtitle'] ?? null,
        ]);
        $output .= Text::get([
            'name' => 'service_type',
            'label' => __('Service Type'),
            'value' => $widget_saved_values['service_type'] ?? null,
        ]);
        $output .= IconPicker::get([
            'name' => 'service_icon',
            'label' => __('Service Icon'),
            'value' => $widget_saved_values['service_icon'] ?? null,
        ]);
        $output .= Text::get([
            'name' => 'service_link',
            'label' => __('Service Link'),
            'value' => $widget_saved_values['service_link'] ?? null,
        ]);
        $output .= Image::get([
            'name' => 'dot_image',
            'label' => __('Banner Dot Image'),
            'value' => $widget_saved_values['dot_image'] ?? null,
            'dimensions' => '163x163'
        ]);
        $output .= Image::get([
            'name' => 'banner_image',
            'label' => __('Banner Image'),
            'value' => $widget_saved_values['banner_image'] ?? null,
            'dimensions' => '46x46'
        ]);

        $output .= Image::get([
            'name' => 'image',
            'label' => __('Background Image'),
            'value' => $widget_saved_values['image'] ?? null,
            'dimensions' => '795x1139'
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
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render(): string
    {
        $settings = $this->get_settings();

        $title = $settings['title'];
        $subtitle = $settings['subtitle'];

        $explode = explode(" ", $title);
        $title_end = end($explode);
        $last_space_position = strrpos($title, ' ');
        $title_start = substr($title, 0, $last_space_position);

        $service_type = $settings['service_type'];
        $service_icon = $settings['service_icon'];
        $service_link = $settings['service_link'];
        $image = render_image_markup_by_attachment_id($settings['image']);
        $banner_dot_image = render_image_markup_by_attachment_id($settings['dot_image']);
        $banner_image = render_image_markup_by_attachment_id($settings['banner_image']);
        $happy_clients = __('Happy Clients');
        $happy_clients_count = User::where('user_type', '1')->where('user_status', '1')->count();
        $search_placeholder = __('What are you looking for?');
        $select_country = __('Select Country');
        $select_city = __('Select City');
        $route = route('service.list.category');
        $search_route = route('frontend.home.search.single');
        $popular = __('Popular:');

        $service_countries = Country::where('status', 1)->get();
        $categories = Category::whereHas('services')->select('id', 'name', 'slug')->take(5)->inRandomOrder()->get();
        $country_markup = '';
        $service_markup = '';
        $category_markup = '';
        $each_banner = '';
        $main_banner = [
            [
                'url' => '',
                'photo' => 'banner.png'
            ],
            [
                'url' => '',
                'photo' => 'banner2.png'
            ]
        ];

        foreach ($main_banner as $key => $banner) {
            $each_banner .= <<<EACHBANNER
            <div class="banner-card p-0">
                                        <a href="{$banner['url']}">
                                            <img class="d-block w-100"
                                                onerror="this.src='{{asset('public/assets/front-end/img/image-place-holder.png')}}'"
                                                src="assets/frontend/img/{$banner['photo']}"
                                                alt="">
                                        </a>
                                    </div>
            EACHBANNER;
        }


        foreach ($service_countries as $country) {
            $country_id = $country->id;
            $country_name = $country->country;
            $country_markup .= <<<COUNTRYMARKUP
            <option value="{$country_id}">{$country_name}</option>
            COUNTRYMARKUP;
        }

        foreach ($categories as $cat) {
            $category_name = $cat->name;
            $category_slug = $cat->slug;
            $service_markup .= <<<SERVICECATEGORY
            <option value="{$category_name}">{$category_name}</option>
SERVICECATEGORY;
        }
        foreach ($categories as $cat) {
            $category_name = $cat->name;
            $category_slug = $cat->slug;
            $category_markup .= <<<CATEGORY
    <li><a href="{$route}/{$category_slug}"> {$category_name} </a></li>
CATEGORY;
        }


        return <<<HTML
<!-- Banner area Starts -->
<div class="container container-two">
        <div class="row">
            <div class="col-12">
                <div class="row rtl">
                    <div class="col-xl-12 col-md-12" style="margin-top: 20px">
                        <div class="mt-2 mb-3 brand-slider">
                            <div class="owl-carousel owl-theme " id="banner-slider-custom">
                                {$each_banner}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner area end -->
    
HTML;
    }

    public function addon_title()
    {
        return __('Header: 03');
    }
}
