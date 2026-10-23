import { clsx } from 'clsx'
import { initials } from '@/shared/lib/format'
import { Tocapu } from '@/shared/textile/Weave'

/** Marca: la estrella tejida de la Candelaria y el nombre */
export function Brand({ dark = false }: { dark?: boolean }) {
  return (
    <span className="flex items-center gap-2">
      <Tocapu motif="estrella" dye="cochinilla" size={32} framed={false} />
      <span className={clsx('font-display text-xl font-extrabold tracking-tight', dark ? 'text-white' : 'text-ink')}>Ñan Puno</span>
    </span>
  )
}

/** Foto de perfil o iniciales sobre morado */
export function Avatar({ name, src, size = 'sm' }: { name: string; src?: string | null; size?: 'sm' | 'lg' }) {
  const cls = size === 'sm' ? 'size-8 text-xs' : 'size-16 text-xl'
  if (src) return <img src={src} alt="" className={clsx(cls, 'rounded-full border-2 border-ink object-cover')} referrerPolicy="no-referrer" />
  return (
    <span className={clsx(cls, 'inline-flex items-center justify-center rounded-full bg-primary font-bold text-white')} aria-hidden>
      {initials(name)}
    </span>
  )
}
