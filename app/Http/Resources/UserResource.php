<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'name' => $this->name,
            'designs' => $this->desings,
            'created_dates' => [
                'created_at_human' => $this->created_at->diffForHumans(),
                'creates_at' => $this->created_at
            ],
            'location' => $this->location,
            'about' => $this->about,
            'tagline' => $this->tagline,
            'formatted_address' => $this->formatted_address,
            'available_to_hire' => $this->available_to_hire,

        ];
    }
}
