import { Download, Heart, Share2 } from 'lucide-react'
import { useState, type ReactNode } from 'react'
import { CategoryNavBand, TypeTag } from '@/shared/textile/Tags'
import { DYES, MotifStrip, ProgressBand, SiteBand, StitchRow, Tocapu, type Motif } from '@/shared/textile/Weave'
import { Button } from '@/shared/ui/Button'
import { Alert, CardGridSkeleton, DifficultyBadge, FilterChip, StarInput, Stars, StatePanel, StatusBadge, type StatusKey } from '@/shared/ui/Feedback'
import { Checkbox, SelectField, TextField } from '@/shared/ui/Field'
import { NativePhrase } from '@/shared/ui/NativePhrase'
import { useToast } from '@/shared/ui/Toast'
import { useDocumentTitle } from '@/shared/lib/hooks'

/*
  Guía de estilo viva: muestra los tokens y componentes base tal como los usa la app,
  para compararlos con la guía "Telar" de Claude Design. No está enlazada en el menú.
*/

const COLORS: { name: string; token: string; hex: string; use: string; dark?: boolean }[] = [
  { name: 'Tinta', token: '--color-ink', hex: '#1b1a3a', use: 'Texto, bordes de 2px, faja de navegación', dark: true },
  { name: 'Tinta suave', token: '--color-ink-soft', hex: '#4a4968', use: 'Texto secundario. Nunca más claro que esto', dark: true },
  { name: 'Lana', token: '--color-lana', hex: '#f6efe3', use: 'Fondo principal' },
  { name: 'Cielo', token: '--color-sky', hex: '#eef2f8', use: 'Fondo de paneles y pantallas largas' },
  { name: 'Línea', token: '--color-line', hex: '#d8deea', use: 'Divisores, puntadas pendientes' },
  { name: 'Primario', token: '--color-primary', hex: '#5930e8', use: 'Acción principal, foco', dark: true },
  { name: 'Primario oscuro', token: '--color-primary-dark', hex: '#4426b8', use: 'Hover, enlaces', dark: true },
  { name: 'Lago', token: '--color-lake', hex: '#10295c', use: 'Lugares, hero 3D', dark: true },
  { name: 'Menta', token: '--color-mint', hex: '#74fa8a', use: 'Éxito, "EN VIVO" sobre oscuro' },
  { name: 'Sol', token: '--color-sun', hex: '#f2b914', use: 'Selección en mapa, aviso' },
  { name: 'Peligro', token: '--color-danger', hex: '#d23b3b', use: 'Errores, cancelar', dark: true },
  { name: 'Cochinilla', token: '--color-cochinilla', hex: '#b3243b', use: 'Eventos, en vivo', dark: true },
  { name: 'Añil', token: '--color-anil', hex: '#2b3a8c', use: 'Experiencias', dark: true },
  { name: 'Chilca', token: '--color-chilca', hex: '#4c7a3a', use: 'Promociones', dark: true },
  { name: 'Tierra', token: '--color-tierra', hex: '#8a5a2b', use: 'Locales', dark: true },
  { name: "Q'olle", token: '--color-qolle', hex: '#d99a1e', use: 'Anfitriones (solo relleno)' },
]

const MOTIFS: { motif: Motif; name: string; use: string; dye: keyof typeof DYES }[] = [
  { motif: 'olas', name: 'Olas', use: 'Experiencias', dye: 'anil' },
  { motif: 'chakana', name: 'Chakana', use: 'Eventos', dye: 'cochinilla' },
  { motif: 'ojo', name: 'Ojo (rombo)', use: 'Promociones', dye: 'chilca' },
  { motif: 'llave', name: 'Llave escalonada', use: 'Locales', dye: 'tierra' },
  { motif: 'estrella', name: 'Estrella de 8 puntas', use: 'Candelaria', dye: 'morado' },
  { motif: 'sol', name: 'Inti', use: 'Almanaque', dye: 'solOscuro' },
  { motif: 'persona', name: 'Persona', use: 'Anfitriones', dye: 'qolle' },
  { motif: 'isla', name: 'Isla flotante', use: 'Lugares del lago', dye: 'lago' },
]

const TYPE_SCALE: { cls: string; spec: string; sample: string }[] = [
  { cls: 't-display', spec: 'display 56/1.02', sample: 'Isla Taquile' },
  { cls: 't-h1', spec: 'h1 40/1.08', sample: 'Candelaria en vivo' },
  { cls: 't-h2', spec: 'h2 28/1.08', sample: 'Tu plan de aclimatación' },
  { cls: 't-h3', spec: 'h3 20/1.08', sample: 'Tejido con la familia Huatta' },
  { cls: 't-body', spec: 'body 16/1.6', sample: 'Aprende el telar de pedal con quienes tejen las fajas de la isla. Incluye almuerzo en casa de la familia.' },
  { cls: 't-small text-ink-soft', spec: 'small 14/1.5', sample: '2 h 30 en lancha desde Puno · 3 950 m' },
  { cls: 't-label', spec: 'label 12/800', sample: 'En vivo · Confirmada · Fácil' },
]

function Section({ title, motif, children }: { title: string; motif: Motif; children: ReactNode }) {
  return (
    <section className="mt-18">
      <h2 className="t-h1 mb-6 flex items-center gap-3">
        <Tocapu motif={motif} dye="lago" size={32} framed={false} />
        {title}
      </h2>
      {children}
    </section>
  )
}

export default function StyleGuidePage() {
  useDocumentTitle('Guía de estilo')
  const notify = useToast()
  const [chips, setChips] = useState({ taquile: true, facil: false })
  const [rating, setRating] = useState(4)
  const statuses: StatusKey[] = ['pending', 'confirmed', 'cancelled', 'completed', 'available', 'used', 'expired']

  return (
    <div className="container-page py-12">
      <header>
        <p className="flex items-center gap-2 font-bold text-ink-soft">
          <Tocapu motif="estrella" dye="cochinilla" size={28} framed={false} /> Ñan Puno · guía de estilo v1 · dirección Telar
        </p>
        <h1 className="t-display mt-4 max-w-3xl">La faja tejida organiza todo lo que se ve.</h1>
        <p className="prose-measure mt-4 text-lg text-ink-soft">
          Fondo lana, tinta de 2px, tintes a plena saturación y la faja como navegación. Esta página muestra los componentes reales de la
          app para compararlos con el diseño.
        </p>
        <MotifStrip className="mt-6" motifs={['olas', 'chakana', 'ojo', 'llave', 'estrella', 'sol', 'persona', 'isla', 'olas', 'chakana', 'ojo', 'llave', 'estrella', 'sol']} />
      </header>

      <Section title="Color" motif="ojo">
        <p className="prose-measure mb-6 text-ink-soft">
          Solo colores sólidos. El texto de párrafo va en tinta o tinta suave; los tintes se usan como relleno o en texto de 14px+ en negrita
          cuando pasan 4.5:1.
        </p>
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {COLORS.map((c) => (
            <div key={c.token} className="card overflow-hidden">
              <div className="h-20" style={{ background: c.hex }} />
              <div className="border-t-2 border-ink p-4">
                <p className="font-bold">{c.name}</p>
                <p className="font-mono text-xs text-ink-soft">{c.token} · {c.hex}</p>
                <p className="t-small mt-1 text-ink-soft">{c.use}</p>
              </div>
            </div>
          ))}
        </div>
      </Section>

      <Section title="Tipografía" motif="llave">
        <div className="grid gap-10 lg:grid-cols-[1fr_320px]">
          <div className="flex flex-col gap-5">
            {TYPE_SCALE.map((t) => (
              <div key={t.spec} className="grid grid-cols-[120px_1fr] items-baseline gap-4">
                <span className="font-mono text-xs font-bold text-ink-soft">{t.spec}</span>
                <p className={t.cls}>{t.sample}</p>
              </div>
            ))}
          </div>
          <div className="panel flex flex-col gap-5 p-6">
            <p className="t-label text-ink-soft">Texto en lengua originaria</p>
            <NativePhrase original="Allillanchu?" translation="¿Cómo estás?" language="quechua" />
            <NativePhrase original="Kamisaraki?" translation="¿Cómo estás?" language="aimara" />
            <p className="t-small text-ink-soft">
              Regla: el original arriba en Bricolage, la traducción justo debajo en Figtree 16px, nunca en tooltip. Siempre con atributo lang.
            </p>
          </div>
        </div>
      </Section>

      <Section title="Espaciado, radios y áreas táctiles" motif="isla">
        <div className="grid gap-10 lg:grid-cols-2">
          <ul className="flex flex-col gap-3">
            {[
              [1, '4px · ícono-texto'],
              [2, '8px · dentro de chips'],
              [3, '12px · entre filas'],
              [4, '16px · margen móvil'],
              [6, '24px · tarjeta interna'],
              [8, '32px · margen escritorio'],
              [12, '48px · entre secciones'],
              [18, '72px · secciones de página'],
            ].map(([n, label]) => (
              <li key={n} className="flex items-center gap-4">
                <span className="w-16 font-mono text-xs font-bold">--sp-{n}</span>
                <span className="h-3 rounded-sm bg-primary" style={{ width: Number(n) * 4 }} />
                <span className="t-small text-ink-soft">{label}</span>
              </li>
            ))}
          </ul>
          <div className="flex flex-wrap items-end gap-5">
            {[
              ['chip', 8, '8 · chips'],
              ['control', 12, '12 · inputs, botones'],
              ['card', 18, '18 · tarjetas'],
              ['panel', 24, '24 · paneles'],
            ].map(([k, r, label]) => (
              <div key={k as string} className="flex flex-col items-center gap-2">
                <div className="size-24 border-2 border-ink bg-white" style={{ borderRadius: Number(r) }} />
                <span className="t-small font-bold">{label}</span>
              </div>
            ))}
            <div className="flex flex-col items-center gap-2">
              <Button variant="secondary" iconOnly aria-label="Guardar">
                <Heart className="size-5" />
              </Button>
              <span className="t-small font-bold">44 mín. táctil</span>
            </div>
          </div>
        </div>
      </Section>

      <Section title="Iconografía textil" motif="chakana">
        <p className="prose-measure mb-6 text-ink-soft">
          Ocho motivos en cuadrícula de 8×8 puntadas, como en un telar de Taquile, dibujados en múltiplos de 8px para que cada puntada caiga
          en un píxel entero. Los íconos de interfaz (buscar, cerrar, reproducir) siguen siendo Lucide.
        </p>
        <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
          {MOTIFS.map((m) => (
            <div key={m.motif} className="card flex items-center gap-4 p-4">
              <Tocapu motif={m.motif} dye={m.dye} size={48} framed={false} />
              <div>
                <p className="font-bold">{m.use}</p>
                <p className="t-small text-ink-soft">{m.name}</p>
              </div>
            </div>
          ))}
        </div>

        <h3 className="t-h2 mb-4 mt-12">Fajas: cuatro usos</h3>
        <div className="grid gap-8 lg:grid-cols-2">
          <div>
            <CategoryNavBand className="rounded-[var(--radius-control)]" />
            <p className="t-small mt-2 text-ink-soft"><strong className="text-ink">Navegación</strong> · banda de tinta con los tipos de contenido; el activo lleva fondo lana.</p>
          </div>
          <div>
            <StitchRow dye="cochinilla" />
            <p className="t-small mt-2 text-ink-soft"><strong className="text-ink">Separador de sección</strong> · 8px de puntada, en el tinte de la sección que empieza.</p>
          </div>
          <div>
            <ProgressBand total={12} done={8} motif="olas" dye="morado" label="Paso 8 de 12" />
            <p className="t-small mt-2 text-ink-soft"><strong className="text-ink">Progreso</strong> · audio, programa de la Candelaria y pasos de reserva. Hecho = tinte, pendiente = línea.</p>
          </div>
          <div>
            <div className="flex flex-wrap gap-2">
              <TypeTag type="experiencias" />
              <TypeTag type="eventos" />
              <TypeTag type="promociones" />
              <TypeTag type="locales" />
            </div>
            <p className="t-small mt-2 text-ink-soft"><strong className="text-ink">Identificación</strong> · la pestaña de cada tarjeta lleva el motivo y el tinte del tipo.</p>
          </div>
        </div>
        <SiteBand className="mt-8" />
      </Section>

      <Section title="Componentes base" motif="persona">
        <h3 className="t-h2 mb-4">Botones</h3>
        <div className="grid gap-4">
          {(['primary', 'secondary', 'ghost', 'danger'] as const).map((v) => (
            <div key={v} className="flex flex-wrap items-center gap-3">
              <span className="w-24 t-small font-bold">{{ primary: 'Primario', secondary: 'Secundario', ghost: 'Fantasma', danger: 'Peligro' }[v]}</span>
              <Button variant={v} icon={v === 'secondary' ? <Download className="size-4" /> : undefined}>
                {{ primary: 'Reservar', secondary: 'Guardar para el viaje', ghost: 'Ver todas', danger: 'Cancelar reserva' }[v]}
              </Button>
              <Button variant={v} loading loadingText={{ primary: 'Reservando…', secondary: 'Descargando…', ghost: 'Cargando…', danger: 'Cancelando…' }[v]}>
                x
              </Button>
              <Button variant={v} disabled>
                {{ primary: 'Sin cupos', secondary: 'Guardado', ghost: 'Ver todas', danger: 'Cancelar reserva' }[v]}
              </Button>
            </div>
          ))}
          <div className="flex flex-wrap items-center gap-3">
            <span className="w-24 t-small font-bold">Tamaños</span>
            <Button size="sm">sm · 44</Button>
            <Button size="md">md · 52</Button>
            <Button size="lg">lg · 60</Button>
            <Button variant="secondary" iconOnly aria-label="Compartir">
              <Share2 className="size-5" />
            </Button>
          </div>
          <p className="t-small text-ink-soft">Nunca menos de 44px de alto. En móvil el CTA principal es lg y de ancho completo.</p>
        </div>

        <h3 className="t-h2 mb-4 mt-12">Campos</h3>
        <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <TextField label="Nombre completo" placeholder="Como figura en tu DNI o pasaporte" />
          <TextField label="Teléfono" defaultValue="+51 951 23" error="Faltan 3 dígitos" />
          <TextField label="Código de socio" placeholder="Solo para socios" disabled />
          <SelectField label="Llego desde" options={{ costa: 'Costa (Lima, Arequipa…)', altura: 'Otra ciudad alta' }} />
          <Checkbox label="Recordarme en este dispositivo" />
        </div>

        <div className="mt-12 grid gap-10 lg:grid-cols-2">
          <div>
            <h3 className="t-h2 mb-4">Insignias de estado</h3>
            <div className="flex flex-wrap gap-2">
              {statuses.map((s) => <StatusBadge key={s} status={s} />)}
            </div>
            <div className="mt-3 flex flex-wrap gap-2">
              <DifficultyBadge level="easy" />
              <DifficultyBadge level="medium" />
              <DifficultyBadge level="hard" />
            </div>
            <p className="t-small mt-2 text-ink-soft">La dificultad usa barras, no solo color.</p>

            <h3 className="t-h2 mb-4 mt-10">Chips de filtro y estrellas</h3>
            <div className="flex flex-wrap gap-2">
              <FilterChip selected={chips.taquile} onClick={() => setChips((c) => ({ ...c, taquile: !c.taquile }))}>Taquile</FilterChip>
              <FilterChip selected={chips.facil} onClick={() => setChips((c) => ({ ...c, facil: !c.facil }))}>Fácil</FilterChip>
              <FilterChip>Hasta S/ 150</FilterChip>
              <FilterChip count={2}>Más filtros</FilterChip>
            </div>
            <div className="mt-4 flex flex-wrap items-center gap-6">
              <Stars value={4.6} size="md" />
              <StarInput value={rating} onChange={setRating} />
            </div>
          </div>
          <div>
            <h3 className="t-h2 mb-4">Alertas</h3>
            <div className="flex flex-col gap-3">
              <Alert tone="info">El oleaje sube después de las 14:00. Sal temprano a Taquile.</Alert>
              <Alert tone="success">Reserva enviada. Te avisamos cuando Alejandro la confirme.</Alert>
              <Alert tone="warning">Llegaste ayer desde la costa. Hoy toca descanso: evita subir a Amantaní.</Alert>
              <Alert tone="error">No pudimos enviar la reserva. Revisa tu conexión e inténtalo otra vez.</Alert>
            </div>
            <h3 className="t-h2 mb-4 mt-10">Toasts</h3>
            <div className="flex flex-wrap gap-3">
              <Button variant="secondary" size="sm" onClick={() => notify('Guardado en Mi viaje', 'success', { action: { label: 'Deshacer', onClick: () => notify('Quitado de Mi viaje') } })}>
                Guardar (con deshacer)
              </Button>
              <Button variant="secondary" size="sm" onClick={() => notify('Sin conexión. Se enviará al volver la señal.', 'offline')}>
                Sin conexión
              </Button>
              <Button variant="secondary" size="sm" onClick={() => notify('No se pudo completar la acción.', 'error')}>
                Error
              </Button>
            </div>
          </div>
        </div>

        <h3 className="t-h2 mb-4 mt-12">Skeleton</h3>
        <p className="t-small mb-4 text-ink-soft">Sin brillo animado: el motivo del tipo aparece estático.</p>
        <CardGridSkeleton count={3} motif="olas" dye="anil" />
      </Section>

      <Section title="Estados obligatorios" motif="sol">
        <div className="grid gap-4 md:grid-cols-2">
          <StatePanel
            kind="empty"
            title="Aún no guardas experiencias"
            icon={<Tocapu motif="olas" dye="anil" size={40} framed={false} />}
            action={<Button>Explorar experiencias</Button>}
          >
            Toca el corazón en cualquier experiencia y la verás aquí, incluso sin señal.
          </StatePanel>
          <StatePanel kind="error" title="No pudimos cargar los eventos" action={<Button variant="secondary">Reintentar</Button>}>
            Puede ser la señal. Lo guardado sigue disponible.
          </StatePanel>
          <StatePanel kind="offline" title="Estás sin conexión" action={<Button variant="ink">Ver lo guardado</Button>}>
            Tienes guardados Taquile y Uros. Las reservas se enviarán al volver la señal.
          </StatePanel>
          <StatePanel kind="forbidden" title="Esta sección es para socios" action={<Button variant="secondary">Ser socio</Button>}>
            Si tienes un negocio en Puno, solicita tu cuenta de socio.
          </StatePanel>
          <StatePanel kind="success" title="¡Reserva enviada!" action={<Button variant="mint">Ver mis reservas</Button>}>
            Alejandro te confirmará en menos de 24 h.
          </StatePanel>
        </div>
      </Section>
    </div>
  )
}
