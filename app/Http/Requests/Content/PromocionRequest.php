<?php

namespace App\Http\Requests\Content;

class PromocionRequest extends ContentRequest
{
    protected function model(): string
    {
        return \App\Models\Promocion::class;
    }

    public function rules(): array
    {
        return [
            'title' => $this->required() . '|string|max:255',
            'locale_id' => $this->ownLocale(required: true),
            'discount_type' => $this->required() . '|in:2x1,percentage,fixed',
            'discount_percentage' => 'nullable|integer|min:1|max:100|required_if:discount_type,percentage',
            'discount_amount' => 'nullable|numeric|min:0|required_if:discount_type,fixed',
            'original_price' => 'nullable|numeric|min:0',
            'final_price' => 'nullable|numeric|min:0',
            'start_date' => $this->required() . '|date',
            'end_date' => $this->required() . '|date|after:start_date',
            'terms_conditions' => 'nullable|string',
            'redemption_code' => 'nullable|string|max:50',
            ...$this->commonRules(),
        ];
    }
}
