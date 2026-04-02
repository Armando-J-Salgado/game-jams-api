<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'username'=>$this->username,
            'email'=>$this->email,
            'name'=>$this->name,
            'lastname'=>$this->lastname,
            'dui'=>$this->dui,
            'team_id'=>$this->team_id,
            'role_id'=>$this->role_id
        ];
    }
}
