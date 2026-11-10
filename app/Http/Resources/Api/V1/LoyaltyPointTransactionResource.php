<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class LoyaltyPointTransactionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'order_id'         => $this->order_id,
            'type'             => $this->type, // earn, redeem, refund, expire, adjustment
            'points'           => $this->points, // موجبة عند الكسب، سالبة عند الخصم
            'balance_after'    => $this->balance_after,
            'description'      => $this->description,
            'created_at_human' => $this->created_at ? Carbon::parse($this->created_at)->diffForHumans() : null,
        ];
    }
}
