<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\{Address, User};
use App\Traits\ApiTrait;
use App\Http\Requests\Api\V1\{AddressRequest, CalculateDeliveryPriceRequest};
use App\Services\DeliveryPriceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use InvalidArgumentException;
use App\Http\Resources\Api\V1\AddressResource;

class AddressApiController extends Controller
{
    use ApiTrait;

    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->get();

        return $this->returnData('data', AddressResource::collection($addresses), __('api.Api_Return_Data'));
    }

    public function store(AddressRequest $request)
    {
        try {
            $address = DB::transaction(function () use ($request) {
                $isFirstAddress = ! $request->user()->addresses()->exists();
                $makeDefault = $request->boolean('is_default') || $isFirstAddress;

                if ($makeDefault) {
                    $request->user()->addresses()->update(['is_default' => false]);
                }

                return $request->user()->addresses()->create([
                    'title' => $request->title,
                    'address' => $request->address,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'building_number' => $request->building_number,
                    'floor' => $request->floor,
                    'apartment_number' => $request->apartment_number,
                    'note' => $request->note,
                    'is_default' => $makeDefault,
                ]);
            });

            return $this->returnData('data', new AddressResource($address), __('api.Address_Added_Success'));

        } catch (Exception $ex) {
            Log::error('AddressApiController@store: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function update(AddressRequest $request, $id)
    {
        try {
            $address = Address::ownedBy($request->user()->id)->find($id);

            if (!$address) {
                return $this->returnError(__('api.Invalid_Address_Selected'));
            }

            DB::transaction(function () use ($request, $address) {
                if ($request->boolean('is_default')) {
                    $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
                }

                $address->update([
                    'title' => $request->title,
                    'address' => $request->address,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'building_number' => $request->building_number,
                    'floor' => $request->floor,
                    'apartment_number' => $request->apartment_number,
                    'note' => $request->note,
                    'is_default' => $request->boolean('is_default') ?: $address->is_default,
                ]);
            });

            return $this->returnData('data', new AddressResource($address->fresh()), __('api.Edit_success'));

        } catch (Exception $ex) {
            Log::error('AddressApiController@update: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function destroy(Request $request, $id)
    {
        $address = Address::ownedBy($request->user()->id)->find($id);

        if (!$address) {
            return $this->returnError(__('api.Invalid_Address_Selected'));
        }

        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $request->user()->addresses()->orderByDesc('id')->first()?->update(['is_default' => true]);
        }

        return $this->returnSuccessMessage(__('api.Delete_success'));
    }

    public function setDefault(Request $request, $id)
    {
        $address = Address::ownedBy($request->user()->id)->find($id);

        if (!$address) {
            return $this->returnError(__('api.Invalid_Address_Selected'));
        }

        DB::transaction(function () use ($request, $address) {
            $request->user()->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return $this->returnData('data', new AddressResource($address->fresh()), __('api.Edit_success'));
    }

    /**
     * ✅ حساب سعر التوصيل بين متجر التاجر وعنوان العميل
     */
    public function calculateDeliveryPrice(CalculateDeliveryPriceRequest $request, DeliveryPriceCalculator $calculator)
    {
        try {
            // ✅ جلب إحداثيات العميل
            if ($request->address_id) {
                $address = Address::ownedBy($request->user()->id)->find($request->address_id);
                if (!$address) {
                    return $this->returnError(__('api.Invalid_Address_Selected'));
                }
                $lat = $address->latitude;
                $lng = $address->longitude;
            } else {
                $lat = $request->latitude;
                $lng = $request->longitude;
            }

            // ✅ جلب إحداثيات التاجر
            $trader = User::traders()->find($request->trader_id);
            if (!$trader || !$trader->store_lat || !$trader->store_lng) {
                return $this->returnError(__('api.Trader_Location_Not_Available'));
            }

            // ✅ حساب التوصيل بين التاجر والعميل
            $result = $calculator->calculate(
                (float) $trader->store_lat,
                (float) $trader->store_lng,
                (float) $lat,
                (float) $lng
            );

            return $this->returnData('data', $result, __('api.Api_Return_Data'));
        } catch (InvalidArgumentException $ex) {
            return $this->returnError($ex->getMessage());
        } catch (Exception $ex) {
            Log::error('calculateDeliveryPrice: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
