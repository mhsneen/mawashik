<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class NotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'details'            => $this->details,
            'user_id'            => $this->user_id,

            // نتأكد أن القيمة ترجع true/false وليس 1/0
            'is_read'            => (bool) $this->is_read,

            // ✅ استخدام Carbon::parse لتحويل النص إلى كائن تاريخ بأمان
            'read_at'            => $this->read_at ? Carbon::parse($this->read_at)->toIso8601String() : null,

            // ✅ استخدام Carbon::parse هنا أيضاً لتجنب نفس الخطأ مع created_at
            'created_at_human'   => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans() : null,
        ];
    }
}