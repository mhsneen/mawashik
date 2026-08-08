@php
    // بناء المسار المحلي للصورة بشكل صحيح
    $logoFileName = $itemstore_info['store_logo'] ?? '';
    $store_logo_img = public_path('uploads/settings/thumbnail/' . $logoFileName);

    // التحقق من وجود الملف قبل محاولة معالجته لتفادي الأخطاء
    if (!empty($logoFileName) && file_exists($store_logo_img)) {
        $ext = pathinfo($store_logo_img, PATHINFO_EXTENSION);

        // تحقق إذا كانت الصورة WebP وحولها إلى PNG
        if (strtolower($ext) === 'webp') {
            $png_path = preg_replace('/\.webp$/i', '.png', $store_logo_img);

            // إذا لم يكن الـ PNG موجوداً مسبقاً، قم بتحويله
            if (!file_exists($png_path)) {
                $image = imagecreatefromwebp($store_logo_img);
                imagepng($image, $png_path);
                imagedestroy($image);
            }
            $store_logo_img = $png_path; // استخدم الصورة المحوّلة
        }
    } else {
        $store_logo_img = null; // في حال عدم وجود شعار
    }
@endphp


<br /><br />
<table style="width: 100%">
    <tr>
        <td rowspan="4" align="left">
            @if($store_logo_img)
                {{-- في TCPDF: نستخدم المسار المحلي (public_path)، ونحدد العرض كـ Attribute فقط --}}
                <img src="{{ $store_logo_img }}" width="80" alt="شعار المتجر">
            @endif
        </td>
        <td align="right" style="line-height: 15px"><br /><br />
            {{ $itemstore_info['store_name'] }}</td>
    </tr>

    <tr>
        <td align="right" style="line-height: 15px">{{ __('dashboard.Contact_Number') }} :
            {{ $itemstore_info['store_mobile'] }}</td>
    </tr>
    <tr>
        <td align="right" style="line-height: 15px"> <span
                style="direction: ltr">{{ $itemstore_info['store_email'] }}</span>
            {{ __('dashboard.Email') }} :</td>
    </tr>
</table>




<br /><br />

<table style="width: 100%">
    <tr>
        <td width="20%" align="right" style="line-height: 15px; direcion:rtl"></td>

        <td width="80%" align="right" style="line-height: 15px">{{ __('dashboard.Client_Name') }} :
            {{ $item->user->name }}</td>
    </tr>
    <tr>
        <td width="20%"align="right" style="line-height: 15px; direcion:rtl">{{ __('dashboard.Invoice_No') }} :
            {{ $item->order_number }}</td>

        <td width="80%"align="right" style="line-height: 15px">{{ __('dashboard.Mobile') }} :
            {{ $item->user->mobile }}</td>
    </tr>
    <tr>
        <td width="20%" align="right" style="line-height: 15px; direcion:rtl">{{ __('dashboard.Issue_Date') }} :
            {{ $item->created_at->format('d/m/Y') }}</td>


    </tr>
</table>
