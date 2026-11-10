<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['store_name']->value ?? config('app.name') }}</title>
    <link rel="icon" href="{{ URL::asset('uploads/settings/thumbnail/' . ($settings['store_logo']->plain_value ?? '')) }}">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { 'sans': ['Cairo', 'sans-serif'] },
                    colors: {
                        'brand': '#059669',       // أخضر زمردي احترافي
                        'brand-dark': '#064e3b',   // أخضر داكن
                        'brand-accent': '#f59e0b', // ذهبي للإبراز
                        'dark': '#0f172a',
                        'light': '#f8fafc',
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        body { background-color: #f1f5f9; }
        .hero-gradient { background: radial-gradient(circle at top right, #ecfdf5 0%, #ffffff 60%); }
        .card-hover { transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1); }
        .card-hover:hover { transform: translateY(-10px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        .product-card img { transition: transform 0.5s ease; }
        .product-card:hover img { transform: scale(1.08); }
        .floating { animation: floating 3s ease-in-out infinite; }
        @keyframes floating { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }
    </style>
</head>
<body class="font-sans text-slate-800 overflow-x-hidden">

    <!-- ===================== Navbar ===================== -->
    <header class="bg-white/80 backdrop-blur-lg sticky top-0 z-50 border-b border-slate-100">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                @if (!empty($settings['store_logo']->plain_value ?? null))
                    <img src="{{ URL::asset('uploads/settings/thumbnail/' . $settings['store_logo']->plain_value) }}" alt="logo" class="h-12 w-12 object-contain rounded-xl">
                @endif
                <span class="text-2xl font-extrabold text-brand-dark">{{ $settings['store_name']->value ?? '' }}</span>
            </div>
            <a href="#download-app" class="bg-brand text-white px-6 py-2.5 rounded-full font-bold hover:bg-brand-dark transition shadow-lg shadow-brand/30">
                حمّل التطبيق الآن
            </a>
        </div>
    </header>

    <!-- ===================== Hero Section ===================== -->
    <section class="hero-gradient py-20 md:py-32 relative overflow-hidden">
        <div class="container mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-12">
            <div class="md:w-1/2 text-center md:text-right z-10">
                <span class="inline-block bg-brand-accent/10 text-brand-accent px-4 py-1 rounded-full text-sm font-bold mb-4">🚀 توصيل سريع لكل مدن المملكة</span>
                <h1 class="text-4xl md:text-6xl font-extrabold text-dark leading-tight mb-6">
                    تسوق ذكي، حياة أسهل <br> مع تطبيق <span class="text-brand">{{ $settings['store_name']->value ?? '' }}</span>
                </h1>
                <p class="text-lg text-slate-500 mb-8 leading-relaxed max-w-xl mx-auto md:mx-0">
                    اكتشف مجموعة واسعة من المنتجات الطازجة والأساسيات اليومية. اطلب الآن ووصلك لباب البيت بأفضل الأسعار وبضغطة زر واحدة.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    @if (!empty($settings['appStorePageLink']->plain_value ?? null))
                        <a href="{{ $settings['appStorePageLink']->plain_value }}" target="_blank" class="bg-dark text-white px-6 py-3 rounded-xl flex items-center gap-3 hover:bg-slate-800 transition shadow-xl">
                            <i class="fab fa-apple text-3xl"></i>
                            <div class="text-right">
                                <div class="text-xs opacity-80">حمّل من</div>
                                <div class="text-lg font-bold">App Store</div>
                            </div>
                        </a>
                    @endif
                    @if (!empty($settings['googlePlayPageLink']->plain_value ?? null))
                        <a href="{{ $settings['googlePlayPageLink']->plain_value }}" target="_blank" class="bg-dark text-white px-6 py-3 rounded-xl flex items-center gap-3 hover:bg-slate-800 transition shadow-xl">
                            <i class="fab fa-google-play text-3xl"></i>
                            <div class="text-right">
                                <div class="text-xs opacity-80">حمّل من</div>
                                <div class="text-lg font-bold">Google Play</div>
                            </div>
                        </a>
                    @endif
                </div>
            </div>
            <div class="md:w-1/2 flex justify-center">
                @if (!empty($settings['store_logo']->plain_value ?? null))
                    <div class="relative">
                        <div class="absolute inset-0 bg-brand/20 rounded-full blur-3xl animate-pulse"></div>
                        <img src="{{ URL::asset('uploads/settings/thumbnail/' . $settings['store_logo']->plain_value) }}" class="relative w-64 h-64 object-contain floating drop-shadow-2xl" alt="app icon">
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- ===================== Features ===================== -->
    @if (
        !empty($settings['free_shipping_sentence']->value ?? null) ||
        !empty($settings['return_policy_sentence']->value ?? null) ||
        !empty($settings['secure_payment_sentence']->value ?? null)
    )
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            @if (!empty($settings['free_shipping_sentence']->value ?? null))
                <div class="bg-light p-8 rounded-3xl text-center card-hover border border-slate-100">
                    <div class="w-16 h-16 bg-brand/10 text-brand rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-2">{{ $settings['free_shipping_sentence']->value }}</h4>
                    <p class="text-slate-500 text-sm">{!! $settings['free_shipping_details']->value ?? '' !!}</p>
                </div>
            @endif

            @if (!empty($settings['return_policy_sentence']->value ?? null))
                <div class="bg-light p-8 rounded-3xl text-center card-hover border border-slate-100">
                    <div class="w-16 h-16 bg-brand/10 text-brand rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-undo-alt"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-2">{{ $settings['return_policy_sentence']->value }}</h4>
                    <p class="text-slate-500 text-sm">{!! $settings['return_policy_details']->value ?? '' !!}</p>
                </div>
            @endif

            @if (!empty($settings['secure_payment_sentence']->value ?? null))
                <div class="bg-light p-8 rounded-3xl text-center card-hover border border-slate-100">
                    <div class="w-16 h-16 bg-brand/10 text-brand rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-2">{{ $settings['secure_payment_sentence']->value }}</h4>
                    <p class="text-slate-500 text-sm">{!! $settings['secure_payment_details']->value ?? '' !!}</p>
                </div>
            @endif
        </div>
    </section>
    @endif

    <!-- ===================== Categories ===================== -->
    @if(!$categories->isEmpty())
    <section class="py-16 bg-light">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-dark">تسوق حسب القسم</h2>
                <p class="text-slate-500 mt-2">اختر القسم الذي تريده وابدأ التسوق</p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach($categories as $category)
                <div class="bg-white p-6 rounded-3xl shadow-sm card-hover text-center cursor-pointer group">
                    <div class="w-20 h-20 bg-brand/5 rounded-2xl flex items-center justify-center mx-auto mb-4 overflow-hidden transition group-hover:bg-brand/10">
                        @if($category->image_url)
                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                        @else
                            <i class="fas fa-shopping-basket text-2xl text-brand"></i>
                        @endif
                    </div>
                    <h3 class="font-bold text-slate-700">{{ $category->name }}</h3>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ===================== Products ===================== -->
    @if(!$products->isEmpty())
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-4">
                <div>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-dark">منتجات مميزة</h2>
                    <p class="text-slate-500 mt-2">أفضل المنتجات الطازجة بانتظارك في تطبيقنا</p>
                </div>
                <span class="bg-brand-accent/10 text-brand-accent px-4 py-2 rounded-full font-bold text-sm">الشراء يتم عبر التطبيق 📱</span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($products as $product)
                <div class="product-card bg-light rounded-3xl overflow-hidden border border-slate-100 group">
                    <div class="h-52 bg-slate-200 overflow-hidden relative">
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400"><i class="fas fa-image text-5xl"></i></div>
                        @endif

                        @if($product->is_on_sale)
                        <span class="absolute top-3 right-3 bg-red-500 text-white text-xs px-3 py-1 rounded-full font-bold shadow-md">خصم {{ $product->salePrice }}%</span>
                        @endif
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-lg text-dark mb-3 truncate">{{ $product->name }}</h3>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex flex-col">
                                @if($product->is_on_sale)
                                    <span class="text-sm text-slate-400 line-through">{{ number_format($product->netPrice, 2) }} ر.س</span>
                                @endif
                                <span class="text-xl font-extrabold text-brand">{{ number_format($product->final_price, 2) }} ر.س</span>
                            </div>
                        </div>
                        <a href="#download-app" class="w-full bg-dark text-white py-3 rounded-xl font-bold hover:bg-brand transition flex items-center justify-center gap-2">
                            <i class="fas fa-cart-arrow-down"></i> اطلب الآن
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- ===================== Download App CTA ===================== -->
    <section id="download-app" class="py-20 bg-gradient-to-br from-brand-dark to-brand text-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-brand-accent/20 rounded-full blur-3xl"></div>
        <div class="container mx-auto px-6 text-center relative z-10">
            <h2 class="text-3xl md:text-5xl font-extrabold mb-4">حمّل تطبيق "{{ $settings['store_name']->value ?? '' }}" الآن!</h2>
            <p class="text-slate-200 mb-8 max-w-2xl mx-auto text-lg">استمتع بتجربة تسوق سهلة وسريعة. اطلب ما تريد في أي وقت ومن أي مكان، ونحن نوصله لك.</p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @if (!empty($settings['appStorePageLink']->plain_value ?? null))
                    <a href="{{ $settings['appStorePageLink']->plain_value }}" target="_blank" class="bg-white text-dark px-8 py-4 rounded-xl font-bold hover:bg-slate-100 transition flex items-center justify-center gap-3 shadow-xl">
                        <i class="fab fa-apple text-3xl"></i> حمّل من App Store
                    </a>
                @endif
                @if (!empty($settings['googlePlayPageLink']->plain_value ?? null))
                    <a href="{{ $settings['googlePlayPageLink']->plain_value }}" target="_blank" class="bg-white text-dark px-8 py-4 rounded-xl font-bold hover:bg-slate-100 transition flex items-center justify-center gap-3 shadow-xl">
                        <i class="fab fa-google-play text-3xl"></i> حمّل من Google Play
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- ===================== Contact ===================== -->
    <section class="py-16 bg-light">
        <div class="container mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-dark">تواصل معنا</h2>
                <p class="text-slate-500 mt-2">نحن هنا لمساعدتك دائماً</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl mx-auto">
                @if (!empty($settings['store_mobile']->plain_value ?? null))
                    <div class="bg-white p-6 rounded-3xl text-center card-hover border border-slate-100">
                        <div class="w-14 h-14 bg-brand/10 text-brand rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl"><i class="fas fa-phone-alt"></i></div>
                        <h4 class="font-bold mb-2">اتصل بنا</h4>
                        <a href="tel:{{ $settings['store_mobile']->plain_value }}" class="text-slate-500 hover:text-brand">{{ $settings['store_mobile']->plain_value }}</a>
                    </div>
                @endif
                @if (!empty($settings['store_whatsapp']->plain_value ?? null))
                    <div class="bg-white p-6 rounded-3xl text-center card-hover border border-slate-100">
                        <div class="w-14 h-14 bg-green-100 text-green-500 rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl"><i class="fab fa-whatsapp"></i></div>
                        <h4 class="font-bold mb-2">واتساب</h4>
                        <a href="https://wa.me/{{ ltrim($settings['store_whatsapp']->plain_value, '0+') }}" target="_blank" class="text-slate-500 hover:text-brand">{{ $settings['store_whatsapp']->plain_value }}</a>
                    </div>
                @endif
                @if (!empty($settings['store_email']->plain_value ?? null))
                    <div class="bg-white p-6 rounded-3xl text-center card-hover border border-slate-100">
                        <div class="w-14 h-14 bg-brand/10 text-brand rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl"><i class="fas fa-envelope"></i></div>
                        <h4 class="font-bold mb-2">البريد الإلكتروني</h4>
                        <a href="mailto:{{ $settings['store_email']->plain_value }}" class="text-slate-500 hover:text-brand break-all">{{ $settings['store_email']->plain_value }}</a>
                    </div>
                @endif
                @if (!empty($settings['store_address']->value ?? null))
                    <div class="bg-white p-6 rounded-3xl text-center card-hover border border-slate-100">
                        <div class="w-14 h-14 bg-brand/10 text-brand rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl"><i class="fas fa-map-marker-alt"></i></div>
                        <h4 class="font-bold mb-2">العنوان</h4>
                        <span class="text-slate-500">{{ $settings['store_address']->value }}</span>
                    </div>
                @endif
            </div>

            @if ($socialLinks->isNotEmpty())
                <div class="flex justify-center gap-4 mt-12">
                    @foreach ($socialLinks as $link)
                        <a href="{{ $link->link }}" target="_blank" title="{{ $link->name }}" class="w-12 h-12 bg-white text-brand rounded-2xl flex items-center justify-center shadow-sm hover:bg-brand hover:text-white transition border border-slate-100">
                            @if (!empty($link->icon))
                                <img src="{{ asset('uploads/socialmedialinks/' . $link->icon) }}" alt="{{ $link->name }}" class="w-6 h-6">
                            @else
                                <i class="fas fa-link"></i>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- ===================== Footer ===================== -->
    <footer class="bg-dark text-white py-12">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    @if (!empty($settings['store_logo']->plain_value ?? null))
                        <img src="{{ URL::asset('uploads/settings/thumbnail/' . $settings['store_logo']->plain_value) }}" alt="logo" class="h-10 w-10 object-contain">
                    @endif
                    <span class="text-xl font-extrabold">{{ $settings['store_name']->value ?? '' }}</span>
                </div>
                <p class="text-slate-400 text-sm">وجهتك الأولى للتسوق أونلاين بأسعار تنافسية وتوصيل سريع.</p>
            </div>
            <div>
                <h4 class="font-bold mb-4">روابط سريعة</h4>
                <ul class="space-y-2 text-slate-400 text-sm">
                    <li><a href="#" class="hover:text-brand transition">الرئيسية</a></li>
                    <li><a href="#" class="hover:text-brand transition">من نحن</a></li>
                    <li><a href="#" class="hover:text-brand transition">سياسة الاسترجاع</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold mb-4">تواصل معنا</h4>
                <ul class="space-y-2 text-slate-400 text-sm">
                    <li><i class="fas fa-phone ml-2"></i> {{ $settings['store_mobile']->plain_value ?? '' }}</li>
                    <li><i class="fas fa-envelope ml-2"></i> {{ $settings['store_email']->plain_value ?? '' }}</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 pt-8 text-center text-slate-500 text-sm">
            <p>© {{ date('Y') }} {{ $settings['store_name']->value ?? '' }} — {{ __('dashboard.CopyRight_footer') }}</p>
        </div>
    </footer>

</body>
</html>
