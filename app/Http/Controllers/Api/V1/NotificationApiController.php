<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Http\Requests\Api\V1\NotificationsRequest;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationApiController extends Controller
{
    use ApiTrait;
    public function index(Request $request)
    {

        try {
            DB::beginTransaction();
            $user = $request->user();
            $items =  Notification::where('user_id', $user->id)->latest()->paginate(API_PAGANATION_COUNT);
            $items->makeHidden(['created_at', 'updated_at']);
            $items->transform(function ($item) {
                $item->created_at_human = $item->created_at->diffForHumans();
                return $item;
            });

            $data = [
                'items' => $items->items(),
                'meta' => [
                    'current_page' => $items->currentPage(),
                    'total_pages' => $items->lastPage(),
                    'total_items' => $items->total(),
                    'per_page' => $items->perPage(),
                ],
                'links' => [
                    'self' => $items->url($items->currentPage()),
                    'next' => $items->nextPageUrl(),
                    'prev' => $items->previousPageUrl(),
                ]
            ];

            DB::commit();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('Notification index: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }



    public function getUnreadCount(Request $request)
    {

        try {
            DB::beginTransaction();
            $user = $request->user();
            $unreadCount = Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->count();
            $data = [
                'unreadCount' => $unreadCount,
            ];

            DB::commit();
            return $this->returnData('data', $data, __('api.Api_Return_Data'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('Notification getUnreadCount: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }


    public function markAllAsRead(Request $request)
    {

        try {
            DB::beginTransaction();
            $user = $request->user();


            Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            DB::commit();
            return $this->returnSuccessMessage(__('api.Api_Save_success'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('Notification markAllAsRead: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */


    /**
     * Destroy resources by the given id.
     *
     * @param string $productId
     * @return void
     */
       public function destroy(NotificationsRequest $request)
    {
        try {
            $user = $request->user();

            // ✅ سد ثغرة IDOR: جلب الإشعار بشرط أن يخص المستخدم الحالي فقط
            $notification = Notification::where('id', $request->notification_id)
                ->where('user_id', $user->id)
                ->first();

            // ✅ منع Fatal Error: التحقق من وجود الإشعار قبل استدعاء delete()
            if (!$notification) {
                return $this->returnError(__('api.Notification_Not_Found'));
            }

            $notification->delete();

            // ملاحظة: DB::beginTransaction لم تعد ضرورية هنا لأنها عملية حذف واحدة
            return $this->returnSuccessMessage(__('api.Delete_success'));
        } catch (Exception $ex) {
            Log::error('Notification destroy: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
