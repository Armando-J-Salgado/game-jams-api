<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HandoverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'attachment' => $this->attachment,
            'is_delivered' => $this->is_delivered,
            'module_id' => $this->module_id,
            'team_id' => $this->team_id,
            'score' => $this->score,
            'date_of_submission' => $this->date_of_submission
        ];
    }
}
