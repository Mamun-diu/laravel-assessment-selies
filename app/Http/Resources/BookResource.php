<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request = [])
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'author' => $this->author,
            'sale_price' => $this->sale_price,
            'discount_price' => $this->discount_price,
            'status' => $this->status,
            'image' => $this->fileUrl(),
            'created_at' => $this->created_at,
        ];
    }
}
