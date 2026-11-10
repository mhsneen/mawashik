<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Log;

class ImageUploadService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * تقوم برفع ومعالجة الصورة وإنشاء مصغرة لها، مع حذف الصورة القديمة اختياريًا.
     *
     * @param UploadedFile $image ملف الصورة المرفوع.
     * @param string $folder اسم المجلد الذي سيتم حفظ الصورة فيه.
     * @param int $thumbnailWidth عرض الصورة المصغرة.
     * @param int $thumbnailHeight ارتفاع الصورة المصغرة.
     * @param string|null $oldFilename اسم الصورة القديمة لحذفها (اختياري).
     * @return string اسم الملف الفريد الذي تم إنشاؤه.
     * @throws Exception في حالة فشل العملية.
     */
    public function uploadAndProcess(
        UploadedFile $image,
        string $folder,
        int $thumbnailWidth = 128,
        int $thumbnailHeight = 128,
        ?string $oldFilename = null // <-- المعامل الجديد
    ): string {
        try {
            // --- الجزء الجديد: حذف الصورة القديمة إذا وجدت ---
            if ($oldFilename) {
                $this->deleteOldImages($oldFilename, $folder);
            }

            // تحديد مسارات المجلدات
            $destinationPath = public_path("uploads/{$folder}");
            $destinationPathThumbnail = public_path("uploads/{$folder}/thumbnail");

            // التأكد من وجود المجلدات
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            if (!file_exists($destinationPathThumbnail)) {
                mkdir($destinationPathThumbnail, 0755, true);
            }

            // إنشاء اسم فريد للملف
            $filename = Str::uuid() . '.webp';

            // معالجة الصورة الأصلية
            $mainImage = $this->manager->read($image->getPathname());
            $mainImage->scaleDown(1920, 1080);
            $mainImage->toWebp(quality: 90)->save("{$destinationPath}/{$filename}");

            // معالجة الصورة المصغرة
            $thumbnailImage = $this->manager->read($image->getPathname());

            if ($thumbnailImage->width() < $thumbnailWidth || $thumbnailImage->height() < $thumbnailHeight) {
                $thumbnailImage->scaleDown($thumbnailWidth, $thumbnailHeight);
            } else {
                $thumbnailImage->cover($thumbnailWidth, $thumbnailHeight);
            }

            $thumbnailImage->toWebp(quality: 85)->save("{$destinationPathThumbnail}/{$filename}");

            return $filename;

        } catch (Exception $e) {
            Log::error("Image Upload Service Error: " . $e->getMessage());
            throw new Exception("فشلت عملية معالجة الصورة. يرجى المحاولة مرة أخرى.");
        }
    }

    /**
     * دالة مساعدة لحذف الصور القديمة.
     */
    public function deleteOldImages(string $filename, string $folder): void
    {
        $oldImagePath = public_path("uploads/{$folder}/{$filename}");
        $oldThumbPath = public_path("uploads/{$folder}/thumbnail/{$filename}");

        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }
        if (file_exists($oldThumbPath)) {
            unlink($oldThumbPath);
        }
    }
}
