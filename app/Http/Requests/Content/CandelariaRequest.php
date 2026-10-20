<?php

namespace App\Http\Requests\Content;

class CandelariaRequest extends ContentRequest
{
    protected function model(): string
    {
        return \App\Models\Candelaria::class;
    }

    public function rules(): array
    {
        return [
            'title' => $this->required().'|string|max:255',
            'content' => 'nullable|string',
            'locale_id' => $this->ownLocale(),
            'event_date' => 'nullable|date',
            'category' => $this->required().'|in:dance,history,costume,music,tradition,procession,other',
            'is_featured' => 'boolean',
            ...$this->commonRules(),
        ];
    }
}
