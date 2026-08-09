<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderStatusResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'name' => $this->name, // سيقوم بإرجاع الاسم حسب اللغة المحددة في التطبيق
        ];
    }
}
