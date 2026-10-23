import { queryOptions } from '@tanstack/react-query'
import { getData, type Paginated } from '@/shared/api/client'
import type { EventAttendance, Review, User, UserCoupon } from '@/shared/api/types'

/** Datos de la cuenta del viajero: perfil (con contadores), cupones, asistencias y reseñas */
export const accountKeys = {
  all: ['account'] as const,
  profile: () => [...accountKeys.all, 'profile'] as const,
  coupons: (page?: number) => (page === undefined ? ([...accountKeys.all, 'coupons'] as const) : ([...accountKeys.all, 'coupons', page] as const)),
  attendances: (page?: number) =>
    page === undefined ? ([...accountKeys.all, 'attendances'] as const) : ([...accountKeys.all, 'attendances', page] as const),
  reviews: (page: number) => [...accountKeys.all, 'reviews', page] as const,
}

export const accountQueries = {
  profile: () => queryOptions({ queryKey: accountKeys.profile(), queryFn: () => getData<User>('/profile') }),
  coupons: (page = 1) =>
    queryOptions({ queryKey: accountKeys.coupons(page), queryFn: () => getData<Paginated<UserCoupon>>('/user-coupons', { page }) }),
  attendances: (page = 1) =>
    queryOptions({ queryKey: accountKeys.attendances(page), queryFn: () => getData<Paginated<EventAttendance>>('/event-attendances', { page }) }),
  reviews: (page = 1) =>
    queryOptions({ queryKey: accountKeys.reviews(page), queryFn: () => getData<Paginated<Review>>('/my-reviews', { page }) }),
}
