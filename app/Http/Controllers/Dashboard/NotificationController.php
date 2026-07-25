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
        $data = [
            "users" => $users,
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
        $users = new User();
        try {
            DB::beginTransaction();
            $recivers = $request->recivers;
            switch ($recivers) {
                case "-1":
                    $users = User::Clients()->Active()->whereNotNull('device_token')->pluck('device_token')->toArray();
                    break;
                default:
                    $users = User::where('id', $recivers)->select('id', 'device_token')->get();
            }
            if ($recivers == -1) {
                $chunks = array_chunk($users, 500);
                foreach ($chunks as $chunk) {
                    (new FCMServices())->sendToMultipleDevices($chunk, $request->msg_title, $request->msg_details);
                }
            } else {
                foreach ($users as $itemuser) {
                    (new FCMServices())->sendMsg($itemuser->id, $itemuser->device_token, $request->msg_title, $request->msg_details);
                }
            }


            DB::commit();
            return redirect()->back()->with(['success' => __('dashboard.The_message_sent_successfully')]);
        } catch (Exception $ex) {
            DB::rollback();
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
