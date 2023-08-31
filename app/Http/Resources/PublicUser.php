<?php

namespace App\Http\Resources;

use App\Helpers\CustomHelper;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class PublicUser extends JsonResource
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
           'id'               => $this->id,
           'name'             => $this->name,
           'business_name'    => $this->business_name,
           'business_category_id' => $this->business_category_id,
           'business_category'    => $this->whenloaded('businessCategory'),
           'promote_category'    => $this->whenloaded('promoteCategory'),
           'promote_category_id' => $this->promote_category_id,
           'slug'             => $this->slug,
           'email'            => $this->email,
           'mobile_no'        => $this->mobile_no,
           'image_url'        => !empty($this->image_url) ? Storage::url($this->image_url) : URL::to('images/user-placeholder.jpg'),
           'banner_image_url' => !empty($this->banner_image_url) ? Storage::url($this->banner_image_url) : URL::to('images/profile-benner.jpg'),
           'blur_image'       => !empty($this->image_url) ? $this->blur_image : 'LKQ,L0of~qof_3fQ%Mj[WBfQM{fQ',
           'id_card'          => !empty($this->id_card) ? Storage::url($this->id_card) : URL::to('images/id_card.png'),
           'profession'       => $this->profession,
           'country'          => $this->country,
           'state'            => $this->state,
           'city'             => $this->city,
           'zipcode'          => $this->zipcode,
           'address'          => $this->address,
           'latitude'         => $this->latitude,
           'longitude'        => $this->longitude,
           'working_days'     => $this->working_days,
           'open_time'        => $this->open_time,
           'close_time'       => $this->close_time,
           'about'            => $this->about,
           'product_service'  => $this->product_service,
           'total_rating'     => $this->total_rating,
           'total_review'     => $this->total_review,
           'site_url'         => $this->site_url,
           'is_web_based_business' => $this->is_web_based_business,
           'business_hours'        => $this->business_hours,
           'organization_name'     => $this->organization_name,
           'created_at'       => $this->created_at,
       ];
    }
}
