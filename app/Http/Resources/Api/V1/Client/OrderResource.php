<?php

namespace App\Http\Resources\Api\V1\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'order_number'     => $this->order_number,

            'status_id'        => $this->status_id,
            'status_name'      => $this->status?->name ?? '',
            'is_read'          => (bool) $this->read,

            'created_at'       => $this->created_at?->format('Y-m-d H:i'),
            'created_at_human' => $this->created_at?->diffForHumans(),

            'delivery_type'       => $this->delivery_type,
            'delivery_type_label' => $this->delivery_type_label,
            'delivery_date'       => $this->delivery_date?->format('Y-m-d') ?? '',
            'delivery_time'       => $this->delivery_time ?? '',
            'distance'            => $this->distance ?? '',

            'payment_method'       => $this->payment_method,
            'payment_method_label' => $this->payment_method_label,
            'payment_status'       => $this->payment_status,
            'payment_status_label' => $this->payment_status_label,

            'note'      => $this->note ?? '',
            'address'   => $this->address ?? '',
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,

            'total_items'      => $this->total_items,
            'total_price'      => (float) $this->total_price,
            'discount_amount'  => (float) $this->discount_amount,
            'coupon_code'      => $this->coupon_code ?? '',
            'added_tax'        => (float) $this->added_tax,
            'tax_amount'       => (float) $this->tax_amount,
            'delivery_price'   => (float) $this->delivery_price,
            'redeemed_points'  => (int) $this->redeemed_points,
            'redeemed_points_value' => (float) $this->redeemed_points_value,
            'earned_points'    => (int) $this->earned_points,
            'final_price'      => (float) $this->final_price,
            'currency'         => $this->currency,

            // يظهر فقط وقت "قيد التوصيل" ليعطيه العميل للسائق
            'delivery_pin' => $this->when(
                $this->delivery_type === 'delivery' && $this->status_id === 4,
                $this->delivery_pin
            ),

            'driver' => $this->whenLoaded('driver', function () {
                return $this->driver ? [
                    'id'        => $this->driver->id,
                    'name'      => $this->driver->name,
                    'mobile'    => $this->driver->mobile,
                    'image_url' => $this->driver->image_url,
                ] : null;
            }),

            'cancel_reason'       => $this->whenLoaded('cancelReason', fn () => $this->cancelReason ? [
                'id'   => $this->cancelReason->id,
                'name' => $this->cancelReason->name,
            ] : null),
            'cancel_other_reason' => $this->cancel_other_reason ?? '',

            // ما المسموح للعميل يعمله على هذا الطلب حالياً
            'can_cancel'          => in_array(6, $this->getAllowedStatuses()),
            'can_confirm_receipt' => $this->delivery_type === 'pickup' && $this->status_id === 3,
            // ✅ التعديل هنا: التقييم مسموح فقط إذا كانت الحالة 5 ولم يتم التقييم مسبقاً
            'can_review'          => $this->status_id === 5 && is_null($this->review),
            
            'review' => $this->whenLoaded('review', fn () => $this->review ? [
                'rating'  => $this->review->rating,
                'comment' => $this->review->comment,
            ] : null),

            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
