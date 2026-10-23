import { type ContentType } from '@/shared/config/content'

export const MANAGED: ContentType[] = ['locales', 'experiencias', 'eventos', 'promociones', 'candelaria']

export function isManaged(type: string | undefined): type is ContentType {
  return MANAGED.includes(type as ContentType)
}
