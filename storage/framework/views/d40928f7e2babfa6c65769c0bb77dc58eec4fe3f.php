<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('frontend.partials.pages-portion.dynamic-page-builder-part',['page_post' => $page_details], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
    $('#banner-slider-custom').owlCarousel({
            loop: true,
            autoplay: true,
            margin: 15,
            nav: false,
            // navText: ["<i class='czi-arrow-left'></i>","<i class='czi-arrow-right'></i>"],
            dots: true,
            autoplayHoverPause: true,
            // center: true,
            responsive: {
                //X-Small
                0: {
                    items: 1
                },
                360: {
                    items: 1
                },
                375: {
                    items: 1
                },
                540: {
                    items: 1
                },
                //Small
                576: {
                    items: 1
                },
                //Medium
                768: {
                    items: 1.2
                },
                //Large
                992: {
                    items: 1.3
                },
                //Extra large
                1200: {
                    items: 1.4
                },
                //Extra extra large
                1400: {
                    items: 1.5
                }
            }
        })
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('frontend.frontend-master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\CODE\jasa\resources\views/frontend/frontend-home.blade.php ENDPATH**/ ?>