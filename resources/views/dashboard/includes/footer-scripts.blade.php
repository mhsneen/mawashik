<!-- Back-to-top -->
<a href="#top" id="back-to-top"><i class="las la-angle-double-up"></i></a>
<!-- JQuery min js -->
<script src="{{URL::asset('assets/dashboard/plugins/jquery/jquery.min.js')}}"></script>
<!-- Bootstrap Bundle js -->
<script src="{{URL::asset('assets/dashboard/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- Ionicons js -->
<script src="{{URL::asset('assets/dashboard/plugins/ionicons/ionicons.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/dashboard/plugins/moment/moment.js')}}"></script>

<!-- Rating js-->
<script src="{{URL::asset('assets/dashboard/plugins/rating/jquery.rating-stars.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/rating/jquery.barrating.js')}}"></script>

<!--Internal  Perfect-scrollbar js -->
<script src="{{URL::asset('assets/dashboard/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/perfect-scrollbar/p-scroll.js')}}"></script>
<!--Internal Sparkline js -->
<script src="{{URL::asset('assets/dashboard/plugins/jquery-sparkline/jquery.sparkline.min.js')}}"></script>
<!-- Custom Scroll bar Js-->
<script src="{{URL::asset('assets/dashboard/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js')}}"></script>
<!-- right-sidebar js -->
<script src="{{URL::asset('assets/dashboard/plugins/sidebar/'.getSidebarfooter().'.js')}}"></script>
<script src="{{URL::asset('assets/dashboard/plugins/sidebar/sidebar-custom.js')}}"></script>
<!-- Eva-icons js -->
<script src="{{URL::asset('assets/dashboard/js/eva-icons.min.js')}}"></script>
@yield('js')
<!-- Sticky js -->
<script src="{{URL::asset('assets/dashboard/js/sticky.js')}}"></script>
<!-- custom js -->
<script src="{{URL::asset('assets/dashboard/js/custom.js')}}"></script><!-- Left-menu js-->
<script src="{{URL::asset('assets/dashboard/plugins/side-menu/sidemenu.js')}}"></script>


<script>
    // ✅ نمرر نصاً وهمياً (PLACEHOLDER) بدل فراغ لتجنب خطأ Missing parameter
    const orderShowBaseUrl = "{{ route('orders.show', ['id' => 'PLACEHOLDER']) }}";

    // ✅ إعداد ملف الصوت (تأكد من وضع ملف صوتي في المسار public/sounds/notification.mp3)
    const notifSound = new Audio('/sounds/notification.mp3');
    let lastNotifId = 0;
    let isFirstLoad = true;

    // دالة تشغيل الصوت
    function playNotificationSound() {
        notifSound.currentTime = 0; // إعادة التشغيل من البداية إذا تم تشغيله مؤخراً
        notifSound.play().catch(e => {
            console.log("المتصفح منع تشغيل الصوت تلقائياً. يجب أن يتفاعل المستخدم مع الصفحة أولاً.");
        });
    }

   function loadNotifications() {
    $.get("{{ route('admin.notifications.fetch') }}", function (data) {
        if (data.unread_count > 0) {
            $('#notifCount').text(data.unread_count).show();
        } else {
            $('#notifCount').hide();
        }

        let html = '';
        let currentMaxId = lastNotifId;

        data.items.forEach(item => {
            if (item.id > currentMaxId) {
                currentMaxId = item.id;
            }

            let orderUrl = orderShowBaseUrl.replace('PLACEHOLDER', item.order_id);
            let orderLabel = item.order_number ? ('#' + item.order_number) : '';

            html += `<a class="d-flex p-3 border-bottom ${item.is_read ? '' : 'bg-light'}" href="${orderUrl}">
                <div class="mr-3 w-100">
                    <h6 class="mb-1">${item.title} ${orderLabel}</h6>
                    <p class="mb-1 tx-13 text-muted">${item.body ?? ''}</p>
                    <div class="notification-subtext">${item.time}</div>
                </div>
            </a>`;
        });

        $('#notifList').html(html || '<p class="p-3 text-muted">{{ __('dashboard.No_Data_in_this_category') }}</p>');

        if (currentMaxId > lastNotifId && !isFirstLoad) {
            playNotificationSound();
        }

        lastNotifId = currentMaxId;

        if (isFirstLoad) {
            isFirstLoad = false;
        }
    });
}

    // ✅ فك حظر الصوت في المتصفحات (Chrome يتطلب تفاعل من المستخدم أولاً)
    // هذه الدالة ستشغل الصوت بصمت 0 عند أول نقرة بالماوس لتسمح بتشغيله لاحقاً تلقائياً
    $(document).one('click', function() {
        notifSound.volume = 0;
        notifSound.play().then(() => {
            notifSound.pause();
            notifSound.currentTime = 0;
            notifSound.volume = 1;
        }).catch(() => {});
    });

    loadNotifications();
    setInterval(loadNotifications, 20000); // كل 20 ثانية

    $('#notifBell').on('click', function () {
        $.post("{{ route('admin.notifications.markRead') }}", { _token: "{{ csrf_token() }}" }, loadNotifications);
    });
</script>
