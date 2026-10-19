import { clsx } from 'clsx'
import { LoaderCircle } from 'lucide-react'
import type { ButtonHTMLAttributes, ReactNode } from 'react'
import { Link, type LinkProps } from 'react-router'

/*
  Botones de la guía Telar.
  - primario: acción principal (una por pantalla)
  - secundario: fondo blanco con borde de tinta de 2px
  - fantasma: enlace subrayado, para acciones menores ("Ver todas")
  - peligro: cancelar o eliminar
  - ink: sobre fondos de color (sol, tintes)
  - mint / onDark: variantes para superficies oscuras (lago, tinta)
  Nunca miden menos de 44px de alto. En móvil el CTA principal es lg y de ancho completo.
*/

export type ButtonVariant = 'primary' | 'secondary' | 'ghost' | 'danger' | 'ink' | 'mint' | 'onDark'
export type ButtonSize = 'sm' | 'md' | 'lg'

const variants: Record<ButtonVariant, string> = {
  primary: 'border-2 border-primary bg-primary text-white hover:border-primary-dark hover:bg-primary-dark',
  secondary: 'border-2 border-ink bg-white text-ink hover:bg-sky',
  ghost: 'border-2 border-transparent text-primary-dark underline decoration-2 underline-offset-4 hover:bg-sky',
  danger: 'border-2 border-cochinilla bg-cochinilla text-white hover:border-[#8f1c2f] hover:bg-[#8f1c2f]',
  ink: 'border-2 border-ink bg-ink text-white hover:bg-[#2c2a55]',
  mint: 'border-2 border-mint bg-mint text-ink hover:border-white hover:bg-white',
  onDark: 'border-2 border-white/70 bg-transparent text-white hover:bg-white/10',
}

/** Deshabilitado: gris neutro en las variantes rellenas; el fantasma queda como texto gris */
const disabledClass: Record<ButtonVariant, string> = {
  primary: 'disabled:border-line disabled:bg-sky disabled:text-ink-soft',
  secondary: 'disabled:border-line disabled:bg-sky disabled:text-ink-soft',
  ghost: 'disabled:text-ink-soft disabled:no-underline disabled:hover:bg-transparent',
  danger: 'disabled:border-line disabled:bg-sky disabled:text-ink-soft',
  ink: 'disabled:border-line disabled:bg-sky disabled:text-ink-soft',
  mint: 'disabled:border-line disabled:bg-sky disabled:text-ink-soft',
  onDark: 'disabled:border-white/30 disabled:text-white/50',
}

const sizes: Record<ButtonSize, string> = {
  sm: 'min-h-11 px-4 text-sm gap-1.5',
  md: 'min-h-13 px-5 text-base gap-2',
  lg: 'min-h-15 px-7 text-lg gap-2.5',
}

const iconSizes: Record<ButtonSize, string> = {
  sm: 'size-11',
  md: 'size-13',
  lg: 'size-15',
}

export function buttonClass(variant: ButtonVariant = 'primary', size: ButtonSize = 'md', extra?: string, iconOnly = false) {
  return clsx(
    'inline-flex shrink-0 items-center justify-center rounded-[var(--radius-control)] font-bold leading-tight transition-colors',
    variants[variant],
    iconOnly ? iconSizes[size] : sizes[size],
    'disabled:cursor-not-allowed',
    disabledClass[variant],
    extra,
  )
}

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: ButtonVariant
  size?: ButtonSize
  loading?: boolean
  /** Texto mientras carga, por ejemplo "Reservando…" (por defecto se mantiene el texto) */
  loadingText?: ReactNode
  icon?: ReactNode
  /** Botón cuadrado solo con ícono: exige aria-label */
  iconOnly?: boolean
  fullWidth?: boolean
}

export function Button({
  variant,
  size,
  loading,
  loadingText,
  icon,
  iconOnly,
  fullWidth,
  className,
  children,
  disabled,
  ...rest
}: ButtonProps) {
  return (
    <button
      // Mientras carga conserva su color (no se ve deshabilitado), pero no acepta otro clic
      className={buttonClass(variant, size, clsx(fullWidth && 'w-full', loading && 'pointer-events-none', className), iconOnly)}
      disabled={disabled}
      aria-disabled={loading || undefined}
      aria-busy={loading || undefined}
      {...rest}
    >
      {loading ? <LoaderCircle className="size-4 animate-spin" aria-hidden /> : icon}
      {iconOnly ? (loading ? null : children) : loading && loadingText ? loadingText : children}
    </button>
  )
}

interface ButtonLinkProps extends LinkProps {
  variant?: ButtonVariant
  size?: ButtonSize
  icon?: ReactNode
  fullWidth?: boolean
}

export function ButtonLink({ variant, size, icon, fullWidth, className, children, ...rest }: ButtonLinkProps) {
  return (
    <Link className={buttonClass(variant, size, clsx(fullWidth && 'w-full', typeof className === 'string' ? className : undefined))} {...rest}>
      {icon}
      {children}
    </Link>
  )
}
