<?php

namespace App\Http\Resources\Api\V1\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name ?? '',
            'email'          => $this->email ?? '',
            'mobile'         => $this->mobile ?? '',
            'image_url'      => $this->image_url ?? '',
            'base_image_url' => $this->base_image_url ?? '',
            'role'           => $this->getRoleNames()->first() ?? '',
            // ✅ إضافة التوكن إذا تم تمريره كـ attribute (access_token)
            'token'          => $this->when(isset($this->access_token), $this->access_token),
        ];
    }
}
