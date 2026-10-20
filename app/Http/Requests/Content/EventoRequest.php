<?php

namespace App\Http\Requests\Content;

class EventoRequest extends ContentRequest
{
    protected function model(): string
    {
        return \App\Models\Evento::class;
    }

    public function rules(): array
    {
        return [
            'title' => $this->required().'|string|max:255',
            'content' => 'nullable|string',
            'locale_id' => $this->ownLocale(),
            // Al crear, el evento debe ser futuro; al editar se permite corregir fechas pasadas
            'start_time' => $this->isUpdate() ? 'sometimes|required|date' : 'required|date|after:now',
            'end_time' => $this->required().'|date|after:start_time',
            'ticket_price' => 'nullable|numeric|min:0',
            'category' => $this->required().'|in:concert,festival,nightlife,cultural,sports,exhibition,other',
            ...$this->commonRules(),
            ...$this->locationRules(),
        ];
    }
}
