<?php

namespace App\Http\Requests\Content;

use App\Models\Anfitrion;
use App\Rules\OwnedByUser;

class ExperienciaRequest extends ContentRequest
{
    protected function model(): string
    {
        return \App\Models\Experiencia::class;
    }

    public function rules(): array
    {
        return [
            'title' => $this->required().'|string|max:255',
            'content' => 'nullable|string',
            'locale_id' => $this->ownLocale(),
            'anfitrion_id' => ['nullable', 'integer', 'exists:anfitriones,id', new OwnedByUser(Anfitrion::class, 'Ese anfitrión no pertenece a tu cuenta.')],
            'difficulty' => $this->required().'|in:easy,medium,hard',
            'duration_hours' => 'nullable|integer|min:1|max:240',
            'price_pen' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'altitude_m' => 'nullable|integer|min:3000|max:6500',
            'effort_note' => 'nullable|string|max:255',
            'meeting_point' => 'nullable|string|max:255',
            'meeting_time' => 'nullable|date_format:H:i',
            'tags' => 'nullable|array|max:20',
            'tags.*' => 'string|max:40',
            ...$this->commonRules(),
            ...$this->locationRules(),
        ];
    }
}
