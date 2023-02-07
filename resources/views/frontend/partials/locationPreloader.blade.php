<style>
  .locPreloader{
    height: 100vh;
    width: 100vw;
    position: fixed;
    top: 0;
    z-index: 9;
    background-color: #000000a1;
  }
  .custom-badge{
    font-size: 18px;
    padding: 10px 30px;
    border-radius: 40px;
    color: #fff;
    background-color: var(--primary-custom);
  }
</style>
<div class="locPreloader d-none">
  <div class="container h-100">
    <div class="row w-100 h-100 align-items-center justify-content-center flex-column">
      {{-- <img src="{{ asset('assets/frontend/img/loading.gif') }}" height="150px" alt=""> --}}
      <span class="badge custom-badge">
        Mengambil lokasi . . .
      </span>
    </div>
  </div>
</div>