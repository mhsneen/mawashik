<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Models\User;
use App\Models\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as NotificationFirebase;

class FCMServices
{
    /**
     * Create a new class instance.
     */
    protected $messaging;

    public function __construct()
    {
        $firebase = (new Factory)->withServiceAccount(storage_path('firebase/firebase_credentials.json'));
        $this->messaging = $firebase->createMessaging();
    }

    /// send FCM Notification
    public function sendMsg($user_id, $device_token, $title, $msg, $data = [])
    {
        try {
            DB::beginTransaction();
            $user = User::find($user_id);
            $item_data = [
                'name' => $title,
                'details' => $msg,
                'user_id' => $user_id
            ];
            $item = Notification::create($item_data);
            $item->save();
            DB::commit();
            $this->sendPushNotification($device_token, $title, $msg, $data);
        } catch (Exception $ex) {
            DB::rollback();
            return $ex;
        }
    }


    public function sendPushNotification($token, $title, $body, $data = [])
    {

        $path = storage_path('firebase/firebase_credentials.json');
        if (!file_exists($path))
            die("File dose not exist");

        try {

            $message = CloudMessage::new()
                ->withNotification(NotificationFirebase::create($title, $body))
                ->withData($data) // أضف هنا بيانات إضافية
                ->toToken(trim($token));

            $this->messaging->send($message);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage(),
                'status' => 'False'
            ]);
        }
    }


    public function sendToMultipleDevices(array $tokens, string $title, string $body)
    {
        try {


            $chunks = array_chunk($tokens, 500);

            $reports = [];
            foreach ($chunks as $chunk) {
                $message = CloudMessage::new()
                    ->withNotification(NotificationFirebase::create($title, $body));

                $report = $this->messaging->sendMulticast($message, $chunk);
                $reports[] = $report;
            }

            return $reports; // يمكنك التعامل مع التقارير لاحقًا

        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage(),
                'status' => 'False'
            ]);
        }
    }
}
