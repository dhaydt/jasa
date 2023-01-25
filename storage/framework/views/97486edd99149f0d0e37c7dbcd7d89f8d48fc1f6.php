<?php if(Auth::guard('web')->check()): ?>
<div class="login-account ml-3">
    <li>
        <div class="info-bar-item-two">
            <a class="accounts loggedin text-light" href="javascript:void(0)">
                <span class="title"> <?php echo e(Auth::guard('web')->user()->name); ?> </span>
            </a>
            <div class="author-thumb">
                <?php if(!empty(Auth::guard('web')->user()->image)): ?>
                    <?php echo render_image_markup_by_attachment_id(Auth::guard('web')->user()->image); ?>

                <?php else: ?>
                    <img src="<?php echo e(asset('assets/frontend/img/static/user_profile.png')); ?>" alt="No Image">
                <?php endif; ?>
                
            </div>
            <ul class="account-list-item mt-2">
                <li class="list"> 
                    <?php if(Auth::guard('web')->user()->user_type==0): ?>
                    <a href="<?php echo e(route('seller.dashboard')); ?>"> <?php echo e(__('Dashboard')); ?> </a> 
                    <?php else: ?> 
                    <a href="<?php echo e(route('buyer.dashboard')); ?>"> <?php echo e(__('Dashboard')); ?> </a> 
                    <?php endif; ?>
                </li>
                <li class="list"> <a href="<?php echo e(route('seller.logout')); ?>"> <?php echo e(__('Logout')); ?> </a> </li>
            </ul>
        </div>
    </li>
</div>
<?php else: ?>
    <div class="login-account" style="padding: 0 28px;">
        <a class="accounts text-light" href="javascript:void(0)"> <span class="account"><?php echo e(__('Account')); ?></span> <i class="las la-user"></i> </a>
        <ul class="account-list-item mt-2">
            <li class="list text-light"> <a href="<?php echo e(route('user.register')); ?>"> <?php echo e(__('Sign Up')); ?> </a> </li>
            <li class="list text-light"> <a href="<?php echo e(route('user.login')); ?>"><?php echo e(__('Sign In')); ?> </a> </li>
        </ul>
    </div>
<?php endif; ?>


<?php /**PATH D:\CODE\jasa\resources\views/components/frontend/user-menu.blade.php ENDPATH**/ ?>