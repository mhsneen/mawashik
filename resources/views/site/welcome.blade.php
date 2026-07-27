<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>متجرك الشامل | كل ما تحتاجه في مكان واحد</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --accent: #f59e0b;
            --dark: #0f172a;
            --light: #f8fafc;
            --gray: #64748b;
            --border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        /* Top Bar */
        .top-bar {
            background: var(--dark);
            color: #fff;
            text-align: center;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        .top-bar span {
            color: var(--accent);
            font-weight: bold;
        }

        /* Header */
        header {
            background: #fff;
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }
        .btn-header {
            background: var(--primary);
            color: #fff;
            padding: 8px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
        }
        .btn-header:hover { background: var(--primary-hover); }

        /* Hero Section */
        .hero {
            padding: 60px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .hero-text { flex: 1; }
        .hero-text h1 {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 15px;
        }
        .hero-text p {
            color: var(--gray);
            font-size: 1.1rem;
            margin-bottom: 25px;
        }
        .btn-hero {
            background: var(--accent);
            color: #fff;
            padding: 14px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
            transition: 0.3s;
        }
        .btn-hero:hover { transform: translateY(-2px); }
        .hero-image { flex: 1; text-align: center; }
        .hero-image img {
            width: 100%;
            max-width: 500px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        /* Features */
        .features {
            background: #fff;
            padding: 30px 5%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto 50px auto;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        }
        .feature-item {
            text-align: center;
            padding: 15px;
        }
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .feature-item h4 { font-size: 1rem; font-weight: 700; }
        .feature-item p { font-size: 0.85rem; color: var(--gray); }

        /* Section Titles */
        .section-title {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 30px;
            font-weight: 800;
        }

        /* Best Sellers */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            max-width: 1200px;
            margin: 0 auto 60px auto;
            padding: 0 5%;
        }
        .product-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            transition: 0.3s;
        }
        .product-card:hover {
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            transform: translateY(-4px);
        }
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .product-card h3 { font-size: 1.1rem; margin-bottom: 8px; }
        .price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 15px;
        }
        .price old {
            font-size: 0.9rem;
            color: var(--gray);
            text-decoration: line-through;
            margin-left: 8px;
        }
        .btn-card {
            background: var(--dark);
            color: #fff;
            border: none;
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-card:hover { background: var(--primary); }

        /* Testimonials */
        .testimonials {
            background: #fff;
            padding: 60px 5%;
            margin-bottom: 60px;
        }
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .testimonial-card {
            background: var(--light);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid var(--border);
        }
        .testimonial-card p { font-style: italic; margin-bottom: 15px; }
        .testimonial-card h5 { font-weight: 700; color: var(--primary); }

        /* Footer */
        footer {
            background: var(--dark);
            color: #fff;
            text-align: center;
            padding: 30px 5%;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero {
                flex-direction: column;
                text-align: center;
                padding: 40px 5%;
            }
            .hero-text h1 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

    <!-- Top Announcement Bar -->
    <div class="top-bar">
        🚚 شحن مجاني للطلبات الأعلى من $50! استخدم كود الخصم: <span>FIRST10</span>
    </div>

    <!-- Header -->
    <header>
        <div class="logo">متجري</div>
        <a href="#products" class="btn-header">تسوق الآن</a>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-text">
            <h1>كل ما تحتاجه حياتك اليومية.. في مكان واحد!</h1>
            <p>اكتشف تشكيلة ممتازة من أحدث المنتجات الأكثر مبيعاً بأفضل الأسعار والجودة العالية. توصيل سريع حتى باب بيتك.</p>
            <a href="#products" class="btn-hero">استكشف العروض الحصرية ⚡</a>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=600&auto=format&fit=crop&q=80" alt="منتجات المتجر">
        </div>
    </section>

    <!-- Trust Features -->
    <div class="features">
        <div class="feature-item">
            <div class="feature-icon">🚚</div>
            <h4>توصيل سريع</h4>
            <p>خلال 2 - 4 أيام عمل</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">💳</div>
            <h4>دفع آمن</h4>
            <p>عند الاستلام أو بالبطاقة</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🔄</div>
            <h4>استرجاع سهل</h4>
            <p>ضمان لمدة 14 يوماً</p>
        </div>
        <div class="feature-item">
            <div class="feature-icon">🎧</div>
            <h4>دعم متواصل</h4>
            <p>فريقنا جاهز لمساعدتك</p>
        </div>
    </div>



    <!-- Testimonials -->
    <section class="testimonials">
        <h2 class="section-title">آراء العملاء 💬</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <p>"المنتجات وصلتني بتغليف ممتاز وفي وقت قياسي. الجودة أفضل مما توقعت!"</p>
                <h5>- أحمد م. ⭐⭐⭐⭐⭐</h5>
            </div>
            <div class="testimonial-card">
                <p>"تجربة شراء سهلة جداً وسرعة في الرد عبر الواتساب. حتماً سأكرر الطلب."</p>
                <h5>- سارة ك. ⭐⭐⭐⭐⭐</h5>
            </div>
            <div class="testimonial-card">
                <p>"خدمة الدفع عند الاستلام ريحتني، والمنتج يعمل بجودة ممتازة."</p>
                <h5>- خالد ع. ⭐⭐⭐⭐⭐</h5>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>جميع الحقوق محفوظة © 2026 - متجرك الشامل</p>
    </footer>

</body>
</html>
