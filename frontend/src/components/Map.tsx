import L from 'leaflet'
import { useEffect } from 'react'
import { MapContainer, Marker, Popup, TileLayer, useMap, useMapEvents } from 'react-leaflet'
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

function FitBounds({ points }: { points: MapPoint[] }) {
  const map = useMap()
  useEffect(() => {
    if (points.length > 1) {
      map.fitBounds(L.latLngBounds(points.map((p) => [p.lat, p.lng])), { padding: [40, 40], maxZoom: 15 })
    } else if (points.length === 1) {
      map.setView([points[0].lat, points[0].lng], 15)
    }
  }, [map, points])
  return null
}

export function MapView({ points, height = 380, className }: { points: MapPoint[]; height?: number | string; className?: string }) {
  return (
    <div className={className} style={{ height }}>
      <MapContainer center={points[0] ? [points[0].lat, points[0].lng] : PUNO_CENTER} zoom={14} scrollWheelZoom={false} style={{ height: '100%' }}>
        <TileLayer url={TILE_URL} attribution={TILE_ATTRIBUTION} />
        <FitBounds points={points} />
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
