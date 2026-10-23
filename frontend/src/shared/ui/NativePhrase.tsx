import { clsx } from 'clsx'

/** Códigos ISO 639 de las lenguas originarias de Puno */
export const NATIVE_LANG = {
  quechua: { code: 'qu', label: 'quechua' },
  aimara: { code: 'ay', label: 'aimara' },
} as const

export type NativeLanguage = keyof typeof NATIVE_LANG

/**
 * Texto en lengua originaria según la guía: el original arriba en Bricolage,
 * la traducción justo debajo en Figtree 16px (nunca en un tooltip) y siempre
 * con el atributo lang para lectores de pantalla y traductores.
 */
export function NativePhrase({
  original,
  translation,
  language,
  size = 'md',
  dark = false,
  className,
}: {
  original: string
  translation?: string | null
  language?: NativeLanguage | null
  size?: 'sm' | 'md' | 'lg'
  dark?: boolean
  className?: string
}) {
  const lang = language ? NATIVE_LANG[language] : null
  return (
    <div className={className}>
      <p
        lang={lang?.code}
        className={clsx(
          'font-display font-extrabold leading-[1.08] tracking-tight',
          size === 'sm' && 'text-lg',
          size === 'md' && 'text-2xl',
          size === 'lg' && 'text-[2rem]',
          dark ? 'text-white' : 'text-ink',
        )}
      >
        {original}
      </p>
      {(translation || lang) && (
        <p lang="es" className={clsx('mt-1 text-base', dark ? 'text-white/80' : 'text-ink-soft')}>
          {translation}
          {translation && lang ? ' · ' : ''}
          {lang?.label}
        </p>
      )}
    </div>
  )
}
