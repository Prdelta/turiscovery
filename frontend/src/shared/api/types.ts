export type Role = 'tourist' | 'socio' | 'admin'

export type Interest = 'experiencias' | 'eventos' | 'promociones' | 'locales' | 'candelaria'
export type TravelPace = 'suave' | 'moderado' | 'intenso'

/** Preferencias del usuario (el backend completa los valores por defecto) */
export interface UserPreferences {
  notifications: { bookings: boolean; promotions: boolean }
  interests: Interest[]
  pace: TravelPace | null
}

export interface User {
  id: number
  name: string
  email: string
  role: Role
  avatar?: string | null
  phone?: string | null
  bio?: string | null
  is_active?: boolean
  created_at?: string
  deactivated_at?: string | null
  email_verified_at?: string | null
  preferences?: UserPreferences | null
  favorites_count?: number
  reviews_count?: number
  bookings_count?: number
  event_attendances_count?: number
  coupons_count?: number
  locales_count?: number
  eventos_count?: number
  experiencias_count?: number
  promociones_count?: number
  deactivated_by?: { id: number; name: string } | null
}

export interface Coordinates {
  lat: number | null
  lng: number | null
}

interface BaseContent {
  id: number
  user_id: number
  user?: Pick<User, 'id' | 'name' | 'email'>
  description: string
  images?: string[] | null
  image_url?: string | null
  is_active: boolean
  created_at: string
  updated_at: string
  reviews?: Review[]
}

interface WithAddress {
  address?: string | null
  formatted_address?: string | null
  short_address?: string | null
  latitude?: number | null
  longitude?: number | null
  coordinates?: Coordinates | null
}

export interface Locale extends BaseContent, WithAddress {
  name: string
  category: string
  phone?: string | null
  email?: string | null
  website?: string | null
  district?: string | null
  city?: string | null
}

export interface Experiencia extends BaseContent, WithAddress {
  title: string
  content?: string | null
  locale_id?: number | null
  locale?: Locale | null
  difficulty: 'easy' | 'medium' | 'hard'
  duration_hours?: number | null
  price_pen?: string | number | null
  max_participants?: number | null
  tags?: string[] | null
  anfitrion_id?: number | null
  anfitrion?: Anfitrion | null
  altitude_m?: number | null
  effort_note?: string | null
  meeting_point?: string | null
  meeting_time?: string | null
}

export interface DayAvailability {
  date: string
  departure_time?: string | null
  capacity?: number | null
  /** null = sin cupo máximo */
  available?: number | null
}

export interface Evento extends BaseContent, WithAddress {
  title: string
  content?: string | null
  locale_id?: number | null
  locale?: Locale | null
  start_time: string
  end_time: string
  ticket_price?: string | number | null
  category: string
}

export interface Promocion extends BaseContent {
  title: string
  locale_id: number
  locale?: Locale | null
  discount_type: '2x1' | 'percentage' | 'fixed'
  discount_percentage?: number | null
  discount_amount?: string | number | null
  original_price?: string | number | null
  final_price?: string | number | null
  start_date: string
  end_date: string
  terms_conditions?: string | null
  redemption_code?: string | null
}

export interface Candelaria extends BaseContent {
  title: string
  content?: string | null
  locale_id?: number | null
  event_date?: string | null
  category: string
  is_featured: boolean
}

export interface Danza {
  id: number
  name: string
  type: 'autoctona' | 'mestiza'
  description: string
  history?: string | null
  image_url?: string | null
  video_url?: string | null
  region?: string | null
  characteristics?: string | null
  order?: number | null
  is_featured: boolean
  is_active: boolean
}

export interface GalleryPhoto {
  id: number
  title: string
  description?: string | null
  image_url: string
  year: number
  order?: number | null
  is_active: boolean
}

export interface Review {
  id: number
  user_id: number
  user?: Pick<User, 'id' | 'name' | 'avatar'>
  reviewable_type: string
  reviewable_id: number
  reviewable?: { id: number; title?: string; name?: string } | null
  rating: number
  title?: string | null
  comment?: string | null
  created_at: string
}

export interface Favorite {
  id: number
  favoritable_type: string
  favoritable_id: number
  favoritable?: (Partial<Experiencia & Evento & Promocion & Locale & Candelaria> & { id: number }) | null
  created_at: string
}

export interface Booking {
  id: number
  experiencia_id: number
  experiencia?: Experiencia
  booking_date: string
  participants: number
  total_price: string | number
  status: 'pending' | 'confirmed' | 'cancelled' | 'completed'
  notes?: string | null
}

export interface EventAttendance {
  id: number
  evento_id: number
  evento?: Evento
  status: 'confirmed' | 'maybe' | 'cancelled'
  guests?: number | null
}

export interface UserCoupon {
  id: number
  promocion_id: number
  promocion?: Promocion
  coupon_code: string
  status: 'available' | 'used' | 'expired'
  claimed_at?: string | null
  used_at?: string | null
  expires_at?: string | null
}

export type AnyContent = Locale | Experiencia | Evento | Promocion | Candelaria

/* ---------- Candelaria en vivo ---------- */

export type ProgramType = 'misa' | 'procesion' | 'concurso' | 'entrada' | 'parada' | 'otro'

export interface ProgramItem {
  id: number
  title: string
  description?: string | null
  type: ProgramType
  starts_at: string
  ends_at: string
  location_name?: string | null
  latitude?: number | null
  longitude?: number | null
  route?: [number, number][] | null
  is_active: boolean
  performances_count?: number
  done_count?: number
}

export interface Performance {
  id: number
  program_item_id: number
  candelaria_danza_id?: number | null
  danza?: Pick<Danza, 'id' | 'name' | 'type' | 'image_url'> | null
  group_name: string
  order_number: number
  status: 'pending' | 'performing' | 'done' | 'skipped'
  started_at?: string | null
  finished_at?: string | null
  position?: number
  eta?: string
}

export interface LiveState {
  live: boolean
  item: ProgramItem | null
  next_item?: ProgramItem | null
  current?: Performance | null
  upcoming?: Performance[]
  done_count?: number
  total?: number
  minutes_per_group?: number
  updated_at?: string
}

/* ---------- Anfitriones ---------- */

export type HostLanguage = 'quechua' | 'aimara' | 'espanol' | 'ingles'

export interface Anfitrion {
  id: number
  user_id?: number
  name: string
  community?: string | null
  role_title?: string | null
  languages?: HostLanguage[] | null
  photo_url?: string | null
  story?: string | null
  greeting?: string | null
  greeting_translation?: string | null
  audio_url?: string | null
  is_active?: boolean
  experiencias_count?: number
  experiencias?: Experiencia[]
}
