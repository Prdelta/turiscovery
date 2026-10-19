import { clsx } from 'clsx'
import { useId, type InputHTMLAttributes, type ReactNode, type SelectHTMLAttributes, type TextareaHTMLAttributes } from 'react'

interface FieldWrapProps {
  label: string
  error?: string
  hint?: string
  required?: boolean
  children: (id: string, describedBy?: string) => ReactNode
  className?: string
}

export function FieldWrap({ label, error, hint, required, children, className }: FieldWrapProps) {
  const id = useId()
  const describedBy = error ? `${id}-error` : hint ? `${id}-hint` : undefined
  return (
    <div className={clsx('flex flex-col gap-1.5', className)}>
      <label htmlFor={id} className="text-sm font-semibold text-ink">
        {label}
        {required && <span className="ml-0.5 text-danger" aria-hidden>*</span>}
      </label>
      {children(id, describedBy)}
      {error ? (
        <p id={`${id}-error`} className="text-sm text-danger">{error}</p>
      ) : hint ? (
        <p id={`${id}-hint`} className="text-sm text-ink-soft">{hint}</p>
      ) : null}
    </div>
  )
}

type Common = { label: string; error?: string; hint?: string; wrapClassName?: string }

export function TextField({ label, error, hint, wrapClassName, className, ...rest }: Common & InputHTMLAttributes<HTMLInputElement>) {
  return (
    <FieldWrap label={label} error={error} hint={hint} required={rest.required} className={wrapClassName}>
      {(id, describedBy) => (
        <input
          id={id}
          aria-invalid={!!error}
          aria-describedby={describedBy}
          className={clsx('field-input', error && 'border-danger', className)}
          {...rest}
        />
      )}
    </FieldWrap>
  )
}

export function TextArea({ label, error, hint, wrapClassName, className, ...rest }: Common & TextareaHTMLAttributes<HTMLTextAreaElement>) {
  return (
    <FieldWrap label={label} error={error} hint={hint} required={rest.required} className={wrapClassName}>
      {(id, describedBy) => (
        <textarea
          id={id}
          aria-invalid={!!error}
          aria-describedby={describedBy}
          className={clsx('field-input min-h-28', error && 'border-danger', className)}
          {...rest}
        />
      )}
    </FieldWrap>
  )
}

export function SelectField({
  label,
  error,
  hint,
  wrapClassName,
  className,
  options,
  placeholder,
  ...rest
}: Common & SelectHTMLAttributes<HTMLSelectElement> & { options: Record<string, string>; placeholder?: string }) {
  return (
    <FieldWrap label={label} error={error} hint={hint} required={rest.required} className={wrapClassName}>
      {(id, describedBy) => (
        <select
          id={id}
          aria-invalid={!!error}
          aria-describedby={describedBy}
          className={clsx('field-input', error && 'border-danger', className)}
          {...rest}
        >
          {placeholder !== undefined && <option value="">{placeholder}</option>}
          {Object.entries(options).map(([value, text]) => (
            <option key={value} value={value}>{text}</option>
          ))}
        </select>
      )}
    </FieldWrap>
  )
}

export function Checkbox({ label, className, ...rest }: { label: string } & InputHTMLAttributes<HTMLInputElement>) {
  return (
    <label className={clsx('inline-flex cursor-pointer items-center gap-2.5 text-sm font-medium', className)}>
      <input type="checkbox" className="size-4.5 rounded accent-primary" {...rest} />
      {label}
    </label>
  )
}
