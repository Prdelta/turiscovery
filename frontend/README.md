# Turiscovery · Frontend React

SPA de Turiscovery. Consume la API Laravel del directorio superior.

```bash
npm install
npm run dev      # http://localhost:5173 (reenvía /api, /sanctum y /storage a http://127.0.0.1:8000)
npm run build    # genera dist/
```

- `src/pages/`: páginas públicas, cuenta del viajero (`user/`), panel del socio (`dashboard/`) y administración (`admin/`).
- `src/three/`: escena 3D del lago Titicaca y visor de modelos `.glb`.
- `public/models/manifest.json`: lista de modelos del visor. `node scripts/generate-models.mjs` regenera los de ejemplo.
- `VITE_API_URL`: URL de la API cuando no está en el mismo origen (producción). Ver `.env.example`.
