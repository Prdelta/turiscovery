import type { ReactNode } from 'react'

/** Encabezado de página de los paneles: título, descripción y acción principal */
export function PageHeader({ title, description, action }: { title: string; description?: string; action?: ReactNode }) {
  return (
    <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
      <div>
        <h1 className="t-h1">{title}</h1>
        {description && <p className="prose-measure mt-2 text-ink-soft">{description}</p>}
      </div>
      {action}
    </div>
  )
}
