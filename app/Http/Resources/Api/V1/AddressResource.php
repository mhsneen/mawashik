<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray($request)
    {
        $val = fn($v) => $v ?? '';

        return [
            'id'               => $this->id,
            'title'            => $val($this->title),
            'address'          => $val($this->address),

            'latitude'         => $this->latitude !== null
                ? number_format((float) $this->latitude, 8, '.', '')
                : '',

            'longitude'        => $this->longitude !== null
                ? number_format((float) $this->longitude, 8, '.', '')
                : '',

            'building_number'  => $val($this->building_number),
            'floor'            => $val($this->floor),
            'apartment_number' => $val($this->apartment_number),
            'note'             => $val($this->note),
            'is_default'       => (bool) $this->is_default,
        ];
    }
}