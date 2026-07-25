<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PaymentMethodRequest;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodsController extends Controller
{
    protected string $routeName = 'paymentmethods';
    protected string $namesection = 'dashboard.Payment_Methods';

    public function __construct()
    {
        $this->middleware('permission:Payment_Methods');
    }

    /** قائمة كل بوابات الدفع (المسجّلة في config/payment_gateways.php) مع حالتها الحالية */
    public function index()
    {
        $this->syncCatalogIntoDatabase();

        $items = PaymentMethod::orderBy('sort_order')->get();

        $data = [
            'items'       => $items,
            'routeName'   => $this->routeName,
            'namesection' => $this->namesection,
        ];

        return view('dashboard.paymentmethods.index', $data);
    }

    public function edit(string $code)
    {
        $item = PaymentMethod::where('code', $code)->firstOrFail();
        $catalog = $item->catalog();

        if (empty($catalog)) {
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
        }

        $data = [
            'item'        => $item,
            'catalog'     => $catalog,
            'routeName'   => $this->routeName,
            'namesection' => $this->namesection,
        ];

        return view('dashboard.paymentmethods.edit', $data);
    }

    public function update(PaymentMethodRequest $request, string $code)
    {
        $item = PaymentMethod::where('code', $code)->firstOrFail();
        $catalog = $item->catalog();

        $config = [];
        foreach (array_keys($catalog['fields'] ?? []) as $fieldKey) {
            if ($request->filled($fieldKey)) {
                $config[$fieldKey] = $request->input($fieldKey);
            } elseif (isset($item->config[$fieldKey])) {
                // إبقاء القيمة القديمة إن ترك الأدمن حقل الباسورد فارغاً عند التعديل
                $config[$fieldKey] = $item->config[$fieldKey];
            }
        }

        $item->update([
            'mode'       => $request->input('mode', 'test'),
            'config'     => $config,
            'is_enabled' => $request->boolean('is_enabled'),
        ]);

        return redirect()->route($this->routeName . '.edit', $code)
            ->with(['success' => __('dashboard.Edit_success')]);
    }

    /** تفعيل/تعطيل سريع من شاشة القائمة (AJAX) */
    public function toggle(Request $request, string $code)
    {
        $item = PaymentMethod::where('code', $code)->firstOrFail();

        if ($request->boolean('enable') && empty($item->config)) {
            return response()->json([
                'success' => false,
                'message' => __('dashboard.Payment_Method_Need_Config'),
            ], 422);
        }

        $item->update(['is_enabled' => $request->boolean('enable')]);

        return response()->json(['success' => true]);
    }

    /** يضمن وجود صف لكل بوابة معرّفة في config/payment_gateways.php حتى لو أُضيفت بوابة جديدة لاحقاً بالكود */
    protected function syncCatalogIntoDatabase(): void
    {
        $catalog = config('payment_gateways', []);
        $sort = 0;
        foreach (array_keys($catalog) as $code) {
            PaymentMethod::firstOrCreate(
                ['code' => $code],
                ['is_enabled' => false, 'mode' => 'test', 'sort_order' => $sort]
            );
            $sort++;
        }
    }
}
