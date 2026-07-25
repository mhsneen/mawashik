<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Imagestore;
use App\Http\Requests\Dashboard\ImagestoreRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;

use App\Services\ImageUploadService;

class ImagestoreController extends Controller
{
    protected string $folder = 'imagestore';
    protected ImageUploadService $imageService;

    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function storeproduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'multipleimage' => 'required|array|min:1|max:5',
            'multipleimage.*' => 'image|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($request->product_id);

            foreach ($request->file('multipleimage') as $file) {

                // 👇 هنا استخدام ImageUploadService
                $filename = $this->imageService->uploadAndProcess(
                    image: $file,
                    folder: $this->folder,
                    thumbnailWidth: 800,
                    thumbnailHeight: 800
                );

                $product->productimages()->create([
                    'filename'  => $filename,
                    'extension' => 'webp',
                    'size'      => $file->getSize(),
                ]);
            }

            DB::commit();

            return response()->json([
                'type' => 'success',
                'msg'  => __('dashboard.Save_success')
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'type' => 'error',
                'msg'  => __('dashboard.Error_DB')
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $imagestore = Imagestore::findOrFail($request->key);

            // 👇 حذف الصور عبر السيرفس
            $this->imageService->deleteOldImages(
                $imagestore->filename,
                $this->folder
            );

            $imagestore->delete();

            DB::commit();

            return response()->json([
                'type' => 'success',
                'msg' => __('dashboard.Delete_success')
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'type' => 'error',
                'msg' => __('dashboard.Error_DB')
            ], 500);
        }
    }
}
