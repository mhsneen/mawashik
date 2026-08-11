<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{User, OrderReview, DriverReview};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Requests\Dashboard\ClientRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use App\Services\ImageUploadService;
use App\Services\FCMServices;
use App\Services\SmsService;
use App\Services\WhatsappService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
class ClientsController extends Controller
{
    protected  $folder = "users";
    protected  $view = "clients";
    protected  $routeName = "clients";
    protected  $namesection =  'dashboard.Clients';

    public function __construct()
    {
        $this->middleware('permission:Clients');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
      public function index(Request $request)
    {
        // ✅ جلب is_blocked بجانب is_active
        $items = User::Clients()
                    ->FilterDashbordClients(request(['search_name', 'search_mobile', 'search_active', 'search_is_blocked']))
                    ->select('id', 'name', 'mobile', 'is_active', 'is_blocked', 'created_at')
                    ->latest('id')
                    ->paginate(PAGANATION_COUNT)
                    ->withQueryString();

        $data = [
            'items' => $items,
            'routeName' => $this->routeName,
            'namesection' => $this->namesection,
            'request_search' => $request->all()
        ];
        return view('dashboard.' . $this->view . '.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection
        ];
        return view('dashboard.' . $this->view . '.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientRequest $request, ImageUploadService $imageService)
    {

        DB::beginTransaction();

        $item_data = [
            'name' => $request->name,
            'is_active' => $request->is_active,
            'mobile' => $request->mobile,
             'email' => $request->email,
        ];

        if ($request->hasFile('image')) {
            $imagename = $imageService->uploadAndProcess(
                image: $request->file('image'),
                folder: $this->folder,
                thumbnailWidth: 128,
                thumbnailHeight: 128
            );
            $item_data['image'] = $imagename;
        }
        $item = User::create($item_data);
        $item->save();
        $clientRole = Role::where('name', 'client')
            ->where('guard_name', 'web')
            ->first();

        $item->assignRole($clientRole);

        DB::commit();
        return redirect()->route($this->routeName)->with(['success' => __('dashboard.Add_success')]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = User::with(
            ['orders']
        )->withCount(['orders'])->find($id);



        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        // تقييمات العميل على الطلبات
        $reviews = OrderReview::where('client_id', $item->id)->get();

        // ✅ تقييمات العميل للسائقين (العميل هو من يقيّم السائق)
        $driverReviews = DriverReview::where('client_id', $item->id)
            ->with('driver:id,name')
            ->latest('id')
            ->get();

        // ✅ تصحيح: مجموع المشتريات يجب احتسابه فقط على الطلبات "المُسلَّمة فعلياً" (status_id = 5)،
        // وليس status_id = 4 (قيد التوصيل) كما كان سابقاً - وهو سبب ظهور القيمة 0 دائماً.
        $totalCompletedOrdersPrice = $item->orders()
            ->where('status_id', 5)
            ->sum('final_price');

        $data = [
            'item' => $item,
            'reviews' => $reviews,
            'driverReviews' => $driverReviews,
            'totalCompletedOrdersPrice' => $totalCompletedOrdersPrice,
            'routeName' => $this->routeName
        ];
        return view('dashboard.' . $this->view . '.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $item = User::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
        $data = [
            'item' => $item,
            'routeName' => $this->routeName,
            'folder' => $this->folder,
            'namesection' => $this->namesection,
        ];
        return view('dashboard.' . $this->view . '.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClientRequest $request, $id, ImageUploadService $imageService)
    {
        try {
            $item = User::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

            DB::beginTransaction();


            $item_data = [
                'name' => $request->name,
                'is_active' => $request->is_active,
                'mobile' => $request->mobile,
            ];

            if ($request->has('image')) {
                $imagename = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 128,
                    thumbnailHeight: 128,
                    oldFilename: $item->image
                );
                $item_data['image'] =  $imagename;
            }

            $item->update($item_data);

            DB::commit();
            return redirect()->route($this->routeName . '.edit', $id)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


    public function changepassword(Request $request, $id)
    {
        $item = User::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        $rules = [
            'password' => 'required|string|min:6',
            'password-confirmation' => 'required|same:password',
        ];
        $messages = [];
        $attributes = [
            'password' => __('dashboard.New_Password'),
            'password-confirmation' => __('dashboard.Confirm_Password'),
        ];



        $validator = Validator::make($request->all(), $rules, $messages, $attributes)->validateWithBag("changepassbag_{$id}");


        try {
            $item = User::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
            DB::beginTransaction();
            $item_data = [
                'password' => Hash::make($request->password),
            ];
            $item->update($item_data);
            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    public function ChangeActive(Request $request, $id)
    {
        $item = User::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        $rules = [
            'is_active' => 'required|in:0,1'
        ];
        $messages = [];
        $attributes = [
            'is_active' => __('dashboard.Efficiency_Status'),
        ];



        $validator = Validator::make($request->all(), $rules, $messages, $attributes)->validateWithBag("changeactivebag_{$id}");


        try {
            $item = User::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);
            DB::beginTransaction();
            $item_data = [
                'is_active' => $request->is_active,
            ];
            $item->update($item_data);
            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }
  public function sendingmessage(Request $request, $id)
{
    $item = User::find($id);
    if (!$item)
        return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

    $rules = [
        'msg_type'    => 'required|in:0,1,2',
        'msg_details' => 'required|max:255', // كبّرتها من 50
        'msg_title'   => 'required|max:20',
    ];
    $attributes = [
        'msg_type'    => __('dashboard.Message_type'),
        'msg_details' => __('dashboard.Message_text'),
        'msg_title'   => __('dashboard.Title'),
    ];

    Validator::make($request->all(), $rules, [], $attributes)
        ->validateWithBag("sendingmessagebag_{$id}");

    try {
        $sent = match ((int) $request->msg_type) {
            0 => (new FCMServices())->sendMsg($item->id, $item->device_token, $request->msg_title, $request->msg_details),
            1 => (new SmsService())->send($item->mobile, $request->msg_details),
            2 => (new WhatsappService())->send($item->mobile, $request->msg_details),
        };

        if (!$sent) {
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.Failed_to_send_message')]);
        }

        return redirect()->route($this->routeName)->with(['success' => __('dashboard.The_message_sent_successfully')]);
    } catch (Exception $ex) {
        Log::error('sendingmessage: ' . $ex->getMessage());
        return redirect()->route($this->routeName)->with(['error' => __('dashboard.Error_DB')]);
    }
}

public function ChangeBlock(Request $request, $id)
    {
        $item = User::find($id);
        if (!$item)
            return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

        $rules = [
            'is_blocked' => 'required|in:0,1'
        ];
        $attributes = [
            'is_blocked' => __('dashboard.Block_Status'),
        ];

        $validator = Validator::make($request->all(), $rules, [], $attributes)->validateWithBag("changeblockbag_{$id}");

        try {
            $item = User::find($id);
            if (!$item)
                return redirect()->route($this->routeName)->with(['error' => __('dashboard.ID_Not_Found')]);

            DB::beginTransaction();
            $item_data = [
                'is_blocked' => $request->is_blocked,
            ];
            $item->update($item_data);
            DB::commit();
            return redirect()->route($this->routeName)->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            DB::rollback();
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $item = User::find($id);
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
        try {
            $ids = explode(",", $request->ids);
            DB::beginTransaction();
            User::find($ids)->each(function ($item, $key) {
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

        return Excel::download(new ClientsExport($request), 'clients.xlsx');
    }
}
