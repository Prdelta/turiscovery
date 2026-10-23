import type { ReactNode } from 'react'
import { Link } from 'react-router'
import { ButtonLink } from '@/shared/ui/Button'
import { useDocumentTitle } from '@/shared/lib/hooks'

function TextPage({ title, children }: { title: string; children: ReactNode }) {
  useDocumentTitle(title)
  return (
    <div className="container-page py-14 sm:py-20">
      <h1 className="t-h1">{title}</h1>
      <div className="prose-measure mt-8 flex flex-col gap-5 text-lg leading-relaxed text-ink-soft [&_h2]:mt-6 [&_h2]:text-2xl [&_h2]:font-extrabold [&_h2]:text-ink">
        {children}
      </div>
    </div>
  )
}

export function AboutPage() {
  return (
    <TextPage title="Sobre Ñan Puno">
      <p>
        Ñan Puno reúne en un solo lugar lo que ofrecen los negocios de Puno: experiencias en el lago, eventos, promociones y
        locales, además de la historia y las danzas de la Festividad de la Virgen de la Candelaria.
      </p>
      <p>
        El contenido lo publican socios locales —agencias, restaurantes, hoteles, artesanos y centros culturales— y lo revisa
        nuestro equipo de administración.
      </p>
      <h2>Para viajeros</h2>
      <p>Crea una cuenta gratis para guardar favoritos, reservar experiencias, confirmar asistencia a eventos y obtener cupones.</p>
      <h2>Para negocios</h2>
      <p>
        Si tienes un negocio turístico en Puno, <Link to="/contacto" className="font-bold text-primary-dark underline decoration-2 underline-offset-4">escríbenos</Link> y te
        creamos una cuenta de socio.
      </p>
    </TextPage>
  )
}

export function ContactPage() {
  return (
    <TextPage title="Contacto">
      <p>¿Quieres publicar tu negocio, reportar un error o hacer una consulta? Escríbenos y te respondemos en un plazo de dos días hábiles.</p>
      <p>
        Correo:{' '}
        <a href="mailto:contacto@nanpuno.pe" className="font-bold text-primary-dark underline decoration-2 underline-offset-4">contacto@nanpuno.pe</a>
      </p>
      <p>Puno, Perú.</p>
      <div>
        <ButtonLink to="/sobre-nosotros" variant="secondary">Conocer más sobre Ñan Puno</ButtonLink>
      </div>
    </TextPage>
  )
}

export function TermsPage() {
  return (
    <TextPage title="Términos y condiciones">
      <p>Al usar Ñan Puno aceptas estas condiciones. Si no estás de acuerdo, no uses la plataforma.</p>
      <h2>Contenido de los socios</h2>
      <p>
        Las experiencias, eventos y promociones son publicados por negocios independientes. Cada socio es responsable de la
        exactitud de su información, de sus precios y de cumplir lo ofrecido.
      </p>
      <h2>Reservas y cupones</h2>
      <p>
        Las reservas hechas en Ñan Puno quedan pendientes hasta que el socio las confirma. Los cupones solo son válidos
        durante la vigencia de la promoción y según sus términos.
      </p>
      <h2>Reseñas</h2>
      <p>Las reseñas deben basarse en experiencias reales. Podemos retirar contenido ofensivo, falso o publicitario.</p>
      <h2>Cuentas</h2>
      <p>Eres responsable de la seguridad de tu cuenta. Podemos suspender cuentas que incumplan estas condiciones.</p>
    </TextPage>
  )
}

export function PrivacyPage() {
  return (
    <TextPage title="Política de privacidad">
      <p>Tratamos tus datos personales conforme a la Ley N.º 29733, Ley de Protección de Datos Personales del Perú.</p>
      <h2>Qué datos guardamos</h2>
      <p>Nombre, correo, teléfono opcional, y tu actividad en la plataforma: favoritos, reseñas, reservas, asistencias y cupones.</p>
      <h2>Para qué los usamos</h2>
      <p>
        Para que puedas usar tu cuenta y para que el socio de una reserva pueda contactarte. No vendemos tus datos ni los
        compartimos con terceros con fines publicitarios.
      </p>
      <h2>Tus derechos</h2>
      <p>
        Puedes pedir acceso, corrección o eliminación de tus datos escribiendo a{' '}
        <a href="mailto:contacto@nanpuno.pe" className="font-bold text-primary-dark underline decoration-2 underline-offset-4">contacto@nanpuno.pe</a>.
      </p>
    </TextPage>
  )
}
