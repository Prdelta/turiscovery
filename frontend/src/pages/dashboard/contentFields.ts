import { CANDELARIA_CATEGORIES, DIFFICULTY, DISCOUNT_TYPES, EVENT_CATEGORIES, LOCALE_CATEGORIES, type ContentType } from '../../lib/content'

export type FieldKind = 'text' | 'textarea' | 'number' | 'select' | 'date' | 'datetime' | 'url' | 'email' | 'tel' | 'tags' | 'checkbox' | 'locale' | 'anfitrion'

export interface FieldDef {
  name: string
  label: string
  kind: FieldKind
  required?: boolean
  options?: Record<string, string>
  hint?: string
  min?: number
  max?: number
  step?: string
  /** Mostrar solo si se cumple la condición sobre el formulario */
  when?: (values: Record<string, unknown>) => boolean
  wide?: boolean
}

export interface FormConfig {
  fields: FieldDef[]
  /** Pide ubicación en el mapa */
  location?: 'required' | 'optional'
  uploadFolder: string
  titleField: 'title' | 'name'
}

export const FORM_CONFIG: Record<ContentType, FormConfig> = {
  locales: {
    titleField: 'name',
    uploadFolder: 'locales',
    location: 'required',
    fields: [
      { name: 'name', label: 'Nombre del local', kind: 'text', required: true, wide: true },
      { name: 'category', label: 'Categoría', kind: 'select', options: LOCALE_CATEGORIES, required: true },
      { name: 'phone', label: 'Teléfono', kind: 'tel' },
      { name: 'email', label: 'Correo de contacto', kind: 'email' },
      { name: 'website', label: 'Sitio web', kind: 'url', hint: 'Incluye https://' },
      { name: 'address', label: 'Dirección', kind: 'text', required: true, wide: true, hint: 'Calle, número y referencia. Ejemplo: Jr. Lima 345, a media cuadra de la Plaza de Armas.' },
      { name: 'description', label: 'Descripción', kind: 'textarea', required: true, wide: true },
    ],
  },
  experiencias: {
    titleField: 'title',
    uploadFolder: 'experiencias',
    location: 'optional',
    fields: [
      { name: 'title', label: 'Título', kind: 'text', required: true, wide: true },
      { name: 'difficulty', label: 'Dificultad', kind: 'select', options: DIFFICULTY, required: true },
      { name: 'locale_id', label: 'Local que la ofrece', kind: 'locale' },
      { name: 'anfitrion_id', label: 'Quién recibe al viajero', kind: 'anfitrion', hint: 'Preséntalo en la sección Anfitriones con su foto y su saludo.' },
      { name: 'price_pen', label: 'Precio por persona (S/)', kind: 'number', min: 0, step: '0.01', hint: 'Usa 0 si es gratis.' },
      { name: 'duration_hours', label: 'Duración (horas)', kind: 'number', min: 1 },
      { name: 'max_participants', label: 'Cupo máximo por fecha', kind: 'number', min: 1, hint: 'Vacío = sin límite.' },
      { name: 'tags', label: 'Etiquetas', kind: 'tags', hint: 'Separadas por comas. Ejemplo: lago, familia, cultura' },
      { name: 'address', label: 'Punto de encuentro', kind: 'text', wide: true },
      { name: 'description', label: 'Descripción', kind: 'textarea', required: true, wide: true },
      { name: 'content', label: 'Itinerario y detalles', kind: 'textarea', wide: true, hint: 'Qué incluye, horarios, qué llevar.' },
    ],
  },
  eventos: {
    titleField: 'title',
    uploadFolder: 'eventos',
    location: 'optional',
    fields: [
      { name: 'title', label: 'Título', kind: 'text', required: true, wide: true },
      { name: 'category', label: 'Categoría', kind: 'select', options: EVENT_CATEGORIES, required: true },
      { name: 'locale_id', label: 'Local organizador', kind: 'locale' },
      { name: 'start_time', label: 'Inicio', kind: 'datetime', required: true },
      { name: 'end_time', label: 'Fin', kind: 'datetime', required: true },
      { name: 'ticket_price', label: 'Precio de entrada (S/)', kind: 'number', min: 0, step: '0.01', hint: 'Vacío o 0 = entrada libre.' },
      { name: 'address', label: 'Lugar', kind: 'text', wide: true },
      { name: 'description', label: 'Descripción', kind: 'textarea', required: true, wide: true },
      { name: 'content', label: 'Programa', kind: 'textarea', wide: true },
    ],
  },
  promociones: {
    titleField: 'title',
    uploadFolder: 'promociones',
    fields: [
      { name: 'title', label: 'Título', kind: 'text', required: true, wide: true },
      { name: 'locale_id', label: 'Local donde se usa', kind: 'locale', required: true },
      { name: 'discount_type', label: 'Tipo de descuento', kind: 'select', options: DISCOUNT_TYPES, required: true },
      { name: 'discount_percentage', label: 'Porcentaje (%)', kind: 'number', min: 1, max: 100, when: (v) => v.discount_type === 'percentage', required: true },
      { name: 'discount_amount', label: 'Monto de descuento (S/)', kind: 'number', min: 0, step: '0.01', when: (v) => v.discount_type === 'fixed', required: true },
      { name: 'original_price', label: 'Precio normal (S/)', kind: 'number', min: 0, step: '0.01' },
      { name: 'final_price', label: 'Precio con descuento (S/)', kind: 'number', min: 0, step: '0.01' },
      { name: 'start_date', label: 'Desde', kind: 'date', required: true },
      { name: 'end_date', label: 'Hasta', kind: 'date', required: true },
      { name: 'redemption_code', label: 'Código interno', kind: 'text', hint: 'Opcional. Para tu control en caja.' },
      { name: 'description', label: 'Descripción', kind: 'textarea', required: true, wide: true },
      { name: 'terms_conditions', label: 'Términos y condiciones', kind: 'textarea', wide: true },
    ],
  },
  candelaria: {
    titleField: 'title',
    uploadFolder: 'candelaria',
    fields: [
      { name: 'title', label: 'Título', kind: 'text', required: true, wide: true },
      { name: 'category', label: 'Tema', kind: 'select', options: CANDELARIA_CATEGORIES, required: true },
      { name: 'event_date', label: 'Fecha relacionada', kind: 'date' },
      { name: 'locale_id', label: 'Local relacionado', kind: 'locale' },
      { name: 'is_featured', label: 'Destacar en la página de Candelaria', kind: 'checkbox' },
      { name: 'description', label: 'Resumen', kind: 'textarea', required: true, wide: true },
      { name: 'content', label: 'Texto completo', kind: 'textarea', wide: true },
    ],
  },
}
