<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use Illuminate\Http\Request;

/**
 * NotificationTemplatesController — تعديل نصوص الإشعارات (عربي/إنجليزي) من الإدارة.
 * القيم الافتراضية من ملفات lang؛ المحفوظ في الجدول يتجاوزها، والحذف يعيد الافتراضي.
 */
class NotificationTemplatesController extends Controller
{
    public function index()
    {
        $defaults = ['ar' => require lang_path('ar/notifications.php'), 'en' => require lang_path('en/notifications.php')];
        $overrides = NotificationTemplate::overrides();

        $keys = collect(array_keys($defaults['ar']))->merge(array_keys($defaults['en']))->unique()->values();

        $templates = $keys->map(function ($key) use ($defaults, $overrides) {
            $ar = $defaults['ar'][$key] ?? '';
            $en = $defaults['en'][$key] ?? '';
            preg_match_all('/:([A-Za-z_]+)/u', $ar . ' ' . $en, $m);

            return (object) [
                'key'          => $key,
                'default_ar'   => $ar,
                'default_en'   => $en,
                'text_ar'      => $overrides[$key]['ar'] ?? null,
                'text_en'      => $overrides[$key]['en'] ?? null,
                'placeholders' => array_values(array_unique($m[1])),
                'customized'   => isset($overrides[$key]),
            ];
        });

        return view('dashboard.notificationtemplates.index', compact('templates'));
    }

    public function update(Request $request, string $key)
    {
        $request->validate([
            'text_ar' => 'nullable|string|max:1000',
            'text_en' => 'nullable|string|max:1000',
        ]);

        $known = array_merge(
            array_keys(require lang_path('ar/notifications.php')),
            array_keys(require lang_path('en/notifications.php'))
        );
        abort_unless(in_array($key, $known, true), 404);

        if (!$request->filled('text_ar') && !$request->filled('text_en')) {
            NotificationTemplate::where('key', $key)->delete();
        } else {
            NotificationTemplate::updateOrCreate(['key' => $key], [
                'text_ar' => $request->text_ar ?: null,
                'text_en' => $request->text_en ?: null,
            ]);
        }

        return back()->with('success', __('dashboard.Template_Saved'));
    }

    public function reset(string $key)
    {
        NotificationTemplate::where('key', $key)->delete();

        return back()->with('success', __('dashboard.Template_Reset'));
    }
}
