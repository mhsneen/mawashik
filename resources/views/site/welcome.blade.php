<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings['store_name']->value ?? config('app.name') }}</title>
    <link rel="icon" href="{{ URL::asset('uploads/settings/thumbnail/' . ($settings['store_logo']->plain_value ?? '')) }}">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- إضافة مكتبة الأيقونات Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --dark: #0f172a;
            --light: #f8fafc;
            --gray: #64748b;
            --border: #e2e8f0;
            --shadow-sm: 0 2px 10px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 30px rgba(0,0,0,0.1);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Cairo', sans-serif; }
        body { background-color: var(--light); color: var(--dark); line-height: 1.6; }

        header {
            background: #fff;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        header img { height: 45px; width: 45px; object-fit: contain; border-radius: 8px; }
        .logo-text { font-size: 1.4rem; font-weight: 800; color: var(--primary); }

        .hero {
            padding: 80px 5% 60px;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
            background: linear-gradient(180deg, #ffffff 0%, var(--light) 100%);
        }
        .hero img.logo-hero {
            width: 120px; height: 120px; object-fit: contain;
            margin-bottom: 25px; border-radius: 20px;

        }
        .hero h1 { font-size: 2.5rem; font-weight: 800; margin-bottom: 15px; color: var(--dark); }
        .hero p { color: var(--gray); font-size: 1.2rem; }

        /* قسم المميزات */
        .features {
            background: #fff;
            padding: 40px 5%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            max-width: 1100px;
            margin: 0 auto 60px auto;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
        }
        .feature-item {
            text-align: center; padding: 25px 15px;
            border-radius: 12px;
            transition: transform 0.3s ease, background 0.3s ease;
        }
        .feature-item:hover {
            transform: translateY(-5px);
            background: var(--light);
        }
        .feature-icon {
            font-size: 2.5rem; margin-bottom: 15px;
            width: 70px; height: 70px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px;
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            border-radius: 50%;
        }
        .feature-item h4 { font-size: 1.1rem; font-weight: 700; margin-bottom: 8px; }
        .feature-item p { font-size: 0.95rem; color: var(--gray); }

        /* قسم تطبيقات الجوال */
        .apps-section {
            text-align: center;
            margin: 0 auto 80px;
            padding: 0 5%;
        }
        .apps-section h3 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 25px;
            color: var(--dark);
        }
        .app-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .app-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: var(--dark);
            color: #fff;
            padding: 12px 25px;
            border-radius: 12px;
            text-decoration: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: var(--shadow-sm);
        }
        .app-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            background: #000;
        }
        .app-btn i { font-size: 2rem; }
        .app-btn .text-container { text-align: right; line-height: 1.2; }
        .app-btn .small-text { font-size: 0.7rem; opacity: 0.8; }
        .app-btn .big-text { font-size: 1.1rem; font-weight: 700; }

        /* قسم التواصل */
        .contact-section {
            max-width: 900px;
            margin: 0 auto 80px auto;
            padding: 0 5%;
            text-align: center;
        }
        .contact-section h2 { font-size: 1.8rem; font-weight: 800; margin-bottom: 30px; }
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .contact-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 25px 20px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .contact-card:hover {
            border-color: var(--primary);
            box-shadow: var(--shadow-md);
        }
        .contact-card i { font-size: 1.8rem; color: var(--primary); margin-bottom: 15px; display: block; }
        .contact-card a, .contact-card span { color: var(--dark); text-decoration: none; font-weight: 600; font-size: 1rem; }
        .contact-card a:hover { color: var(--primary); }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 40px;
        }
        .social-links a {
            width: 50px; height: 50px;
            display: flex; align-items: center; justify-content: center;
            background: #fff; color: var(--primary); border-radius: 50%;
            text-decoration: none; transition: 0.3s;
            border: 1px solid var(--border);
            font-size: 1.3rem;
        }
        .social-links a:hover {
            background: var(--primary); color: #fff; transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }
        .social-links img { width: 24px; height: 24px; }

        footer {
            background: var(--dark);
            color: #fff;
            text-align: center;
            padding: 30px 5%;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>

    <header>
        <div style="display:flex; align-items:center; gap:12px;">
            @if (!empty($settings['store_logo']->plain_value ?? null))
                <img src="{{ URL::asset('uploads/settings/thumbnail/' . $settings['store_logo']->plain_value) }}" alt="logo">
            @endif
            <span class="logo-text">{{ $settings['store_name']->value ?? '' }}</span>
        </div>
    </header>

    <section class="hero">
        @if (!empty($settings['store_logo']->plain_value ?? null))
            <img src="{{ URL::asset('uploads/settings/thumbnail/' . $settings['store_logo']->plain_value) }}" class="logo-hero" alt="logo">
        @endif
        <h1>{{ $settings['store_name']->value ?? '' }}</h1>

    </section>

       @if (
        !empty($settings['free_shipping_sentence']->value ?? null) ||
        !empty($settings['return_policy_sentence']->value ?? null) ||
        !empty($settings['secure_payment_sentence']->value ?? null)
    )
        <div class="features">
            @if (!empty($settings['free_shipping_sentence']->value ?? null))
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-shipping-fast"></i></div>
                    <h4>{{ $settings['free_shipping_sentence']->value }}</h4>
                    @if (!empty($settings['free_shipping_details']->value ?? null))
                        <!-- تم التعديل هنا -->
                        <p>{!! $settings['free_shipping_details']->value !!}</p>
                    @endif
                </div>
            @endif

            @if (!empty($settings['return_policy_sentence']->value ?? null))
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-undo-alt"></i></div>
                    <h4>{{ $settings['return_policy_sentence']->value }}</h4>
                    @if (!empty($settings['return_policy_details']->value ?? null))
                        <!-- تم التعديل هنا -->
                        <p>{!! $settings['return_policy_details']->value !!}</p>
                    @endif
                </div>
            @endif

            @if (!empty($settings['secure_payment_sentence']->value ?? null))
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-lock"></i></div>
                    <h4>{{ $settings['secure_payment_sentence']->value }}</h4>
                    @if (!empty($settings['secure_payment_details']->value ?? null))
                        <!-- تم التعديل هنا -->
                        <p>{!! $settings['secure_payment_details']->value !!}</p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    @if (!empty($settings['appStorePageLink']->plain_value ?? null) || !empty($settings['googlePlayPageLink']->plain_value ?? null))
        <section class="apps-section">
            <h3>حمّل تطبيقنا الآن</h3>
            <div class="app-links">
                @if (!empty($settings['appStorePageLink']->plain_value ?? null))
                    <a href="{{ $settings['appStorePageLink']->plain_value }}" target="_blank" class="app-btn">
                        <i class="fab fa-apple"></i>
                        <div class="text-container">
                            <span class="small-text">Download on the</span><br>
                            <span class="big-text">App Store</span>
                        </div>
                    </a>
                @endif

                @if (!empty($settings['googlePlayPageLink']->plain_value ?? null))
                    <a href="{{ $settings['googlePlayPageLink']->plain_value }}" target="_blank" class="app-btn">
                        <i class="fab fa-google-play"></i>
                        <div class="text-container">
                            <span class="small-text">GET IT ON</span><br>
                            <span class="big-text">Google Play</span>
                        </div>
                    </a>
                @endif
            </div>
        </section>
    @endif

    <section class="contact-section">
        <h2>تواصل معنا</h2>
        <div class="contact-grid">
            @if (!empty($settings['store_mobile']->plain_value ?? null))
                <div class="contact-card">
                    <i class="fas fa-phone-alt"></i>
                    <a href="tel:{{ $settings['store_mobile']->plain_value }}">{{ $settings['store_mobile']->plain_value }}</a>
                </div>
            @endif

            @if (!empty($settings['store_whatsapp']->plain_value ?? null))
                <div class="contact-card">
                    <i class="fab fa-whatsapp"></i>
                    <a href="https://wa.me/{{ ltrim($settings['store_whatsapp']->plain_value, '0+') }}" target="_blank">
                        {{ $settings['store_whatsapp']->plain_value }}
                    </a>
                </div>
            @endif

            @if (!empty($settings['store_email']->plain_value ?? null))
                <div class="contact-card">
                    <i class="fas fa-envelope"></i>
                    <a href="mailto:{{ $settings['store_email']->plain_value }}">{{ $settings['store_email']->plain_value }}</a>
                </div>
            @endif

            @if (!empty($settings['store_address']->value ?? null))
                <div class="contact-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $settings['store_address']->value }}</span>
                </div>
            @endif
        </div>

        @if ($socialLinks->isNotEmpty())
            <div class="social-links">
                @foreach ($socialLinks as $link)
                    <a href="{{ $link->link }}" target="_blank" title="{{ $link->name }}">
                        @if (!empty($link->icon))
                            <img src="{{ asset('uploads/socialmedialinks/' . $link->icon) }}" alt="{{ $link->name }}" style="width: 24px; height: 24px;">
                        @else
                            <i class="fas fa-link"></i> <!-- أيقونة احتياطية -->
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <footer>
        <p>© {{ date('Y') }} {{ $settings['store_name']->value ?? '' }} — {{ __('dashboard.CopyRight_footer') }}</p>
    </footer>

</body>
</html>
