<?php

namespace App\Http\Controllers\Api\V1\Trader;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Trader\TraderRegisterRequest;
use App\Http\Requests\Api\V1\Trader\TraderUpdateRequest;
use App\Models\User;
use App\Services\{ImageUploadService, OtpService};
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TraderAuthApiController extends Controller
{
    use ApiTrait;

    /**
     * رفع صورة/بانر وإرجاع أسماء الملفات المحفوظة.
     */
    private function handleImages(Request $request, ImageUploadService $imageService): array
    {
        $files = [];

        if ($request->hasFile('image')) {
            $files['image'] = $imageService->uploadAndProcess(
                image: $request->file('image'),
                folder: User::FOLDER,
                thumbnailWidth: 128,
                thumbnailHeight: 128
            );
        }

        if ($request->hasFile('banner_image')) {
            $files['banner_image'] = $imageService->uploadAndProcess(
                image: $request->file('banner_image'),
                folder: User::FOLDER,
                thumbnailWidth: 500,
                thumbnailHeight: 500
            );
        }

        return $files;
    }

    /**
     * حذف ملفات قديمة من السيرفر (الأصلي + thumbnail).
     */
    private function deleteOldFile(?string $fileName): void
    {
        if (!$fileName) {
            return;
        }

        foreach ([
            public_path('uploads/' . User::FOLDER . '/' . $fileName),
            public_path('uploads/' . User::FOLDER . '/thumbnail/' . $fileName),
        ] as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    /**
     * شكل بيانات التاجر الموحّد (للبروفايل والتعديل).
     */
    private function traderResource(User $trader): array
    {
        $locale = app()->getLocale();

        return [
            'id'                  => $trader->id,
            'name'                => $trader->name,
            'mobile'              => $trader->mobile,
            'whatsapp_number'     => $trader->whatsapp_e164,
            'whatsapp_link'       => $trader->whatsapp_link,
            'id_number'           => $trader->id_number,
            'commercial_register' => $trader->commercial_register,
            'store_lat'           => $trader->store_lat,
            'store_lng'           => $trader->store_lng,
            //'store_address'       => $trader->store_address,
            'city_id'             => $trader->city_id,
            'area_id'             => $trader->area_id,
            'neighborhood_id'     => $trader->neighborhood_id,
            'city'                => $trader->city?->translate($locale)?->name,
            'area'                => $trader->area?->translate($locale)?->name,
            'neighborhood'        => $trader->neighborhood?->translate($locale)?->name,
            'image'               => $trader->base_image_url,   // رابط كامل للأصلية
            'image_thumbnail'     => $trader->image_url,        // رابط كامل للمصغّرة
            'banner'              => $trader->banner_url,       // رابط كامل أو null
            'trader_status'       => $trader->trader_status,
            'commission_rate'     => $trader->commission_rate,
        ];
    }

    public function register(TraderRegisterRequest $request, ImageUploadService $imageService)
    {
        $uploaded = [];

        try {
            $data = $request->validated();

            DB::beginTransaction();

            $uploaded = $this->handleImages($request, $imageService);

            $trader = User::create([
                'name'                => $data['name'],
                'mobile'              => $data['mobile'],
                'whatsapp_number'     => $data['whatsapp_number'] ?? null,
                'id_number'           => $data['id_number'],
                'commercial_register' => $data['commercial_register'] ?? null,
                'store_lat'           => $data['store_lat'],
                'store_lng'           => $data['store_lng'],
               // 'store_address'       => $data['store_address'],
                'city_id'             => $data['city_id'],
                'area_id'             => $data['area_id'] ?? null,
                'neighborhood_id'     => $data['neighborhood_id'] ?? null,
                'terms_accepted'      => true,
                'trader_status'       => 'pending',
                'is_active'           => 0,
            ] + $uploaded);

            $trader->assignRole('trader');

            app(OtpService::class)->generateAndSend($trader, 'registration');

            DB::commit();

            return $this->returnData('data', [
                'user_id'             => $trader->id,
                'trader_status'       => 'pending',
                'requires_activation' => true,
            ], __('api.Registration_Code_Sent_Verify'));

        } catch (\Exception $ex) {
            DB::rollBack();

            // تنظيف الصور لو فشل التسجيل
            foreach ($uploaded as $file) {
                $this->deleteOldFile($file);
            }

            Log::error('Trader register: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function get_TraderInfo(Request $request)
    {
        try {
            $trader = $request->user()->load(['city', 'area', 'neighborhood']);

            return $this->returnData('data', $this->traderResource($trader), __('api.Api_Return_Data'));
        } catch (\Exception $ex) {
            Log::error('get_TraderInfo: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    public function update(TraderUpdateRequest $request, ImageUploadService $imageService)
    {
        try {
            $trader = $request->user();

            $data = collect($request->validated())
                ->except(['image', 'banner_image'])
                ->toArray();

            $oldImage  = $trader->image;
            $oldBanner = $trader->banner_image;

            $uploaded = $this->handleImages($request, $imageService);

            $trader->update($data + $uploaded);

            // حذف القديم بعد نجاح التحديث
            if (isset($uploaded['image'])) {
                $this->deleteOldFile($oldImage);
            }
            if (isset($uploaded['banner_image'])) {
                $this->deleteOldFile($oldBanner);
            }

            $trader->refresh()->load(['city', 'area', 'neighborhood']);

            return $this->returnData('data', $this->traderResource($trader), __('api.Api_Update_Success'));
        } catch (\Exception $ex) {
            Log::error('Trader update: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}