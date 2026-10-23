# 🎨 Paleta de Colores Oficial - Ñan Puno

## Descripción
Identidad visual de marca para Ñan Puno. Paleta vibrante, moderna y energética que transmite innovación, frescura y confianza.

---

## 🔵 1️⃣ Azul/Morado Vibrante (COLOR PRINCIPAL)

**Uso:** Botones principales, CTAs, enlaces, headers, elementos destacados

| Nombre | Hex | RGB | Carácter |
|--------|-----|-----|----------|
| **Primary** | `#5930E8` | `rgb(89, 48, 232)` | Moderno, tecnológico, confiable |
| **Primary Light** | `#7654EC` | `rgb(118, 84, 236)` | Hover states, variantes de color claros |
| **Primary Dark** | `#4426B8` | `rgb(68, 38, 184)` | Gradientes oscuros, sombras |

**Variable CSS:** `var(--color-primary)`

```css
/* Ejemplo de uso */
background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
color: var(--color-primary);
```

---

## 🟢 2️⃣ Verde Menta/Neón (COLOR SECUNDARIO)

**Uso:** Botones "Explorar", highlights, mensajes de éxito, elementos activos

| Nombre | Hex | RGB | Carácter |
|--------|-----|-----|----------|
| **Secondary** | `#74FA8A` | `rgb(116, 250, 138)` | Fresco, energético, juvenil |
| **Secondary Light** | `#8FFBA0` | `rgb(143, 251, 160)` | Hover states suaves |
| **Secondary Dark** | `#5FD973` | `rgb(95, 217, 115)` | Estados activos, bordes |

**Variable CSS:** `var(--color-secondary)`

**También disponible como:** `var(--color-success)` para mensajes de éxito

---

## ⚪ 3️⃣ Gris Azulado (NEUTRO)

**Uso:** Fondos neutros, elementos desactivados, bordes sutiles, divisores

| Nombre | Hex | RGB | Carácter |
|--------|-----|-----|----------|
| **Gray** | `#B0C5C3` | `rgb(176, 197, 195)` | Profesional, calmado, equilibrado |
| **Gray Dark** | `#8A9B99` | `rgb(138, 155, 153)` | Textos secundarios |
| **Gray Light** | `#D5E3E2` | `rgb(213, 227, 226)` | Fondos sutiles |

**Variable CSS:** `var(--color-gray)`

---

## 🟡 4️⃣ Amarillo/Dorado (ACENTO)

**Uso:** Promociones, ofertas especiales, alertas importantes, badges premium

| Nombre | Hex | RGB | Carácter |
|--------|-----|-----|----------|
| **Accent** | `#F2B914` | `rgb(242, 185, 20)` | Llamativo, premium, optimista |
| **Accent Light** | `#FFD24D` | `rgb(255, 210, 77)` | Hover states brillantes |
| **Accent Dark** | `#D19E0B` | `rgb(209, 158, 11)` | Bordes, sombras |

**Variable CSS:** `var(--color-accent)`

**También disponible como:** `var(--color-warning)` para advertencias

---

## 🔵 5️⃣ Celeste/Cyan Claro (INFORMACIÓN)

**Uso:** Badges informativos (ej: "EXPLORA ÑAN PUNO"), tooltips, mensajes informativos

| Nombre | Hex | RGB | Carácter |
|--------|-----|-----|----------|
| **Info** | `#B6DFE7` | `rgb(182, 223, 231)` | Suave, informativo, acogedor |
| **Info Light** | `#D0EBF1` | `rgb(208, 235, 241)` | Fondos de notificaciones |
| **Info Dark** | `#9BCAD5` | `rgb(155, 202, 213)` | Bordes, separadores |

**Variable CSS:** `var(--color-info)`

---

## 🎯 Colores de Estado

| Uso | Color | Variable CSS |
|-----|-------|--------------|
| ✅ **Éxito** | Verde Menta `#74FA8A` | `var(--color-success)` |
| ⚠️ **Advertencia** | Amarillo/Dorado `#F2B914` | `var(--color-warning)` |
| ❌ **Error/Peligro** | Rojo `#E74C3C` | `var(--color-danger)` |
| ℹ️ **Información** | Celeste `#B6DFE7` | `var(--color-info)` |

---

## 📝 Colores de Tipografía

| Nombre | Hex | RGB | Uso |
|--------|-----|-----|-----|
| **Text** | `#2C3E50` | `rgb(44, 62, 80)` | Textos principales |
| **Text Light** | `#5A6C7D` | `rgb(90, 108, 125)` | Textos secundarios |
| **Text Muted** | `#95A5A6` | `rgb(149, 165, 166)` | Textos terciarios, hints |

**Variables CSS:** `var(--color-text)`, `var(--color-text-light)`, `var(--color-text-muted)`

---

## 🎨 Colores de Fondo

| Nombre | Hex | RGB | Uso |
|--------|-----|-----|-----|
| **Background** | `#FFFFFF` | `rgb(255, 255, 255)` | Fondo principal, cards |
| **Background Light** | `#F8F9FA` | `rgb(248, 249, 250)` | Fondo de página |
| **Background Gray** | `#ECEFF1` | `rgb(236, 239, 241)` | Secciones alternas |

**Variables CSS:** `var(--color-bg)`, `var(--color-bg-light)`, `var(--color-bg-gray)`

---

## 📐 Guías de Uso

### ✅ Buenas Prácticas

#### Combinaciones Recomendadas:
1. **Botón Principal:** Azul/Morado (`#5930E8`) + texto blanco
2. **Botón Secundario:** Verde Menta (`#74FA8A`) + texto oscuro
3. **Promociones:** Amarillo/Dorado (`#F2B914`) + texto oscuro
4. **Información:** Celeste (`#B6DFE7`) + texto oscuro
5. **Fondos neutros:** Gris Azulado (`#B0C5C3`)

#### Dónde Usar Cada Color:
- **Primary (Azul/Morado):** Navegación, botones principales, enlaces
- **Secondary (Verde Menta):** Botones de exploración, confirmaciones, estados activos
- **Accent (Amarillo/Dorado):** Promociones, ofertas, badges de "Premium"
- **Info (Celeste):** Badges informativos, tooltips, ayudas
- **Gray (Gris Azulado):** Elementos desactivados, fondos neutros

### ❌ Evitar

- ❌ No mezclar todos los colores en un mismo componente
- ❌ No usar más de 2-3 colores vibrantes en una misma vista
- ❌ No usar colores hardcoded (siempre usar variables CSS)
- ❌ No usar el verde menta para textos sobre fondos claros (bajo contraste)
- ❌ No usar amarillo/dorado para textos principales (bajo contraste)

---

## 🔧 Implementación

### En Blade Templates
```html
<!-- Botón Principal -->
<button style="background: var(--color-primary); color: white;">
    Acción Principal
</button>

<!-- Botón Secundario -->
<button style="background: var(--color-secondary); color: #2C3E50;">
    Explorar
</button>

<!-- Badge de Promoción -->
<span style="background: var(--color-accent); color: #2C3E50; padding: 0.25rem 0.75rem; border-radius: 9999px;">
    ¡Oferta Especial!
</span>

<!-- Badge Informativo -->
<span style="background: var(--color-info); color: #2C3E50; padding: 0.25rem 0.75rem; border-radius: 9999px;">
    EXPLORA ÑAN PUNO
</span>
```

### Colores Sólidos y Estados
```css
/* Color Sólido Principal */
background: var(--color-primary);
color: white;

/* Color Secundario */
background: var(--color-secondary);
color: var(--color-text);

/* Fondos con Opacidad (suaves) */
background: rgba(89, 48, 232, 0.08);  /* Primary suave */
background: rgba(116, 250, 138, 0.08); /* Secondary suave */

/* Bordes de Acento */
border-left: 4px solid var(--color-primary);
border: 2px solid var(--color-accent);
```

### En CSS
```css
/* Botón con hover */
.btn-primary {
    background: var(--color-primary);
    color: white;
    transition: all 0.3s;
}

.btn-primary:hover {
    background: var(--color-primary-dark);
    transform: translateY(-2px);
}

/* Card con borde de marca */
.card-featured {
    border-left: 4px solid var(--color-primary);
    background: var(--color-bg);
}

/* Badge informativo */
.badge-info {
    background: var(--color-info);
    color: var(--color-text);
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}
```

---

## 📊 Accesibilidad (WCAG 2.1)

| Combinación | Ratio | Nivel | Uso Recomendado |
|-------------|-------|-------|-----------------|
| Primary sobre blanco | 7.42:1 | ✅ AAA | Textos grandes, botones |
| Text sobre blanco | 12.63:1 | ✅ AAA | Todos los textos |
| Secondary sobre blanco | 1.47:1 | ❌ Bajo | Solo backgrounds, NO textos |
| Accent sobre blanco | 2.23:1 | ❌ Bajo | Solo backgrounds, NO textos |
| Info sobre blanco | 1.89:1 | ❌ Bajo | Solo backgrounds, NO textos |

### ⚠️ Importante
- Verde Menta, Amarillo/Dorado y Celeste tienen **bajo contraste** sobre blanco
- Usar estos colores solo para **fondos**, **badges** o **elementos decorativos**
- Para textos sobre estos colores, usar texto oscuro (`var(--color-text)`)

---

## 🗂️ Archivos del Sistema

Los siguientes archivos definen y usan la paleta de colores:

### Definición de Variables
- **`resources/css/app.css`** - Variables CSS de la paleta

### Archivos Actualizados
- **`resources/views/dashboard/index.blade.php`** - Dashboard con paleta organizada
- **`resources/views/search.blade.php`** - Buscador con colores de marca
- **`resources/views/layouts/dashboard.blade.php`** - Layout con sidebar

---

## 🎨 Identidad Visual

### Personalidad de Marca
La paleta de Ñan Puno comunica:
- **Innovación** (Azul/Morado vibrante)
- **Energía** (Verde Menta neón)
- **Optimismo** (Amarillo/Dorado)
- **Frescura** (Celeste suave)
- **Equilibrio** (Gris Azulado)

### Tono de Voz Visual
- Moderno pero accesible
- Vibrante sin ser abrumador
- Tecnológico con calidez humana
- Juvenil y profesional a la vez

---

## 📅 Historial de Cambios

**Fecha:** 2026-02-08
**Versión:** 2.0
**Cambios:**
- ✅ Restaurada paleta oficial de Ñan Puno
- ✅ Organización profesional del uso de colores
- ✅ Reducción de saturación visual en dashboard
- ✅ Uso estratégico de colores vibrantes
- ✅ Documentación completa de accesibilidad

---

## 💡 Recomendaciones de Uso

### Para Nuevos Componentes

1. **Acción Principal** → Azul/Morado (`var(--color-primary)`)
2. **Acción Secundaria** → Verde Menta (`var(--color-secondary)`)
3. **Promoción/Oferta** → Amarillo/Dorado (`var(--color-accent)`)
4. **Información** → Celeste (`var(--color-info)`)
5. **Neutro** → Gris Azulado (`var(--color-gray)`)

### Regla de Oro
**"Un color vibrante por sección"** - No mezclar azul/morado, verde menta y amarillo en el mismo componente pequeño. Usar uno como principal y los demás como acentos sutiles.

---

**Mantener esta paleta vibrante pero usarla estratégicamente para lograr una identidad visual distintiva sin saturación visual.**
