<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'type' => 'likes',
                'like_id' => $this->id,
                'attributes' => []
            ],
            'links' => [
                'self' => url('/posts/'.$this->pivot->type_id),
            ]
        ];
    }
}
