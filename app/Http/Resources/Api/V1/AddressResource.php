<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        // دالة مساعدة لتحويل null إلى نص فارغ "" (أو يمكنك تركها null لو يفضل التطبيق ذلك)
        $val = fn($v) => $v ?? '';

        return [
            'id'               => $this->id,
            'title'            => $val($this->title),
            'address'          => $val($this->address),
            'latitude'         => $this->latitude, // الإحداثيات يفضل تركها كما هي
            'longitude'        => $this->longitude,
            'building_number'  => $val($this->building_number),
            'floor'            => $val($this->floor),
            'apartment_number' => $val($this->apartment_number),
            'note'             => $val($this->note),
            'is_default'       => (bool) $this->is_default, // نتأكد أنها ترجع true/false وليس 1/0
        ];
    }
}
