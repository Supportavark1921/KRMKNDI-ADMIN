<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'status'       => $this->status,
            'name'         => $this->name,
            'phone'        => $this->phone,
            'address'      => $this->address,
            'subtotal'       => (float) $this->subtotal,
            'platform_fee'   => (float) $this->platform_fee,
            'gst_amount'     => (float) $this->gst_amount,
            'total_amount'   => (float) $this->total_amount,
            'payment_method' => $this->payment_method,
            'notes'        => $this->notes,
            'items'        => $this->whenLoaded('items', fn () =>
                $this->items->map(fn ($item) => [
                    'id'           => $item->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product_name,
                    'unit'         => $item->unit,
                    'price'        => (float) $item->price,
                    'quantity'     => $item->quantity,
                    'subtotal'     => (float) $item->subtotal,
                ])
            ),
            'created_at'   => $this->created_at?->toISOString(),
        ];
    }
}
