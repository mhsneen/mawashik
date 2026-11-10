<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Messagecontactus;
use Illuminate\Http\Request;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\V1\MessageContactsRequest;
use Illuminate\Support\Facades\Log;

class MessagescontactusApiController extends Controller
{
    use ApiTrait;
    public function set_MessageContact(MessageContactsRequest $request)
    {
        try {
            DB::beginTransaction();
            $item_data = [
                'name' => $request->name,
                'mobile' => $request->mobile,
                'details' => $request->details,
                'email' => $request->email,
                'subject' => $request->subject

            ];
            $item = Messagecontactus::create($item_data);
            $item->save();
            DB::commit();
            return $this->returnSuccessMessage(__('api.Api_Save_success'));
        } catch (Exception $ex) {
            DB::rollback();
            Log::error('set_MessageContact: ' . $ex->getMessage(), [
                'trace' => $ex->getTraceAsString(),
                'input' => $request->all()
            ]);
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
