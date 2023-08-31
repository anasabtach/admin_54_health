<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class Deal extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'   => $this->id,
            'user' => $this->whenLoaded('user'),
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => Storage::url($this->image_url),
            'blur_image' => $this->blur_image,
            'description' => $this->description,
            'price_type' => $this->price_type,
            'price' => $this->price,
            'sale_price' => $this->sale_price,
            'discount_percentage' => $this->discount_percentage,
            'time_bound' => $this->time_bound,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'redeem_type' => $this->redeem_type,
            'redeem_length' => $this->redeem_length,
            'deal_code' => $this->deal_code,
            'status' => $this->status,
            'paid_promotion' => $this->paid_promotion,
            'paid_pormotion_expire_date' => $this->paid_pormotion_expire_date,
            'total_rating' => $this->total_rating,
            'total_review' => $this->total_review,
            'deal_redeemed_count' => $this->deal_redeemed_count,
            'deal_redeemed_user_count' => $this->deal_redeemed_user_count,
            'marketing_deal_redeemed_count' => $this->marketing_deal_redeemed_count,
            'marketing_deal_redeemed_user_count' => $this->marketing_deal_redeemed_user_count,
            'is_favourite' => $this->is_favourite,
            'is_redeem'  => $this->is_redeem,
            'deal_type'  => $this->deal_type,
             'how_to_redeem' => $this->how_to_redeem,
            'created_at' => $this->created_at,
            'rating'=>$this->rating,
        ];
    }
}
