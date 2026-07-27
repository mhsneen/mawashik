<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Models\Orderstatus;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\DriverAssignmentService;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\OrdersSearchRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected  $view = "orders";
    protected  $routeName = "orders";


    public function __construct()
    {
        $this->middleware('permission:Orders');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(OrdersSearchRequest $request)
    {



        $items = Order::FilterDashbordAdmin(request(['search_order_number', 'search_client_name', 'search_client_mobile', 'search_status_id', 'search_date_from', 'search_date_to']))
            ->with('user:id,name', 'status:id', 'coupon:id,code,discount_percentage')
            ->select('id', 'user_id', 'total_items',  'status_id', 'currency', 'read', 'created_at', 'total_price', 'discount_amount', 'coupon_id', 'added_tax', 'final_price')->latest('id')->paginate(PAGANATION_COUNT)->withQueryString();
        $orderstatus = Orderstatus::select('id')->orderBy('id', 'asc')->get();

        $data = [
            'items' => $items,
            'orderstatus' => $orderstatus,
            'routeName' => $this->routeName,
            'request_search' => $request->all()
        ];
        return view('dashboard.' . $this->view . '.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {}



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Order::with([
            'user:id,name,mobile,image',
            'driver:id,name,mobile,image',
            'status:id',
            'coupon:id,code,discount_percentage',
            'review',
            'cancelReason',
            'address',
         'items' => function ($query) {
    $query->select('id', 'order_id', 'product_id', 'quantity', 'unit_price', 'total_price')
        ->with([
            'product:id,image,category_id,quantity,unit_id',
            'product.unit',
            'attributes' => function ($query) {
                $query->select('id', 'order_item_id', 'attribute_id', 'attribute_value_id', 'attribute_name', 'value_name', 'color_code');
            }
        ]);
}
        ])->find($id);

        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        DB::beginTransaction();
        $item->update(['read' => '1']);
        DB::commit();

        $allStatuses = Orderstatus::all();
        $allowedIds  = $item->getAllowedStatuses();
        $allowedStatuses = $allStatuses->whereIn('id', $allowedIds);

        // السائقون المتاحون للتعيين (نشطون وليس لديهم طلب قيد التوصيل)
        $availableDrivers = User::Drivers()
            ->where('is_active', 1)
            ->whereDoesntHave('driverOrders', fn($q) => $q->where('status_id', 4))
            ->select('id', 'name', 'mobile')
            ->get();

        $data = [
            'item'             => $item,
            'routeName'        => $this->routeName,
            'allowedStatuses'  => $allowedStatuses,
            'availableDrivers' => $availableDrivers,
        ];

        return view('dashboard.' . $this->view . '.show', $data);
    }


    public function changeorder_status(Request $request)
    {
        $rules = [
            'id'        => 'required|numeric|exists:orders,id',
            'status_id' => 'required|numeric|exists:orderstatuses,id',
        ];
        $attributes = [
            'id'        => __('dashboard.Number_Order'),
            'status_id' => __('dashboard.Order_Status'),
        ];

        Validator::make($request->all(), $rules, [], $attributes)->validateWithBag('ModalChangeStatusOrderbag');

        $item = Order::find($request->id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        if (!$item->canChangeStatusTo($request->status_id)) {
            return back()->withErrors(['error' => __('dashboard.The order status cannot be changed to this state')]);
        }

        DB::beginTransaction();
        $updateData = ['status_id' => $request->status_id];

        // تسجيل الطوابع الزمنية تلقائياً عند تغيير الحالة
        if ($request->status_id == 3) $updateData['prepared_at']          = now();
        if ($request->status_id == 4) $updateData['out_for_delivery_at'] = now();
        if ($request->status_id == 5) $updateData['delivered_at']         = now();

        // إذا وصل الطلب لمرحلة التجهيز وهو توصيل → عيّن سائق تلقائياً إن لم يكن معيّناً
        if ($request->status_id == 3 && $item->delivery_type === 'delivery' && !$item->driver_id) {
            (new DriverAssignmentService())->assignAvailableDriver($item);
        }

        $item->update($updateData);
        DB::commit();

        return redirect()->route($this->routeName . '.show', $request->id)
            ->with(['success' => __('dashboard.Status_modified_successfully')]);
    }

    /**
     * تعيين سائق يدوياً للطلب من لوحة الإدارة
     */
    public function assignDriver(Request $request)
    {
        $rules = [
            'id'        => 'required|numeric|exists:orders,id',
            'driver_id' => 'required|numeric|exists:users,id',
        ];
        Validator::make($request->all(), $rules, [], [])->validateWithBag('AssignDriverBag');

        $item = Order::find($request->id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        // الطلب يجب أن يكون من نوع توصيل ولم يُسلَّم/يُلغَ بعد
        if ($item->delivery_type !== 'delivery' || in_array($item->status_id, [5, 6])) {
            return back()->withErrors(['error' => __('dashboard.The order status cannot be changed to this state')]);
        }

        // التأكد أن المستخدم المُختار سائق فعلاً ونشط
        $driver = User::Drivers()->where('is_active', 1)->find($request->driver_id);
        if (!$driver) {
            return back()->withErrors(['error' => __('dashboard.ID_Not_Found')]);
        }

        DB::beginTransaction();
        $item->update(['driver_id' => $driver->id]);
        DB::commit();

        (new DriverAssignmentService())->notifyDriverAssigned($driver, $item);

        return redirect()->route($this->routeName . '.show', $request->id)
            ->with(['success' => __('dashboard.Status_modified_successfully')]);
    }

    /**
     * إلغاء تعيين السائق
     */
    public function unassignDriver(Request $request)
    {
        $item = Order::findOrFail($request->id);

        if ($item->status_id == 5) {
            return back()->withErrors(['error' => __('dashboard.The order status cannot be changed to this state')]);
        }

        DB::beginTransaction();
        $item->update(['driver_id' => null]);
        DB::commit();

        return redirect()->route($this->routeName . '.show', $request->id)
            ->with(['success' => __('dashboard.Status_modified_successfully')]);
    }


    public function changeorder_payment_status(Request $request)
    {
        $rules = [
            'id' => 'required|numeric|exists:orders,id',
            'payment_status' => 'required|string|in:pending,paid,failed,cancelled,refunded',
        ];
        $messages = [];
        $attributes = [
            'id' => __('dashboard.Number_Order'),
            'payment_status' => __('dashboard.Payment_Statussingle'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages, $attributes)->validateWithBag("ModalChangeMethodStatusOrderbag");
        $item = Order::find($request->id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        DB::beginTransaction();
        $item_data = [
            'payment_status' => $request->payment_status,
            'paid_at' => $request->payment_status === 'paid' ? now() : null,
        ];
        $item->update($item_data);
        DB::commit();
        return redirect()->route($this->routeName . '.show', $request->id)->with(['success' => __('dashboard.Status_modified_successfully')]);
    }


    public function invoice($id, $downloadpdf = null)
    {

        $item = Order::with([
            'user:id,name,mobile,image', // بيانات العميل
            'status:id', // حالة الطلب
            'coupon:id,code,discount_percentage', // كوبون الخصم
            'review',
            'items' => function ($query) {
                $query->select('id', 'order_id', 'product_id', 'quantity', 'unit_price', 'total_price')
                    ->with([
                        'product:id,image,category_id', // معلومات المنتج
                        'attributes' => function ($query) {
                            $query->select('id', 'order_item_id', 'attribute_id', 'attribute_value_id', 'attribute_name', 'value_name', 'color_code');
                        }
                    ]);
            }
        ])->find($id);

        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        $store_info = Setting::whereIn('key', ['store_logo', 'store_email', 'store_mobile', 'store_name'])
            ->get()
            ->mapWithKeys(function ($setting) {
                // ترجع القيمة المترجمة إذا موجودة، وإلا تستخدم plain_value
                return [$setting->key => $setting->value ?? $setting->plain_value];
            });

        $data = [
            'item' => $item,
            'itemstore_info' => $store_info,
            'routeName' => $this->routeName
        ];
        if ($downloadpdf == 1) {
            // $pdf = new \TCPDF();
            //  Set document information

            $pdf = new \TCPDF();
            //  Set document information

            $namefile = "invoice-" . $id . '.pdf';
            $Header_view = view('dashboard.' . $this->view . '.invoice.pdf.header', $data)->render();
            $footer_view = view('dashboard.' . $this->view . '.invoice.pdf.footer', $data)->render();
            $main_view = view('dashboard.' . $this->view . '.invoice.pdf.content', $data)->render();
            $summery_price_view = view('dashboard.' . $this->view . '.invoice.pdf.summery_price', $data)->render();
            $pdf->SetPrintHeader(true);
            $pdf->SetPrintFooter(true);
            $pdf->setHeaderFont(['aealarabiya', '', '10']);
            $pdf->setFooterFont(['aealarabiya', '', '10']);
            $pdf->setHtmlHeader($Header_view);
            $pdf->setHtmlFooter($footer_view);
            $pdf->SetTitle($namefile);
            $pdf->SetFont('aealarabiya', '', 12);
            $pdf->SetTopMargin(20);
            $pdf->SetMargins(13, 70, 13, true);
            $pdf->setFooterMargin(5);
            $pdf->SetAutoPageBreak(true, 30);
            $pdf->SetAuthor($namefile);
            $pdf->SetDisplayMode('real', 'default');

            // first page
            $pdf->AddPage();

            $pdf->writeHTML($main_view);

            $pdf->writeHTML($summery_price_view, true, false, true, false, '');
            $pdf->lastPage();
            // Output PDF
            $pdf->Output($namefile, 'D'); //I Output 、D download
        }



        return view('dashboard.' . $this->view . '.invoice.show', $data);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $item = Order::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

            DB::beginTransaction();
            $item->delete();
            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Delete_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    public function deleteAll(Request $request)
    {

        $ids = explode(",", $request->ids);
        $orders = Order::find($ids);



        try {
            DB::beginTransaction();
            $orders->each(function ($item) {
                $item->delete();
            });
            DB::commit();
            return response()->json(['success' => __('dashboard.Delete_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return response()->json(['error' => __('dashboard.Error_DB')]);
        }
    }

    public function Export_Excel(Request $request)
    {

        return Excel::download(new OrdersExport($request), 'orders.xlsx');
    }

    public function latest_order()
    {

        $orders = Order::where('restaurant_id', Auth::id())
            ->with('user:id,name', 'status:id', 'restaurant:id', 'coupon:id,code,discount_percentage')
            ->select('id', 'user_id', 'total_items',  'status_id', 'currency', 'read', 'created_at', 'restaurant_id', 'total_price', 'discount_amount', 'coupon_id', 'added_tax', 'final_price', 'app_commission_amount', 'restaurant_earning', 'app_commission_percentage')->latest('id')
            ->take(10)
            ->get()
            ->map(function ($o) {
                return [
                    'id' => $o->id,
                    'user_name' => $o->user->name,
                    'restaurant_name' => $o->restaurant->restaurant_name,
                    'status_name' => $o->status->name,
                    'created_at' => $o->created_at->format('Y-m-d H:i'),
                    'total_items' => $o->total_items,
                    'total_price' => $o->total_price,
                    'final_price' => $o->final_price,
                ];
            });

        return response()->json($orders);
    }
}
