<style>
  .profile-btn {
      max-width: 51px;
      min-width: 51px;
  }
</style>
<div class="mobile-footer d-block d-md-none" id="mobile-footer">
  <div class="unf-bottomnav css-15iqbvc">
      <a class="css-11rf802" href="{{route('homepage')}}">
          <div class="css-mw28ox">
            {{-- <img width="24" height="24"
                  src="{{ asset('assets/front-end/img/home.svg') }}" alt="home"
                  class="css-mw28ox"> --}}
                  <i class="fa-solid fa-house" style="font-size: 18px;"></i>
                </div>Beranda
      </a>
      <a class="css-11rf802" href="/service-list?cat=&subcat=&child_cat=&rating=&sortby=latest_service">
          <div class="css-mw28ox">
            {{-- <img width="24" height="24"
                  src="https://assets.tokopedia.net/assets-tokopedia-lite/v2/atreus/kratos/eb6fad37.svg"
                  alt="wishlist" class="css-mw28ox"> --}}
                  <i class="fa-solid fa-cubes" class="css-mw28ox" style="font-size: 20px;"></i>
                </div>Jasa Terbaru
      </a>
      {{-- <a class="css-11rf802" href="{{route('account-oder')}}" data-cy="bottomnavFeed" id="bottomnavFeed"
          data-testid="icnFooterFeed">
          <div class="css-mw28ox"><img width="24" height="24"
                  src="https://assets.tokopedia.net/assets-tokopedia-lite/v2/atreus/kratos/66eb4811.svg" alt="feed"
                  class="css-mw28ox"></div>Pemesanan
      </a> --}}
      @if(auth('web')->check())
          <a class="css-11rf802" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="text-transform: capitalize; cursor: pointer;">
              <div class="">
                  <div class="">
                    @if(!empty(Auth::guard('web')->user()->image))
                        {!! render_image_markup_by_attachment_id(Auth::guard('web')->user()->image) !!}
                    @else
                        <img class="img-profile rounded-circle" style="width: 24px;height: 24px" src="{{ asset('assets/frontend/img/static/user_profile.png') }}" alt="No Image">
                    @endif
                  </div>
              </div>
              {{Auth::guard('web')->user()->name}}
          </a>
          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
              @if(Auth::guard('web')->user()->user_type==0)
              <a class="dropdown-item" href="{{ route('seller.dashboard')}}"> {{ __('Dashboard') }} </a> 
              @else 
              <a class="dropdown-item" href="{{ route('buyer.dashboard')}}"> {{ __('Dashboard') }} </a> 
              @endif
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{route('seller.logout')}}">Keluar</a>
          </div>
      @else
          <a class="css-11rf802 profile-btn" style="cursor: pointer;" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <div class="">
                  <div class="css-mw28ox">
                      <i class="fa-solid fa-user" style="font-size: 20px;"></i>
                  </div>
              </div>
              Akun
          </a>
          <div class="dropdown-menu bg-white" aria-labelledby="dropdownMenuButton"
              style="text-align:left;">
              <a class="dropdown-item" href="{{route('user.login')}}">
                  <i class="fa fa-sign-in me-2"></i>
                  Login
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{route('user.register')}}">
                  <i class="fa fa-user-circle me-2"></i>Daftar
              </a>
          </div>
      @endif
  </div>
</div>
