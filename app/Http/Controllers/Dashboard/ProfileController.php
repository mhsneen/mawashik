<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\ProfileRequest;
use App\Http\Requests\Dashboard\PasswordRequest;
use Exception;
use App\Services\ImageUploadService;

class ProfileController extends Controller
{
    protected $folder = "users";

    public function editProfile()
    {

        $admin = User::find(auth('admin')->user()->id);
        return view('dashboard.profile.edit', compact('admin'));
    }

    public function updateProfile(ProfileRequest $request, ImageUploadService $imageService)
    {
        try {
            // احصل على كائن المستخدم من قاعدة البيانات لضمان توفر جميع وظائف Eloquent
            $admin = User::find(auth('admin')->user()->id);

            if ($request->has('image')) {
                $imageService = new ImageUploadService();
                $newFilename = $imageService->uploadAndProcess(
                    image: $request->file('image'),
                    folder: $this->folder,
                    thumbnailWidth: 128,
                    thumbnailHeight: 128,
                    oldFilename: $admin->image // <-- هنا تمرير الصورة القديمة
                );
                $admin->update([
                    'image' => $newFilename
                ]);
            }

            $admin->update($request->only(['name', 'email', 'mobile']));
            return redirect()->back()->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


    public function editePassword()
    {

        return view('dashboard.profile.editpassword');
    }

    public function updatePassword(PasswordRequest $request)
    {
        try {
            $admin = User::find(auth('admin')->user()->id);
            $admin->update([
                'password' => bcrypt($request->password)

            ]);
            return redirect()->back()->with(['success' => __('dashboard.Edit_success')]);
        } catch (Exception $ex) {
            return redirect()->back()->with(['error' => __('dashboard.Error_DB')]);
        }
    }


    public function saveDesktopToken(Request $request)
    {
        $request->validate([
            'desktop_token' => 'required|string'
        ]);


        $user = User::find(auth('admin')->user()->id);


        // فقط احفظ التوكن إذا لم يكن موجود مسبقًا
        if ($user->desktop_token != $request->desktop_token) {

            $user->desktop_token = $request->desktop_token;
            $user->save();


            return response()->json([
                'success' => true,
                'message' => 'Desktop token saved successfully'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Desktop token already exists, no changes made'
        ]);
    }
}
