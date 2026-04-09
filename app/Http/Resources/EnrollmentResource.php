<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'team' => [
                'name' => $this->resource['team']->name,
                'members' => $this->resource['team']->users->map(fn($user) => [
                    'name' => $user->name,
                    'lastname' => $user->lastname,
                ]),
            ],
            'competition' => [
                'name' => $this->resource['competition']->name,
                'end_date' => $this->resource['competition']->end_date,
            ],
            'modules' => $this->resource['competition']->modules->map(fn($module) => [
                'id' => $module->id,
                'title' => $module->title,
                'due_date' => $module->due_date,
            ]),
        ];
    }
}
