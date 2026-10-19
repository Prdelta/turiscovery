import L from 'leaflet'
import { useEffect } from 'react'
import { MapContainer, Marker, Polyline, Popup, TileLayer, useMap, useMapEvents } from 'react-leaflet'
import { Link } from 'react-router'

/** Plaza de Armas de Puno */
export const PUNO_CENTER: [number, number] = [-15.8402, -70.0219]

const TILE_URL = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
const TILE_ATTRIBUTION = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'

/** Marcador propio (evita el problema de rutas de íconos de Leaflet con Vite) */
export function pinIcon(color = '#5930e8') {
  return L.divIcon({
    className: '',
    iconSize: [28, 38],
    iconAnchor: [14, 38],
    popupAnchor: [0, -34],
    html: `<svg width="28" height="38" viewBox="0 0 28 38" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <path d="M14 0C6.3 0 0 6.2 0 13.9 0 24.3 14 38 14 38s14-13.7 14-24.1C28 6.2 21.7 0 14 0z" fill="${color}"/>
      <circle cx="14" cy="14" r="5.5" fill="#fff"/></svg>`,
  })
}

export interface MapPoint {
  id: string | number
  lat: number
  lng: number
  title: string
  subtitle?: string
  href?: string
  color?: string
}

function FitBounds({ points, route }: { points: MapPoint[]; route?: [number, number][] }) {
  const map = useMap()
  useEffect(() => {
    const all: [number, number][] = [...points.map((p) => [p.lat, p.lng] as [number, number]), ...(route ?? [])]
    if (all.length > 1) {
      map.fitBounds(L.latLngBounds(all), { padding: [40, 40], maxZoom: 16 })
    } else if (all.length === 1) {
      map.setView(all[0], 15)
    }
  }, [map, points, route])
  return null
}

export function MapView({
  points,
  route,
  height = 380,
  className,
}: {
  points: MapPoint[]
  /** Recorrido opcional (por ejemplo, el del desfile de la Candelaria) */
  route?: [number, number][] | null
  height?: number | string
  className?: string
}) {
  return (
    <div className={className} style={{ height }}>
      <MapContainer center={points[0] ? [points[0].lat, points[0].lng] : PUNO_CENTER} zoom={14} scrollWheelZoom={false} style={{ height: '100%' }}>
        <TileLayer url={TILE_URL} attribution={TILE_ATTRIBUTION} />
        <FitBounds points={points} route={route ?? undefined} />
        {route && route.length > 1 && <Polyline positions={route} pathOptions={{ color: '#5930e8', weight: 6, opacity: 0.85 }} />}
        {points.map((p) => (
          <Marker key={p.id} position={[p.lat, p.lng]} icon={pinIcon(p.color)}>
            <Popup>
              <div className="font-sans">
                <p className="font-bold">{p.title}</p>
                {p.subtitle && <p className="text-sm">{p.subtitle}</p>}
                {p.href && (
                  <Link to={p.href} className="text-sm font-semibold">
                    Ver detalle
                  </Link>
                )}
              </div>
            </Popup>
          </Marker>
        ))}
      </MapContainer>
    </div>
  )
}

function ClickToPlace({ onPick }: { onPick: (lat: number, lng: number) => void }) {
  useMapEvents({
    click(e) {
      onPick(Number(e.latlng.lat.toFixed(6)), Number(e.latlng.lng.toFixed(6)))
    },
  })
  return null
}

/** Selector de ubicación: clic en el mapa o arrastrar el marcador */
export function LocationPicker({
  lat,
  lng,
  onChange,
}: {
  lat?: number | null
  lng?: number | null
  onChange: (lat: number, lng: number) => void
}) {
  const position: [number, number] | null = lat && lng ? [lat, lng] : null
  return (
    <div className="h-72 overflow-hidden rounded-[var(--radius-card)] border border-line">
      <MapContainer center={position ?? PUNO_CENTER} zoom={14} style={{ height: '100%' }}>
        <TileLayer url={TILE_URL} attribution={TILE_ATTRIBUTION} />
        <ClickToPlace onPick={onChange} />
        {position && (
          <Marker
            position={position}
            icon={pinIcon()}
            draggable
            eventHandlers={{
              dragend: (e) => {
                const p = (e.target as L.Marker).getLatLng()
                onChange(Number(p.lat.toFixed(6)), Number(p.lng.toFixed(6)))
              },
            }}
          />
        )}
      </MapContainer>
    </div>
  )
}

function ClickToAdd({ onAdd }: { onAdd: (p: [number, number]) => void }) {
  useMapEvents({
    click(e) {
      onAdd([Number(e.latlng.lat.toFixed(6)), Number(e.latlng.lng.toFixed(6))])
    },
  })
  return null
}

/** Editor de recorridos: cada clic agrega un punto al trazado */
export function RouteEditor({ value, onChange }: { value: [number, number][]; onChange: (route: [number, number][]) => void }) {
  return (
    <div className="flex flex-col gap-2">
      <div className="h-80 overflow-hidden rounded-[var(--radius-card)] border border-line">
        <MapContainer center={value[0] ?? PUNO_CENTER} zoom={15} style={{ height: '100%' }}>
          <TileLayer url={TILE_URL} attribution={TILE_ATTRIBUTION} />
          <ClickToAdd onAdd={(p) => onChange([...value, p])} />
          {value.length > 1 && <Polyline positions={value} pathOptions={{ color: '#5930e8', weight: 5 }} />}
          {value.length > 0 && <Marker position={value[0]} icon={pinIcon('#2f9e45')} />}
          {value.length > 1 && <Marker position={value[value.length - 1]} icon={pinIcon('#d23b3b')} />}
        </MapContainer>
      </div>
      <div className="flex flex-wrap items-center gap-3 text-sm">
        <span className="text-ink-soft">{value.length} puntos. Haz clic en el mapa siguiendo las calles del recorrido.</span>
        <button type="button" disabled={!value.length} onClick={() => onChange(value.slice(0, -1))} className="font-semibold text-primary disabled:opacity-40">
          Deshacer último punto
        </button>
        <button type="button" disabled={!value.length} onClick={() => onChange([])} className="font-semibold text-danger disabled:opacity-40">
          Borrar recorrido
        </button>
      </div>
    </div>
  )
}
