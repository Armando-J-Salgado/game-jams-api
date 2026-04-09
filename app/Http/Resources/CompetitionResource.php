<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
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
            'name'=>$this->name,
            'description'=>$this->description,
            'prize'=> $this->prize_information,
            'tools'=> $this->tools_information,
            'maximum_number_of_teams' => $this->max_teams,
            'total_number_of_teams'=> !$this->total_teams ? 0 : $this->total_teams,
            'registration_start_date'=> $this->start_date,
            'registration_closing_date' => $this->end_date,
            'competition_is_finished' => $this->is_finished === null || $this->is_finished === 0 || !$this->is_finished ? false : true,
            'category' => $this->category->name,
            'admin'=> $this->user->name, //Agregar el UserResource cuando este
            'teams'=> $this->teams->pluck('name'), //Agregar el team Resource cuando este
            'modules'=>$this->modules->pluck('title'),
        ];
    }
}
