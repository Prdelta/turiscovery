<?php

namespace App\Http\Requests\Content;

class LocaleRequest extends ContentRequest
{
    /** Límites aproximados del departamento de Puno (incluye todas sus provincias) */
    public const PUNO_BOUNDS = [-17.4, -13.0, -71.2, -68.8];

    protected function model(): string
    {
        return \App\Models\Locale::class;
    }

    public function rules(): array
    {
        return [
            'name' => $this->required() . '|string|max:255',
            'category' => $this->required() . '|in:restaurant,hotel,tour_agency,craft_shop,museum,cultural_center,other',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            ...$this->commonRules(),
            ...$this->locationRules(required: true, bounds: self::PUNO_BOUNDS),
            'address' => $this->required() . '|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'latitude.between' => 'La ubicación debe estar dentro del departamento de Puno.',
            'longitude.between' => 'La ubicación debe estar dentro del departamento de Puno.',
        ];
    }
}
