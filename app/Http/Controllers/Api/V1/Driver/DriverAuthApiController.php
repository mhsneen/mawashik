<?php

namespace App\Http\Controllers\Api\V1\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Driver\DriverRegisterRequest;
use App\Models\{User, OtpCode};
use App\Services\{OtpService, FCMServices, ImageUploadService, AdminNotifier};
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

/**
 * DriverAuthApiController — مصادقة مندوب التوصيل.
 *
 * العمليات:
 *  - register: تسجيل مندوب جديد (حساب pending بانتظار اعتماد الأدمن).
 *  - login: تسجيل دخول عبر OTP (يُعاد استخدام AuthCommonApiController).
 *  - get_DriverInfo: معلومات المندوب بعد الاعتماد.
 *  - driver_Update: تحديث البيانات الشخصية + الصور.
 *  - logout: تسجيل خروج (يُعاد استخدام AuthCommonApiController).
 *
 * ملاحظة: المندوب لا يمكنه قبول الطلبات إلا بعد اعتماد الأدمن.
 * تسجيله يضع driver_status = pending و is_active = 0.
 */
class DriverAuthApiController extends Controller
{
    use ApiTrait;

    /**
     * تسجيل مندوب توصيل جديد.
     *  - يرفع صورة الهوية + صورة الرخصة.
     *  - يخزّن driver_status = pending.
     *  - يطلق OTP للتحقق.
     *  - يخطر الأدمن بوجود مندوب جديد بانتظار الاعتماد.
     */
    public function register(DriverRegisterRequest $request, ImageUploadService $imageService)
    {
        try {
            $data = $request->validated();

            DB::beginTransaction();

            $driver = User::create([
                'name'             => $data['name'],
                'mobile'           => $data['mobile'],
                'email'            => $data['email'] ?? null,
                'password'         => Hash::make($data['password']),
                'country_id'       => $data['country_id'] ?? null,
                'city_id'          => $data['city_id'] ?? null,
                'area_id'          => $data['area_id'] ?? null,
                'neighborhood_id'  => $data['neighborhood_id'] ?? null,
                'device_token'     => $data['device_token'] ?? null,
                'id_number'        => $data['id_number'],
                'driver_status'    => 'pending',
                'is_active'         => 0,  // يُفعّل عند الاعتماد
                'terms_accepted'   => true,
            ]);

            // رفع الصورة الشخصية
            if ($request->hasFile('image')) {
                $driver->image = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: 'users',
                    thumbnailWidth: 300,
                    thumbnailHeight: 300
                );
            }

            // رفع صورة الهوية (مطلوبة)
            if ($request->hasFile('id_image')) {
                $driver->id_image = $imageService->uploadAndProcess(
                    image: $request->file('id_image'),
                    folder: 'users',
                    thumbnailWidth: 400,
                    thumbnailHeight: 300
                );
            }

            // رفع صورة الرخصة (مطلوبة)
            if ($request->hasFile('license_image')) {
                $driver->license_image = $imageService->uploadAndProcess(
                    image: $request->file('license_image'),
                    folder: 'users',
                    thumbnailWidth: 400,
                    thumbnailHeight: 300
                );
            }

            $driver->save();

            // تعيين دور driver
            $driverRole = Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'web']);
            $driver->assignRole($driverRole);

            // إنشاء محفظة فارغة للمندوب
            \App\Models\Wallet::firstOrCreate(
                ['user_id' => $driver->id],
                ['balance' => 0, 'rolename' => 'driver']
            );

            // توليد OTP وإرساله
            app(OtpService::class)->generateAndSend($driver, 'registration');

            // إشعار الأدمن بوجود مندوب جديد بانتظار الاعتماد
            AdminNotifier::newDriverPendingApproval($driver);

            DB::commit();

            return $this->returnData('data', [
                'user_id'            => $driver->id,
                'driver_status'      => 'pending',
                'requires_activation' => true,
            ], __('api.Driver_Registered_Pending_Approval'));

        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Driver register: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * معلومات المندوب الحالي.
     */
    public function get_DriverInfo(Request $request)
    {
        try {
            $driver = $request->user();

            return $this->returnData('data', [
                'id'                => $driver->id,
                'name'              => $driver->name,
                'mobile'            => $driver->mobile,
                'email'             => $driver->email,
                'image'             => $driver->image_url,
                'id_number'         => $driver->id_number,
                'id_image'          => $driver->id_image ? url('uploads/users/' . $driver->id_image) : null,
                'license_image'     => $driver->license_image ? url('uploads/users/' . $driver->license_image) : null,
                'driver_status'     => $driver->driver_status,
                'is_active'         => (bool) $driver->is_active,
                'is_approved'        => $driver->isApprovedDriver(),
                'commission_rate'   => $driver->driver_commission_rate,
                'city_id'           => $driver->city_id,
                'area_id'           => $driver->area_id,
                'neighborhood_id'   => $driver->neighborhood_id,
                'approved_at'       => $driver->driver_approved_at?->toIso8601String(),
                'created_at'        => $driver->created_at?->toIso8601String(),
            ], __('api.Api_Return_Data'));
        } catch (\Exception $ex) {
            Log::error('Driver get_DriverInfo: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }

    /**
     * تحديث البيانات الشخصية للمندوب (لا يشمل الحالة/العمولة — فقط الأدمن يعدّلها).
     */
    public function driver_Update(Request $request, ImageUploadService $imageService)
    {
        try {
            $driver = $request->user();

            $request->validate([
                'name'             => 'sometimes|string|max:150',
                'email'            => 'sometimes|nullable|email|max:255|unique:users,email,' . $driver->id,
                'password'         => 'sometimes|nullable|string|min:6|max:30',
                'image'            => 'sometimes|image|max:5120',
                'id_image'         => 'sometimes|image|max:5120',
                'license_image'    => 'sometimes|image|max:5120',
                'device_token'     => 'sometimes|nullable|string|max:255',
                'city_id'          => 'sometimes|nullable|exists:cities,id',
                'area_id'          => 'sometimes|nullable|exists:areas,id',
                'neighborhood_id'  => 'sometimes|nullable|exists:neighborhoods,id',
            ], [], [
                'name'            => __('dashboard.Name'),
                'email'           => __('dashboard.Email'),
                'password'        => __('dashboard.Password'),
                'image'           => __('dashboard.Image'),
                'id_image'        => __('dashboard.ID_Image'),
                'license_image'   => __('dashboard.License_Image'),
            ]);

            DB::beginTransaction();

            $updateData = [];
            foreach (['name', 'email', 'device_token', 'city_id', 'area_id', 'neighborhood_id'] as $field) {
                if ($request->filled($field)) {
                    $updateData[$field] = $request->$field;
                }
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('image')) {
                $updateData['image'] = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: 'users',
                    thumbnailWidth: 300,
                    thumbnailHeight: 300,
                    oldFilename: $driver->image
                );
            }

            if ($request->hasFile('id_image')) {
                $updateData['id_image'] = $imageService->uploadAndProcess(
                    image: $request->file('id_image'),
                    folder: 'users',
                    thumbnailWidth: 400,
                    thumbnailHeight: 300,
                    oldFilename: $driver->id_image
                );
            }

            if ($request->hasFile('license_image')) {
                $updateData['license_image'] = $imageService->uploadAndProcess(
                    image: $request->file('license_image'),
                    folder: 'users',
                    thumbnailWidth: 400,
                    thumbnailHeight: 300,
                    oldFilename: $driver->license_image
                );
            }

            $driver->update($updateData);

            DB::commit();

            return $this->returnData('data', [
                'id'            => $driver->id,
                'name'          => $driver->name,
                'image'         => $driver->image_url,
                'driver_status' => $driver->driver_status,
            ], __('api.Profile_Updated_Success'));
        } catch (\Exception $ex) {
            DB::rollBack();
            Log::error('Driver update: ' . $ex->getMessage());
            return $this->returnError(__('api.Api_Error_Contact_Programmer'));
        }
    }
}
