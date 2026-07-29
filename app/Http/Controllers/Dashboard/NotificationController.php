<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\BankRequest;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\User;
use App\Models\Notification;
use App\Http\Requests\Dashboard\NotificationRequest;
use App\Services\FCMServices;

class NotificationController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:Alerts');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::Clients()->Active()->select('id', 'name')->get();
        $drivers = User::Drivers()->Active()->select('id', 'name')->get();
        $data = [
            "users" => $users,
            "drivers" => $drivers,
        ];
        return view('dashboard.notitfications.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(NotificationRequest $request)
{
    try {
        DB::beginTransaction();

        // ملاحظة: اسم الحقل في الفيو "recivers" (بخطأ إملائي)، تركته كما هو ليتطابق مع الفيو
        $recivers = $request->recivers;
        $title = $request->msg_title;
        $details = $request->msg_details;

        // تحديد ما إذا كان الإرسال جماعي أم فردي
        $isBulk = in_array($recivers, ['all', 'all_clients', 'all_drivers']);

        if ($isBulk) {
            $tokens = [];

            switch ($recivers) {
                case 'all':
                    // كل المستخدمين النشطين الذين يملكون توكن
                    $tokens = User::Active()->whereNotNull('device_token')->pluck('device_token')->toArray();

                    // إذا كان السائقين في موديل منفصل (مثل Driver)، قم بإلغاء التعليق عن الكود التالي:
                    // $driverTokens = \App\Models\Driver::Active()->whereNotNull('device_token')->pluck('device_token')->toArray();
                    // $tokens = array_merge($tokens, $driverTokens);
                    break;

                case 'all_clients':
                    $tokens = User::Clients()->Active()->whereNotNull('device_token')->pluck('device_token')->toArray();
                    break;

                case 'all_drivers':
                    // إذا كان السائقين داخل موديل User بنوع (type = driver)
                    $tokens = User::Drivers()->Active()->whereNotNull('device_token')->pluck('device_token')->toArray();

                    // أما إذا كان لديك موديل منفصل للسائقين استخدم هذا:
                    // $tokens = \App\Models\Driver::Active()->whereNotNull('device_token')->pluck('device_token')->toArray();
                    break;
            }

            // تقسيم الإرسال الجماعي إلى دفعات (كل دفعة 500) لتجنب ضغط الفايربيس
            if (!empty($tokens)) {
                $chunks = array_chunk($tokens, 500);
                foreach ($chunks as $chunk) {
                    (new FCMServices())->sendToMultipleDevices($chunk, $title, $details);
                }
            }
        } else {
            // إرسال فردي لمستخدم أو سائق محدد بالـ ID
            $user = User::where('id', $recivers)->select('id', 'device_token')->first();

            // إذا كان السائقين في موديل منفصل ولم يتم إيجاد المستخدم، ابحث عن السائق
            // if (!$user) {
            //     $user = \App\Models\Driver::find($recivers);
            // }

            if ($user && $user->device_token) {
                (new FCMServices())->sendMsg($user->id, $user->device_token, $title, $details);
            }
        }

        DB::commit();
        return redirect()->back()->with(['success' => __('dashboard.The_message_sent_successfully')]);

    } catch (Exception $ex) {
        DB::rollback();
        // للديباج مؤقتاً: return redirect()->back()->with(['error' => $ex->getMessage()]);
        return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
    }
}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {}

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
    public function destroy($id) {}

    public function deleteAll(Request $request) {}
}
