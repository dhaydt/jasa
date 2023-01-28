<?php

namespace App\Providers;


use App\Helpers\InstagramFeedHelper;
use App\Language;
use App\StaticOption;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
       
    }

    public function boot()
    {
        // $xenditGateway = StaticOption::where('option_name', 'xendit_gateway')->first();
        // if(!$xenditGateway){
        //     $new0 = new StaticOption();
        //     $new0->option_name = 'xendit_gateway';
        //     $new0->option_value = 'on';
        //     $new0->save();
        // }

        // $xenditLogo = StaticOption::where('option_name', 'xendit_preview_logo')->first();
        // if(!$xenditLogo){
        //     $new = new StaticOption();
        //     $new->option_name = 'xendit_preview_logo';
        //     $new->option_value = 'xendit-logo.png';
        //     $new->save();
        // }
        
        // $xenditMode = StaticOption::where('option_name', 'xendit_test_mode')->first();
        // if(!$xenditMode){
        //     $new1 = new StaticOption();
        //     $new1->option_name = 'xendit_test_mode';
        //     $new1->option_value = 'on';
        //     $new1->save();
        // }
        
        // $xenditMode = StaticOption::where('option_name', 'xendit_test_mode')->first();
        // if(!$xenditMode){
        //     $new1 = new StaticOption();
        //     $new1->option_name = 'xendit_test_mode';
        //     $new1->option_value = 'on';
        //     $new1->save();
        // }
        
        // $xenditKey = StaticOption::where('option_name', 'xendit_public_key')->first();
        // if(!$xenditKey){
        //     $new2 = new StaticOption();
        //     $new2->option_name = 'xendit_public_key';
        //     $new2->option_value = null;
        //     $new2->save();
        // }
        
        // $xenditSecret = StaticOption::where('option_name', 'xendit_secret_key')->first();
        // if(!$xenditSecret){
        //     $new3 = new StaticOption();
        //     $new3->option_name = 'xendit_secret_key';
        //     $new3->option_value = null;
        //     $new3->save();
        // }

        Schema::defaultStringLength(191);
        $all_language = Language::all();
        Paginator::useBootstrap();
        if (get_static_option('site_force_ssl_redirection') === 'on'){
            URL::forceScheme('https');
        }

        $this->loadViewsFrom(__DIR__.'/../PageBuilder/views','pagebuilder');
    }
}
