<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title'=> $this->title,
            'description'=>$this->description,
            'attachments'=>$this->attachments,
            'due_date'=>$this->due_date,
            'competition'=>[
                'id'=>$this->competition_id,
                'name'=>$this->competition->name,
            ],
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at,
        ];
    }
}
