<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

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

            // تنسيق التاريخ إذا كان موجوداً، أو إرجاع null
            'read_at'            => $this->read_at ? $this->read_at->toIso8601String() : null,

            // إضافة الحقل الجديد بصيغة "منذ كذا"
            'created_at_human'   => $this->created_at?->diffForHumans(),
        ];
    }
}
