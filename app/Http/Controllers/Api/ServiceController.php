<?php

namespace App\Http\Controllers\Api;

use App\Actions\Media\MediaHelper;
use App\AdminCommission;
use App\Category;
use App\Day;
use App\Http\Controllers\Controller;
use App\Mail\OrderMail;
use App\Notifications\OrderNotification;
use App\Order;
use App\OrderAdditional;
use App\OrderInclude;
use App\Review;
use App\Schedule;
use App\Service;
use App\ServiceArea;
use App\Servicebenifit;
use App\ServiceCity;
use App\ServiceCoupon;
use App\Serviceinclude;
use App\Tax;
use App\User;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Xendit\Xendit;
use Xgenious\Paymentgateway\Facades\XgPaymentGateway;


class ServiceController extends Controller
{

    public function embedCodeTest()
    {
        $iframe_string = '<iframe width="560" height="315" src="https://www.youtube.com/embed/Uc5i1AKaSTs" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        // $result = '';
        preg_match('/src="([^"]+)"/', $iframe_string, $match);
        //$url = $match[1];
        // $result = Str::after($sr,'src="');

        return response()->error([
            'message' =>  end($match),
        ]);
    }
    //top selling services
    public function topService()
    {

        $top_services_query = Service::query()->select('id', 'title', 'image', 'price', 'seller_id')
            ->with('reviews_for_mobile')
            ->whereHas('reviews_for_mobile')
            ->where('status', '1')
            ->where('is_service_on', '1')
            ->when(subscriptionModuleExistsAndEnable('Subscription'), function ($q) {
                $q->whereHas('seller_subscription');
            });


        if (!empty(request()->get('state_id'))) {
            $top_services_query->where('service_city_id', request()->get('state_id'));
        }

        $top_services_query->orderBy('sold_count', 'Desc');

        if (!empty(request()->get('paginate'))) {
            $top_services = $top_services_query->paginate(request()->get('paginate'))->withQueryString();
        } else {
            $top_services = $top_services_query->take(10)->get();
        }



        $service_image = [];
        $service_seller_name = [];
        $reviewer_image = [];
        foreach ($top_services as $service) {
            $service_image[] = get_attachment_image_by_id($service->image);
            $service_seller_name[] = optional($service->seller_for_mobile)->name;
            foreach ($service->reviews_for_mobile as $review) {
                $reviewer_image[] = get_attachment_image_by_id(optional($review->buyer_for_mobile)->image);
            }
        }

        if ($top_services) {
            return response()->success([
                'top_services' => $top_services,
                'service_image' => $service_image,
                'service_seller_name' => $service_seller_name,
                'reviewer_image' => $reviewer_image,
            ]);
        }
        return response()->error([
            'message' => __('Service Not Available'),
        ]);
    }

    // Seller list
    public function sellerList(){
        $seller_lists = User::where(['user_type'=> 0,'user_status' => 1])->orderBy('created_at','desc')->get();

        $mapped = [];
        foreach($seller_lists as $c){
            $d['id'] = $c['id'];
            $d['name'] = $c['name'];
            $d['email'] = $c['email'];
            $d['username'] = $c['username'];
            $d['profile_background'] = $c->profile_background;
            if($c->image){
                $d['image'] = get_attachment_image_by_id($c->image);
            }else{
                $d['image'] = [
                    "image_id" => '01',
                    "path" => "ip.png",
                    "img_url" => asset('assets/frontend/img/ip.png'),
                    "img_alt" => 'default image'
                ];
            }
            $d['service_city'] = $c['service_city'];
            $d['service_area'] = $c['service_area'];
            $d['service_city_name'] = ServiceCity::find($c['service_city']) ? ServiceCity::find($c['service_city'])['service_city'] : '';
            $d['service_area_name'] = ServiceArea::find($c['service_area']) ? ServiceArea::find($c['service_area'])['service_area'] : '';
            $d['user_type'] = $c['user_type'];
            $d['about'] = $c['about'];

            if(isset($city_id)){
                foreach($c['services'] as $s){
                        if($s->service_city_id == $city_id){
                            $d['services'] = $s;
                        }
                    }
                }
            else{
                $d['services'] = $c['services'];
            }
            array_push($mapped, $d);
        }

        return response()->json([
            'seller_list' => $mapped,
        ]);
    }

    public function sellerDetail($id){
        $seller = User::where('id',$id)->firstOrFail();
        $seller_since = User::select('created_at')->where('id', $id)->where('user_status', 1)->first();
        $completed_order = Order::where('seller_id', $id)->where('status', 2)->count();

        if($seller['image']){
            $seller['image'] = get_attachment_image_by_id($seller['image']);
        }else{
            $seller['image'] = [
                "image_id" => '01',
                "path" => "ip.png",
                "img_url" => asset('assets/frontend/img/ip.png'),
                "img_alt" => 'default image'
            ];
        }

        $seller_rating = Review::where('seller_id', $id)->avg('rating');
        $seller_rating_percentage_value = $seller_rating * 20;

        $services = Service::with('serviceCity')->select('id','seller_id','category_id','title','description','price','slug','image','featured','service_city_id')
        ->where(['seller_id'=>$id,'status'=>1,'is_service_on'=>1])
        ->take(4)
        ->inRandomOrder()
        ->get();

        $mapped = [];
        foreach($services as $s){
            $s['image'] = get_attachment_image_by_id($s['image']);
            $s['service_area_name'] = getAreaService($s);
            $s['service_city_name'] = getAreaService($s, 'city');

            array_push($mapped, $s);
        }

        $service_rating = Review::where('seller_id', $id)->avg('rating');
        $service_reviews = Review::where('seller_id', $id)->get();

        $data = [
            'seller' => $seller,
            'seller_since' => $seller_since,
            'completed_order' => $completed_order,
            'seller_rating_percentage_value' => $seller_rating_percentage_value,
            'services' => $mapped,
            'service_rating' => $service_rating,
            'service_reviews' => $service_reviews,
        ];

        return response()->json([
            'seller_detail' => $data
        ]);
    }

    public function service_per_categories($city)
    {
        $cat = Category::with('services')->where('status', 1)->whereHas('services', function($q){
            $q->where('status', 1)
            ->where('is_service_on', 1);
        })->get();
        $city = ServiceCity::where('service_city', $city)->first();
        if($city){
            $city_id = $city['id']; 
        }
        $mapped = [];
        foreach($cat as $c){
            $d['id'] = $c['id'];
            $d['name'] = $c['name'];
            $d['slug'] = $c['slug'];
            $d['icon'] = $c['icon'];
            $d['image'] = get_attachment_image_by_id($c->image);
            $d['status'] = $c['status'];
            $d['mobile_icon'] = $c['mobile_icon'];
            $d['services'] = [];
            if(isset($city_id)){
                foreach($c['services'] as $s){
                        if($s->service_city_id == $city_id){
                            $e = $s;
                            $e['service_area_name'] = getAreaService($s);
                            $e['service_city_name'] = getAreaService($s, 'city');
                            $seller = User::find($s['seller_id']);
                            $e['seller'] = $seller;
                            $s['image'] = get_attachment_image_by_id($s['image']);
                            $e['reviews_for_mobile'];
                            if($seller){
                                $seller['image'] = get_attachment_image_by_id($seller['image']);
                            }
                            array_push($d['services'], $e);
                        }
                    }
                }
            else{
                $d['services'] = $c['services'];
                foreach($c['services'] as $s){
                    $seller = User::find($s['seller_id']);
                    $e['seller'] = $seller;
                    if($seller){
                        $seller['image'] = get_attachment_image_by_id($seller['image']);
                    }
                    $s['image'] = get_attachment_image_by_id($s['image']);
                }
            }

            array_push($mapped, $d);
        }

        return response()->json([
            'service_per_categories' => $mapped,
        ]);
    }

    //latest services
    public function latestService()
    {
        $latest_services_query = Service::query()->select('id', 'title', 'image', 'price', 'seller_id', 'category_id')
            ->with('reviews_for_mobile')
            ->where('status', '1')
            ->where('is_service_on', '1')
            ->when(subscriptionModuleExistsAndEnable('Subscription'), function ($q) {
                $q->whereHas('seller_subscription');
            });

        if (!empty(request()->get('state_id'))) {
            $latest_services_query->where('service_city_id', request()->get('state_id'));
        }

        $latest_services  = $latest_services_query->latest()
            ->take(10)
            ->get();
        $service_image = [];
        $service_seller_name = [];
        $reviewer_image = [];
        foreach ($latest_services as $service) {
            $service_image[] = get_attachment_image_by_id($service->image);
            $service_seller_name[] = optional($service->seller_for_mobile)->name;
            $seller = User::select('image')->find($service['seller_id']);
            $service['seller'] = $seller;
            $service['service_area_name'] = getAreaService($service);
            $service['service_city_name'] = getAreaService($service, 'city');
            if($seller){
                $service['seller']['image'] = get_attachment_image_by_id($seller['image']);
            }
            foreach ($service->reviews_for_mobile as $review) {
                $reviewer_image[] = get_attachment_image_by_id(optional($review->buyer_for_mobile)->image);
            }
        }

        if ($latest_services) {
            return response()->success([
                'latest_services' => $latest_services,
                'service_image' => $service_image,
                'service_seller_name' => $service_seller_name,
                'reviewer_image' => $reviewer_image,
            ]);
        }
        return response()->error([
            'message' => __('Service Not Available'),
        ]);
    }

    public function availableCity()
    {
        $latest_services_query = Service::query()->select('id', 'title', 'image', 'price', 'seller_id')
            ->with('reviews_for_mobile')
            ->where('status', '1')
            ->where('is_service_on', '1')
            ->when(subscriptionModuleExistsAndEnable('Subscription'), function ($q) {
                $q->whereHas('seller_subscription');
            });

        $serv = Service::where('status', 1)->get();
        $cities = [];
        $ids = [];
        $merged = [];
        foreach ($serv as $s) {
            if ($s->serviceCity) {
                $id_city = $s->serviceCity->id;
                $name = $s->serviceCity->service_city;
                array_push($ids, $id_city);
                array_push($cities, $name);
            }
        }

        foreach (array_unique($cities) as $key => $cit) {
            $data = [
                'id' => $ids[$key],
                'name' => $cities[$key]
            ];
            array_push($merged, $data);
        }

        // dd($merged);

        if (!empty(request()->get('state_id'))) {
            $latest_services_query->where('service_city_id', request()->get('state_id'));
        }

        $latest_services  = $latest_services_query->latest()
            ->take(10)
            ->get();
        $service_image = [];
        $service_seller_name = [];
        $reviewer_image = [];
        foreach ($latest_services as $service) {
            $service_image[] = get_attachment_image_by_id($service->image);
            $service_seller_name[] = optional($service->seller_for_mobile)->name;
            foreach ($service->reviews_for_mobile as $review) {
                $reviewer_image[] = get_attachment_image_by_id(optional($review->buyer_for_mobile)->image);
            }
        }

        if ($latest_services) {
            return response()->success([
                'list_service_city' => $merged,
                // 'service_image'=>$service_image,
                // 'service_seller_name'=>$service_seller_name,
                // 'reviewer_image'=>$reviewer_image,
            ]);
        }
        return response()->error([
            'message' => __('Service Not Available'),
        ]);
    }

    // service details
    public function serviceDetails($id = null)
    {
        $service_details = Service::with('serviceFaq')->where('status', 1)->where('is_service_on', 1)->where('id', $id)->first();
        if (is_null($service_details)) {
            return response(["msg" => __("service not found")], 500);
        }
        $service_details['service_area_name'] = getAreaService($service_details);
        $service_details['service_city_name'] = getAreaService($service_details, 'city');
        $service_image = get_attachment_image_by_id($service_details->image);
        $service_seller_name = optional($service_details->seller_for_mobile)->name;
        $service_seller_image_Id = optional($service_details->seller_for_mobile)->image;
        $service_seller_image = get_attachment_image_by_id($service_seller_image_Id);
        $seller_complete_order = Order::where('seller_id', $service_details->seller_id)->where('status', 2)->count();
        $seller_cancelled_order = Order::where('seller_id', $service_details->seller_id)->where('status', 4)->count();
        $seller_rating = Review::where('seller_id', $service_details->seller_id)->avg('rating');
        $seller_rating_percentage_value = round($seller_rating * 20);
        $seller_from = optional(optional($service_details->seller_for_mobile)->country)->country;
        $seller_since = User::select('created_at')->where('id', $service_details->seller_id)->where('user_status', 1)->first();
        $service_includes = Serviceinclude::select('id', 'service_id', 'include_service_title')->where('service_id', $service_details->id)->get();
        $service_benifits = Servicebenifit::select('id', 'service_id', 'benifits')->where('service_id', $service_details->id)->get();

        $order_completion_rate = 0;
        if ($seller_complete_order > 0 || $seller_cancelled_order > 0) {
            $order_completion_rate = $seller_complete_order / ($seller_complete_order + $seller_cancelled_order) * 100;
        }

        $service_reviews = $service_details->reviews_for_mobile->transform(function ($item) {
            $buyer_details = User::find($item->buyer_id);
            $item->buyer_name = !is_null($buyer_details) ? $buyer_details->name : 'Unknown'; // $item->buyer_id;
            $image_url =  get_attachment_image_by_id(optional($buyer_details)->image) ? get_attachment_image_by_id($buyer_details->image)['img_url'] : null;
            $item->buyer_image = !is_null($buyer_details) ? $image_url : null; // $item->buyer_id;
            return $item;
        });
        $reviewer_image = [];
        foreach ($service_details->reviews_for_mobile as $review) {
            $reviewer_image[] = get_attachment_image_by_id(optional($review->buyer_for_mobile)->image);
        }
        $service_brands = [];
        foreach (json_decode($service_details->brands) as $b) {
            array_push($service_brands, getBrands($b));
        }

        $service_video_url = $service_details->video;
        preg_match('/src="([^"]+)"/', $service_video_url, $service_video_url_match);

        if ($service_details) {
            return response()->success([ 
                'service_details' => $service_details,
                'service_image' => $service_image,
                'service_seller_name' => $service_seller_name,
                'service_seller_image' => is_array($service_seller_image) && !empty($service_seller_image) ? $service_seller_image : null,
                'seller_complete_order' => $seller_complete_order,
                'seller_rating' => $seller_rating_percentage_value,
                'order_completion_rate' => round($order_completion_rate),
                'seller_from' => $seller_from,
                'seller_since' => $seller_since,
                'service_includes' => $service_includes,
                'service_benifits' => $service_benifits,
                'service_reviews' => $service_reviews,
                'reviewer_image' => $reviewer_image,
                'service_brands' => $service_brands,
                'video_url' => is_null($service_video_url) ? null : end($service_video_url_match)
            ]);
        }
        return response()->error([
            'message' => __('Service Not Available'),
        ]);
    }

    //service rating
    public function serviceRating(Request $request, $id = null)
    {
        $request->validate([
            'rating' => 'required|integer',
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'message' => 'required',
        ]);

        $order = Order::find($id);
        if(!$order){
            return response()->error([
                'message' => __('Order not found'),
            ]);
        }
        if($order['status'] != "2" || $order['status'] != 2){
            return response()->error([
                'message' => __('Order must be completed for post a review!'),
            ]);
        }
        $service_details = Service::select('id', 'seller_id')->where('id', $order['service_id'])->first();
        $order_count = Order::where(['id' => $id, 'buyer_id' => auth('sanctum')->user()->id, 'status' => 2])->count();



        if (!empty($order_count) && $order_count > 0) {
            //todo add another filter to check this buyer already leave a review in this or not
            $old_review = Review::where(['service_id' => $service_details->id, 'buyer_id' => auth('sanctum')->user()->id])->count();
            if ($old_review > 0) {
                return response()->error([
                    'message' => __('you have already leave a review in this service'),
                ]);
            }
            Review::create([
                'service_id' => $service_details->id,
                'seller_id' => $service_details->seller_id,
                'buyer_id' => auth('sanctum')->user()->id,
                'rating' => $request->rating,
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
            ]);

            return response()->success([
                'message' => __('Review Added Success'),
            ]);
        }

        return response()->error([
            'message' => __('You need to buy this service to leave feedback'),
        ]);
    }

    //all services
    public function allServices()
    {
        $all_services_query = Service::query()->with('seller_for_mobile', 'reviews_for_mobile', 'serviceCity')
            ->select('id', 'seller_id', 'title', 'price', 'image', 'is_service_online', 'service_city_id')
            ->where('status', 1)
            ->where('is_service_on', 1)
            ->when(subscriptionModuleExistsAndEnable('Subscription'), function ($q) {
                $q->whereHas('seller_subscription');
            });

        if (!empty(request()->get('state_id'))) {
            $all_services_query->where('service_city_id', request()->get('state_id'));
        }

        $all_services = $all_services_query->OrderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        if ($all_services) {
            foreach ($all_services as $service) {
                $service_image[] = get_attachment_image_by_id($service->image);
                $service_country[] = optional(optional($service->serviceCity)->countryy)->country;
                $service['service_area_name'] = getAreaService($service);
                $service['service_city_name'] = getAreaService($service, 'city');
            }
            return response()->success([
                'all_services' => $all_services,
                'service_image' => $service_image,
            ]);
        }

        return response()->error([
            'message' => __('Service Not Available'),
        ]);
    }

    //service search by category
    public function searchByCategory($category_id = null)
    {

        $all_services_query = Service::query()->with('seller_for_mobile', 'reviews_for_mobile', 'serviceCity')
            ->select('id', 'seller_id', 'title', 'price', 'image', 'is_service_online', 'service_city_id')
            ->where('status', 1)
            ->where('is_service_on', 1)
            ->where('category_id', $category_id)
            ->when(subscriptionModuleExistsAndEnable('Subscription'), function ($q) {
                $q->whereHas('seller_subscription');
            });

        if (!empty(request()->get('state_id'))) {
            $all_services_query->where('service_city_id', request()->get('state_id'));
        }

        $all_services  =   $all_services_query->OrderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        foreach($all_services as $a){
            $a['service_area_name'] = getAreaService($a);
            $a['service_city_name'] = getAreaService($a, 'city');
            $seller = User::find($a['seller_id']);
            $a['seller'] = $seller;
            if($seller){
                $seller['image'] = get_attachment_image_by_id($seller['image']);
            }
        }

        if ($all_services->count() >= 1) {
            foreach ($all_services as $service) {
                $service_image[] = get_attachment_image_by_id($service->image);
                $service_country[] = optional(optional($service->serviceCity)->countryy)->country;
            }
            return response()->success([
                'all_services' => $all_services,
                'service_image' => $service_image,
            ]);
        }
        return response()->error([
            'message' => __('Service Not Found'),
        ]);
    }

    //service search by category and subcategory
    public function searchBySubCategory($category_id, $subcategory_id)
    {

        $all_services = Service::with('seller_for_mobile', 'reviews_for_mobile', 'serviceCity')
            ->select('id', 'seller_id', 'title', 'price', 'image', 'is_service_online', 'service_city_id')
            ->where('status', 1)
            ->where('is_service_on', 1)
            ->when(subscriptionModuleExistsAndEnable('Subscription'), function ($q) {
                $q->whereHas('seller_subscription');
            })
            ->where('category_id', $category_id)
            ->where('subcategory_id', $subcategory_id)
            ->OrderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        if ($all_services->count() >= 1) {
            foreach ($all_services as $service) {
                $service_image[] = get_attachment_image_by_id($service->image);
                $service_country[] = optional(optional($service->serviceCity)->countryy)->country;
            }
            return response()->success([
                'all_services' => $all_services,
                'service_image' => $service_image,
            ]);
        }
        return response()->error([
            'message' => __('Service Not Found'),
        ]);
    }

    //service search by category, subcategory and rating
    public function searchByRating($category_id = null, $subcategory_id = null, $rating = null)
    {
        if (isset($rating)) {
            $rating = (int) $rating;
            $all_services = Service::with('seller_for_mobile', 'reviews_for_mobile', 'serviceCity')
                ->select('id', 'seller_id', 'title', 'price', 'image', 'is_service_online', 'service_city_id')
                ->where('status', 1)
                ->where('is_service_on', 1)
                ->when(subscriptionModuleExistsAndEnable('Subscription'), function ($q) {
                    $q->whereHas('seller_subscription');
                })
                ->where('category_id', $category_id)
                ->where('subcategory_id', $subcategory_id);

            $all_services = $all_services->whereHas('reviews', function ($q) use ($rating) {
                $q->groupBy('reviews.id')
                    ->havingRaw('AVG(reviews.rating) >= ?', [$rating])
                    ->havingRaw('AVG(reviews.rating) < ?', [$rating + 1]);
            });

            if (!empty(request()->get('state_id'))) {
                $all_services->where('service_city_id', request()->get('state_id'));
            }

            $all_services = $all_services > OrderBy('id', 'desc')
                ->paginate(10)
                ->withQueryString();

            $service_image[] = '';
            if ($all_services->count() >= 1) {
                foreach ($all_services as $service) {
                    $service_image[] = get_attachment_image_by_id($service->image);
                    $service_country[] = optional(optional($service->serviceCity)->countryy)->country;
                }
                return response()->success([
                    'all_services' => $all_services,
                    'service_image' => $service_image,
                ]);
            }
            return response()->error([
                'message' => __('Service Not Found'),
            ]);
        }
    }

    //service search by category, subcategory and rating and sort by
    public function searchBySort()
    {
        $service_quyery = Service::query();
        $service_quyery->with('seller_for_mobile', 'reviews_for_mobile', 'serviceCity');
        $service_quyery->select('id', 'seller_id', 'title', 'price', 'image', 'is_service_online', 'service_city_id')
            ->when(subscriptionModuleExistsAndEnable('Subscription'), function ($q) {
                $q->whereHas('seller_subscription');
            });
        if (!empty(request()->get('cat'))) {
            $service_quyery->where('category_id', request()->get('cat'));
        }
        if (!empty(request()->get('subcat'))) {
            $service_quyery->where('subcategory_id', request()->get('subcat'));
        }
        if (!empty(request()->get('rating'))) {
            $rating = (int) request()->get('rating');
            $service_quyery->whereHas('reviews', function ($q) use ($rating) {
                $q->groupBy('reviews.id')
                    ->havingRaw('AVG(reviews.rating) >= ?', [$rating])
                    ->havingRaw('AVG(reviews.rating) < ?', [$rating + 1]);
            });
        }

        if (!empty(request()->get('sortby'))) {

            if (request()->get('sortby') == 'latest_service') {
                $service_quyery->orderBy('id', 'Desc');
            }
            if (request()->get('sortby') == 'lowest_price') {
                $service_quyery->orderBy('price', 'Asc');
            }
            if (request()->get('sortby') == 'highest_price') {
                $service_quyery->orderBy('price', 'Desc');
            }
        }
        $all_services = $service_quyery->where('status', 1)
            ->where('is_service_on', 1)
            ->OrderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $service_image = [];
        if ($all_services->count() >= 1) {
            foreach ($all_services as $service) {
                $service_image[] = get_attachment_image_by_id($service->image);
                $service_country[] = optional(optional($service->serviceCity)->countryy)->country;
            }
            return response()->success([
                'all_services' => $all_services,
                'service_image' => $service_image,
            ]);
        }
        return response()->error([
            'message' => __('Service Not Found'),
        ]);
    }

    //service book
    public function serviceBook($id = null)
    {
        $service = Service::with('serviceAdditional', 'serviceInclude', 'serviceBenifit', 'seller_for_mobile', 'serviceCity')
            ->select('id', 'seller_id', 'title', 'price', 'tax', 'image', 'is_service_online', 'service_city_id')
            ->where('status', 1)
            ->where('is_service_on', 1)
            ->when(subscriptionModuleExistsAndEnable('Subscription'), function ($q) {
                $q->whereHas('seller_subscription');
            })
            ->where('id', $id)
            ->first();

        $service_image[] = '';
        if (isset($service)) {
            $service_image[] = get_attachment_image_by_id($service->image);
            return response()->success([
                'service' => $service,
                'service_image' => $service_image,
            ]);
        }
        return response()->error([
            'message' => __('Service Not Found'),
        ]);
    }

    //get schedule by seller
    public function scheduleByDay($day, $seller_id)
    {
        $get_day = Day::select('id', 'day', 'total_day')
            ->where('day', $day)
            ->where('seller_id', $seller_id)
            ->first();

        if (!is_null($get_day)) {
            $schedules = Schedule::select('schedule')
                ->where('seller_id', $seller_id)
                ->where('day_id', $get_day->id)
                ->get();

            if ($schedules->count() >= 1) {
                return response()->json([
                    'day' => $get_day,
                    'schedules' => $schedules,
                ]);
            }
            return response()->json([
                'status' => __('no schedule'),
            ]);
        }
        return response()->json([
            'status' => __('no schedule'),
        ]);
    }

    // coupon apply
    public function couponApply(Request $request)
    {
        if (!isset($request->coupon_code)) {
            return response()->error([
                'message' => __('Please enter your coupon'),
            ]);
        }

        $coupon_code = ServiceCoupon::where('code', $request->coupon_code)->first();
        $current_date = date('Y-m-d');

        if (!empty($coupon_code)) {

            if ($coupon_code->seller_id != $request->seller_id) {
                return response()->error([
                    'message' => __('Coupon is not Applicable for this Service'),
                ]);
            }

            if ($coupon_code->code == $request->coupon_code && $coupon_code->expire_date > $current_date) {

                if ($coupon_code->discount_type == 'percentage') {
                    $coupon_amount = ($request->total_amount * $coupon_code->discount) / 100;
                    return response()->success([
                        'status' => __('success'),
                        'coupon_amount' => $coupon_amount,
                    ]);
                } else {
                    $coupon_amount = $coupon_code->discount;
                    return response()->success([
                        'status' => __('success'),
                        'coupon_amount' => $coupon_amount,
                    ]);
                }
            }

            if ($coupon_code->expire_date < $current_date) {
                return response()->error([
                    'status' => __('expired'),
                    'msg' => __('Coupon is Expired'),
                ]);
            }
        } else {
            return response()->error([
                'status' => __('invalid'),
                'msg' => __('Coupon is Invalid'),
            ]);
        }
    }

    // service city
    public function serviceCity()
    {
        $service_city = ServiceCity::query()->select('id', 'service_city')->where('status', 1)->get();

        if ($service_city) {
            return response()->success([
                'service_city' => $service_city,
            ]);
        }
        return response()->error([
            'message' => __('Service City Not Available'),
        ]);
    }

    // home search
    public function homeSearch(Request $request)
    {
        // dd($request->service_city_id);
        $services = Service::query();
        $services->with('seller_for_mobile', 'reviews_for_mobile', 'serviceCity');
        $services->where('status', 1)
            ->where('is_service_on', 1)
            ->when(subscriptionModuleExistsAndEnable('Subscription'), function ($q) {
                $q->whereHas('seller_subscription');
            });

        if (!isset($request->service_city_id)) {

            $services->Where('title', 'LIKE', '%' . $request->search_text . '%')
                ->orWhere('description', 'LIKE', '%' . $request->search_text . '%');
        } else {
            $services->where('service_city_id', $request->service_city_id)->where(function($q) use($request){
                $q->Where('title', 'LIKE', '%' . $request->search_text . '%')
                ->orWhere('description', 'LIKE', '%' . $request->search_text . '%');
            })
                ;
        }
        $services->where('status', 1);
        $services =  $services->orderBy('id', 'desc')->get();

        // dd($services);

        if (count($services) > 0) {
            foreach ($services as $service) {
                $service_image[] = get_attachment_image_by_id($service->image);
                $seller = User::select('image')->find($service->seller_id);
                $service['seller'] = $seller;
                $service['service_area_name'] = getAreaService($service);
                $service['service_city_name'] = getAreaService($service, 'city');
                if($seller){
                    $service['seller']['image'] = get_attachment_image_by_id($seller->image);
                }
            }
            return response()->success([
                'services' => $services,
                'service_image' => $service_image,
            ]);
        }

        return response()->error([
            'message' => __('No Service Found'),
        ]);
    }

    // order create
    public function order(Request $request)
    {
        $is_service_online_bool = $request->is_service_online === '1';
        if ($is_service_online_bool) {
            $request->validate([
                'name' => 'required|max:191',
                'email' => 'required|max:191',
                'phone' => 'required|max:191',
                'address' => 'nullable|max:191',
                // 'choose_service_city' => 'nullable',
                // 'choose_service_area' => 'nullable',
                // 'choose_service_country' => 'nullable',
                'date' => 'nullable|max:191',
                'schedule' => 'nullable|max:191',
                'include_services' => 'nullable',
                'include_services.*.title' => 'nullable',
                'include_services.*.price' => 'nullable',
                'include_services.*.quantity' => 'nullable',
            ]);
        }

        $commission = AdminCommission::first();

        $payment_status = 'pending';
        // if ($request->selected_payment_gateway == 'cash_on_delivery' || $request->selected_payment_gateway == 'manual_payment') {
        //     $payment_status = 'pending';
        // } else {
        //     $payment_status = 'pending';
        // }


        if (empty($request->seller_id)) {
            return response()->error([
                'message' => __('Seller Id missing, please try another seller services'),
            ]);
        }

        // if ($request->selected_payment_gateway === 'manual_payment') {
        //     $this->validate($request, [
        //         'manual_payment_image' => 'required|mimes:jpg,jpeg,png,pdf'
        //     ]);
        // }

        Order::create([
            'service_id' => $request->service_id,
            'seller_id' => $request->seller_id,
            'buyer_id' => Auth::guard('sanctum')->check() ? Auth::guard('sanctum')->user()->id : NULL,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'post_code' => !$is_service_online_bool ? $request->post_code : '0000',
            'address' => !$is_service_online_bool ? $request->address : 'n/a',
            'city' => $request->choose_service_city,
            'area' => $request->choose_service_area,
            'country' => $request->choose_service_country,
            'date' => !$is_service_online_bool ? $request->date : '00.00.00',
            'schedule' => !$is_service_online_bool ? $request->schedule : '00.00.00',
            'package_fee' => 0,
            'is_order_online' => $is_service_online_bool ? 1 : '0',
            'extra_service' => 0,
            'sub_total' => 0,
            'tax' => 0,
            'total' => 0,
            'commission_type' => $commission->commission_charge_type,
            'commission_charge' => $commission->commission_charge,
            'status' => 0,
            'order_note' => $request->order_note,
            'payment_gateway' => $request->selected_payment_gateway,
            'payment_status' => $payment_status,
            'brands' => $request->brands,
        ]);

        $last_order_id = DB::getPdo()->lastInsertId();
        $service_details = Service::where('id', $request->service_id)->first();
        $service_sold_count = Service::select('sold_count')->where('id', $request->service_id)->first();

        Service::where('id', $request->service_id)->update(['sold_count' => $service_sold_count->sold_count + 1]);

        $package_fee = $is_service_online_bool ? $service_details->price : 0;

        // dd($package_fee);

        if (isset($request->include_services)) {
            $included_services = !empty($request->include_services) ? json_decode($request->include_services, true) : (object) [];
            // dd(json_decode($request->include_services));
            foreach (current($included_services) as $requested_service) {
            // foreach ($included_services as $requested_service) {
                // dd($requested_service);
                $package_fee += $requested_service['quantity'] * $requested_service['price'];
                OrderInclude::create([
                    'order_id' => $last_order_id,
                    'title' => $requested_service['title'],
                    'price' => $requested_service['price'],
                    'quantity' => $requested_service['quantity'],
                ]);
            }
        } elseif ($request->is_service_online === 0 && count($request->include_services) < 1) {
            return response()->error([
                'message' => __('Include service required'),
            ]);
        }

        $extra_service = 0;
        if (!empty($request->additional_services)) {
            $additional_services = !empty($request->additional_services) ? json_decode($request->additional_services, true) : (object) [];
            foreach (current($additional_services) as $requested_additional) {
                $extra_service += $requested_additional['quantity'] * $requested_additional['additional_service_price'];

                OrderAdditional::create([
                    'order_id' => $last_order_id,
                    'title' => $requested_additional['additional_service_title'],
                    'price' => $requested_additional['additional_service_price'],
                    'quantity' => $requested_additional['quantity'],
                ]);
            }
        }
        
        $tax_amount = 0;
        $tax = Service::select('tax')->where('id', $request->service_id)->first();
        $service_details_for_book = Service::select('id', 'service_city_id')->where('id', $request->service_id)->first();
        $service_country =  optional(optional($service_details_for_book->serviceCity)->countryy)->id;
        $country_tax =  Tax::select('id', 'tax')->where('country_id', $service_country)->first();
        $sub_total = $package_fee + $extra_service;
        if (!is_null($country_tax)) {
            $tax_amount = ($sub_total * $country_tax->tax) / 100;
        }
        $total = $sub_total + $tax_amount;
        // dd($sub_total);

        //calculate coupon amount
        $coupon_code = '';
        $coupon_type = '';
        $coupon_amount = 0;

        if (!empty($request->coupon_code)) {
            $coupon_code = ServiceCoupon::where('code', $request->coupon_code)->first();
            $current_date = date('Y-m-d');
            if (!empty($coupon_code)) {
                if ($coupon_code->seller_id == $request->seller_id) {
                    if ($coupon_code->code == $request->coupon_code && $coupon_code->expire_date > $current_date) {
                        if ($coupon_code->discount_type == 'percentage') {
                            $coupon_amount = ($total * $coupon_code->discount) / 100;
                            $total = $total - $coupon_amount;
                            $coupon_code = $request->coupon_code;
                            $coupon_type = 'percentage';
                        } else {
                            $coupon_amount = $coupon_code->discount;
                            $total = $total - $coupon_amount;
                            $coupon_code = $request->coupon_code;
                            $coupon_type = 'amount';
                        }
                    } else {
                        $coupon_code = '';
                    }
                } else {
                    $coupon_code = '';
                }
            }
        }


        //commission amount
        $commission_amount = 0;
        if ($commission->commission_charge_type == 'percentage') {
            $commission_amount = ($sub_total * $commission->commission_charge) / 100;
        } else {
            $commission_amount = $commission->commission_charge;
        }

        if ($request->selected_payment_gateway === 'manual_payment') {
            if ($image = $request->file('manual_payment_image')) {
                $imageName = 'manual_attachment_' . time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move('assets/uploads/manual-payment/', $imageName);
                Order::where('id', $last_order_id)->update([
                    'manual_payment_image' => $imageName
                ]);
            }
        }

        // dd($sub_total);


        $order = Order::where('id', $last_order_id)->update([
            'package_fee' => $package_fee,
            'extra_service' => $extra_service,
            'sub_total' => $sub_total,
            'tax' => $tax_amount,
            'total' => $total,
            'coupon_code' => $coupon_code,
            'coupon_type' => $coupon_type,
            'coupon_amount' => $coupon_amount,
            'commission_amount' => $commission_amount,
        ]);

        // dd($order);

        //Send order notification to seller
        $seller = User::where('id', $request->seller_id)->first();
        $order_message = __('You have a new order');
        $seller->notify(new OrderNotification($last_order_id, $request->service_id, $request->seller_id, $request->buyer_id, $order_message));

        $order_details = Order::find($last_order_id);


        //Send order email to buyer for cash on delivery
        // try {
        //     $subject = __('You have successfully created order');
        //     Mail::to($order_details->email)->send(new OrderMail($subject, $order_details));
        //     Mail::to($seller->email)->send(new OrderMail($subject, $order_details));
        //     Mail::to(get_static_option('site_global_email'))->send(new OrderMail($subject, $order_details));
        // } catch (\Exception $e) {
        //     return response()->error($e->getMessage());
        // }
        //todo send success/cancel url
        //todo is it has paytm parameter then return paytm object instance
        $random_order_id_1 = Str::random(30);
        $random_order_id_2 = Str::random(30);
        $new_order_id = $random_order_id_1 . $last_order_id . $random_order_id_2;
        $paytm_details = null;

        $payment_url = '';

        if ($request->payment_method == 'xendit') {
            $user_info = Auth::guard('sanctum')->user();
            $user_name = $user_info['name'];
            $user_email = $user_info['email'];
            $order = $last_order_id;
            $value = $total;
            $tran = rand(1000, 9999) . '-' . Str::random(5) . '-' . time();
            $order = Order::find($last_order_id);
            $type = '';
            $xendit_key = getenv('XENDIT_SECRET');

            session()->put('order_id', $last_order_id);

            Xendit::setApiKey($xendit_key);

            $user = [
                'given_names' => $user_name ? $user_name : 'invalid name',
                'email' => $user_email,
                'mobile_number' => $request->phone,
                'address' => 'no data',
            ];

            // dd($user);
            $redirect_url = route('frontend.order.payment.xendit.success', [$last_order_id, $tran]);

            $params = [
                'external_id' => 'jasakita' . $request->phone . $last_order_id,
                'amount' => round($value, 0),
                'payer_email' => $user_email,
                'description' => env('APP_NAME') ? env('APP_NAME') : 'JasaKita',
                // 'payment_methods' => [$type],
                'fixed_va' => true,
                'should_send_email' => true,
                'customer' => $user,
                'success_redirect_url' => $redirect_url,
            ];

            
            $checkout_session = \Xendit\Invoice::create($params);

            // return redirect()->away($checkout_session['invoice_url']);
            $payment_url = $checkout_session['invoice_url'];


            return response()->json(['payment_redirect_url' => $payment_url, 'order_id' => $last_order_id]);
        }

        if ($request->has('paytm') && !empty($request->has('paytm'))) {
            $user_info = Auth::guard('sanctum')->user();
            $title = Str::limit(strip_tags($service_details->title), 20);
            $description = sprintf(__('Order id #%1$d Email: %2$s, Name: %3$s'), $last_order_id, $user_info->email, $user_info->name);
            $paytm_details = XgPaymentGateway::paytm()->charge_customer([
                'amount' => $total,
                'title' => $title,
                'description' => $description,
                'ipn_url' => route('frontend.paytm.ipn'),
                'order_id' => $last_order_id,
                'track' => \Str::random(36),
                'success_url' => route('frontend.order.payment.success', $new_order_id),
                'cancel_url' => route('frontend.order.payment.cancel.static', $last_order_id),
                'email' => $user_info->email,
                'name' => $user_info->name,
                'payment_type' => 'order',
            ]);
        }

        return response()->success([
            'order_id' => $last_order_id,
            'service_sold_count' => $service_sold_count,
            'package_fee' => float_amount_with_currency_symbol($package_fee),
            'extra_service' => float_amount_with_currency_symbol($extra_service),
            'sub_total' => float_amount_with_currency_symbol($sub_total),
            'tax_amount' => float_amount_with_currency_symbol($tax_amount),
            'total' => float_amount_with_currency_symbol($total),
            'coupon_code' => $coupon_code,
            'coupon_type' => $coupon_type,
            'coupon_amount' => float_amount_with_currency_symbol($coupon_amount),
            'commission_amount' => float_amount_with_currency_symbol($commission_amount),
            'success_url' => route('frontend.order.payment.success', $new_order_id),
            'cancel_url' => route('frontend.order.payment.cancel.static', $last_order_id),
            'paytm_details' => $paytm_details,
            // 'payment_url' => $payment_url
        ]);
    }

    public function imageUpload(Request $request)
    {
        $this->validate($request, [
            'file' => 'nullable|mimes:jpg,jpeg,png,gif|max:11000'
        ]);
        MediaHelper::insert_media_image($request);
        $last_image_id = DB::getPdo()->lastInsertId();
        return response()->success([
            'image_id' => $last_image_id,
        ]);
    }

    public function manualPaymentImage(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:jpeg,jpg,png,bmp'
        ]);

        if (isset($request->order_id)) {
            if ($image = $request->file('image')) {
                $imageName = 'manual_attachment_' . time() . '-' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move('assets/uploads/manual-payment/', $imageName);

                $update = Order::where('id', $request->order_id)->update([
                    'manual_payment_image' => $imageName
                ]);
            }
        }
    }
    public function paymentStatusUpdate(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer'
        ]);
        $order_details = Order::find($request->order_id);

        $user_id = Auth::guard("sanctum")->id();
        $order_details->payment_status = 'complete';
        $order_details->save();

        if ($request->has('job_id') && $request->job_id === $order_details->job_post_id) {
            BuyerJob::where('id', $request->job_id)->update(['status' => 1]);
        }
        return response()->error(['message' => __('payment status update success')]);
    }
}
