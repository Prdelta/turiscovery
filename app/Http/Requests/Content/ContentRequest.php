<?php

namespace App\Http\Requests\Content;

use App\Models\Locale;
use App\Rules\OwnedByUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

/**
 * Base de las solicitudes de crear/editar contenido de socios.
 * Al crear (POST) los campos obligatorios son requeridos; al editar (PUT/PATCH)
 * solo se validan los que llegan.
 */
abstract class ContentRequest extends FormRequest
{
    /** Modelo que se crea o edita (para aplicar su Policy) */
    abstract protected function model(): string;

    /**
     * Al editar, primero se comprueba que el usuario pueda modificar el registro
     * (su Policy), así un socio ajeno recibe 403 y no errores de validación.
     */
    public function authorize(): bool
    {
        if ($this->isUpdate()) {
            $record = $this->model()::find($this->route('id'));
            if ($record) {
                Gate::authorize('update', $record);
            }
        }

        return true;
    }

    protected function isUpdate(): bool
    {
        return $this->isMethod('put') || $this->isMethod('patch');
    }

    /** "required" al crear; "sometimes|required" al editar */
    protected function required(): string
    {
        return $this->isUpdate() ? 'sometimes|required' : 'required';
    }

    /** Solo se puede asociar un local propio (o cualquiera, si es admin) */
    protected function ownLocale(bool $required = false): array
    {
        return [
            $required ? $this->required() : 'nullable',
            'integer',
            'exists:locales,id',
            new OwnedByUser(Locale::class, 'Solo puedes asociar tus propios locales.'),
        ];
    }

    /** Campos comunes a todo contenido */
    protected function commonRules(): array
    {
        return [
            'description' => $this->required().'|string',
            'images' => 'nullable|array|max:12',
            'images.*' => 'string|max:500',
            'is_active' => 'boolean',
        ];
    }

    /** Ubicación en el mapa (opcional salvo que se indique) */
    protected function locationRules(bool $required = false, ?array $bounds = null): array
    {
        [$latMin, $latMax, $lngMin, $lngMax] = $bounds ?? [-90, 90, -180, 180];
        $presence = $required ? $this->required() : 'nullable';

        return [
            'latitude' => "{$presence}|numeric|between:{$latMin},{$latMax}|required_with:longitude",
            'longitude' => "{$presence}|numeric|between:{$lngMin},{$lngMax}|required_with:latitude",
            'address' => 'nullable|string|max:500',
        ];
    }

    /** Dirección por partes; con ellas se arman formatted_address y short_address (HasLocation) */
    protected function addressRules(): array
    {
        return [
            'street' => 'nullable|string|max:255',
            'street_number' => 'nullable|string|max:20',
            'district' => 'nullable|string|max:100',
            'city' => 'sometimes|string|max:100',
            'region' => 'sometimes|string|max:100',
            'country' => 'sometimes|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'address_notes' => 'nullable|string|max:500',
        ];
    }

    /**
     * Ciudad, región y país tienen valor por defecto (Puno, Puno, Perú) y no admiten vacío:
     * si llegan vacíos se ignoran y queda el valor que ya tenían.
     */
    protected function prepareForValidation(): void
    {
        $blank = array_filter(['city', 'region', 'country'], fn ($field) => $this->exists($field) && blank($this->input($field)));

        if ($blank) {
            $this->replace($this->except($blank));
        }
    }

    public function hasCoordinates(): bool
    {
        return $this->filled('latitude') && $this->filled('longitude');
    }
}
