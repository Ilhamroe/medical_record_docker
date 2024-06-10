<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClinicResource extends JsonResource
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
            'dokter' => $this->doctor->name,
            'patient' => $this->patient->name,
            'dated' => $this->dated,
            'symptom' => $this->symptom,
            'diagnosis' => $this->diagnosis,
            'drug' => $this->drug,
            'advice' => $this->advice,
        ];
    }
}
