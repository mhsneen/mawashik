<?php

namespace App\Services\Branches;

use App\Models\Branch;
use Illuminate\Support\Facades\Cache;

/**
 * نقطة القرار الوحيدة في كل المشروع لسؤال "أي فرع؟".
 *
 * القاعدة الذهبية: أي كود (كونترولر، API، طلب جديد، تقرير...) يحتاج يعرف الفرع الحالي يجب أن
 * يستدعي BranchContext::current() ولا يفترض/يكتب فرع بشكل مباشر (hardcoded). هذا يخليك تبدّل
 * "كيف يتحدد الفرع" بمكان واحد بس لما يصير عندك أكثر من فرع فعلياً، بدون ما تلمس باقي الكود.
 *
 * =============================================================================================
 * المرحلة الحالية (فرع واحد): دايماً يرجع الفرع الرئيسي. بسيط ومضمون.
 *
 * لما يُطلب منك نظام فروع فعلي مستقبلاً، التغيير يكون هنا فقط، باختيار واحد من الأساليب التالية
 * (أو مزيج منها) بدل current() الحالية - بدون أي تعديل في باقي الكونترولرز:
 *
 *   - متجر إلكتروني عادي (المستخدم يختار فرعه): اقرأ من session('branch_id') بعد شاشة اختيار فرع،
 *     مع fallback لأقرب فرع جغرافياً عبر lat/lng لو أرسل الموقع.
 *   - تطبيق موبايل: اقرأ من Header يرسله التطبيق (X-Branch-Id) بعد شاشة اختيار الفرع.
 *   - لوحة تحكم/موظف: اقرأ من branch_id المخزّن على حساب الموظف نفسه (auth()->user()->branch_id).
 *   - نطاق فرعي لكل فرع (sub.domain.com): اقرأ الفرع حسب الـ subdomain الحالي.
 * =============================================================================================
 */
class BranchContext
{
    protected static ?Branch $resolved = null;

    public static function current(): Branch
    {
        if (static::$resolved) {
            return static::$resolved;
        }

        return static::$resolved = static::mainBranch();
    }

    public static function currentId(): int
    {
        return static::current()->id;
    }

    /** يُستخدم في الاختبارات أو الأوامر التي تحتاج تفرض فرعاً معيناً يدوياً. */
    public static function setCurrent(Branch $branch): void
    {
        static::$resolved = $branch;
    }

    public static function mainBranch(): Branch
    {
        return Cache::remember('branch:main', 3600, function () {
            return Branch::main()->active()->first()
                ?? Branch::main()->first()
                ?? Branch::first();
        });
    }

    public static function forgetCache(): void
    {
        Cache::forget('branch:main');
    }

    /** هل النظام يشتغل بأكثر من فرع فعّال حالياً؟ يفيد لإخفاء/إظهار واجهات الفروع بالأدمن. */
    public static function isMultiBranch(): bool
    {
        return Branch::active()->count() > 1;
    }
}
