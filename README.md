# Real Estate Property Listing Platform

Proyecto inmobiliario **educacional** para construir un sistema de **listado y búsqueda de propiedades** (venta/renta), con **dashboard de usuario**, **panel de agente** y **panel de administrador**. Incluye **AMC (Análisis de Mercado Comparativo)** para agentes y admins.

---

## Tabla de contenidos

- [Arquitectura](#arquitectura)
- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Despliegue](#despliegue)
- [Problemas resueltos](#problemas-resueltos)

---

## Arquitectura

### Stack tecnológico

| Capa | Tecnología |
|------|------------|
| Backend | PHP 8.2 + Laravel 12 |
| Base de datos | MySQL / SQLite |
| Frontend | Blade, Bootstrap 5, Tailwind CSS 4 |
| Build | Vite 7 |
| PDF | DomPDF (barryvdh/laravel-dompdf) |

### Estructura de la aplicación

```
app/
├── Http/Controllers/
│   ├── Admins/          # Panel admin (propiedades, usuarios, sectores, AMC, agentes)
│   ├── Agent/           # Panel agente (propiedades, solicitudes, AMC)
│   ├── Api/             # API AMC (ejecución y exportación)
│   ├── Props/           # Propiedades públicas y solicitudes
│   └── Users/           # Usuario autenticado (solicitudes, favoritos)
├── Models/
│   ├── Prop/            # Property, Request, SavedProperties, PropImage, HomeType
│   ├── Admin/           # Admin (guard separado)
│   └── ...              # User, Sector, Acabado, PonderacionAcabado, AgentApplication
├── Services/
│   └── AmcService.php   # Lógica del Análisis de Mercado Comparativo
└── Http/Middleware/
    ├── EnsureAgent.php  # Restringe rutas a usuarios con rol agent
    └── EnsureAgentOrAdmin # Restringe AMC a agentes o admins
```

### Autenticación y roles

| Guard | Modelo | Uso |
|-------|--------|-----|
| `web` | `User` | Usuarios públicos y agentes |
| `admin` | `Admin` | Panel de administración |

**Roles de usuario (`User.role`):**

- `user` — Usuario normal: solicitudes, favoritos
- `agent` — Agente inmobiliario: CRUD propiedades, AMC, solicitudes de clientes

### Flujo de datos

1. **Propiedades** — Creadas por agentes o admins. Estados: `draft`, `active`, `paused`, `closed`, `sold`, `rented`, `reserved`.
2. **Solicitudes** — Usuarios envían solicitudes de información sobre propiedades.
3. **AMC** — Agentes/admins ejecutan análisis comparativo por sector, acabados y área; exportan PDF.
4. **Sectores** — Zonas geográficas para clasificación y AMC (Chacao, Altamira, Los Naranjos, etc.).

### Rutas principales

| Ruta | Descripción |
|------|-------------|
| `/` | Home con propiedades destacadas |
| `/properties` | Listado y filtros (tipo, precio) |
| `/property-details/{id}` | Detalle de propiedad |
| `/user/requests`, `/user/favorites` | Dashboard usuario |
| `/agent/dashboard`, `/agent/properties` | Panel agente |
| `/admin/dashboard` | Panel admin |
| `/amc` | Herramienta AMC (agentes/admins) |

---

## Requisitos

- PHP 8.2+ (extensiones: gd, bcmath, pdo_mysql, mbstring, xml, curl, zip)
- Composer
- Node.js 18+
- MySQL 8+ o SQLite

---

## Instalación

### 1. Clonar e instalar dependencias

```bash
git clone <repo-url> realstate
cd realstate

composer install
npm install
```

### 2. Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` según tu entorno:

- **SQLite** (por defecto): `DB_CONNECTION=sqlite`
- **MySQL**: descomentar y configurar `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

### 3. Base de datos

```bash
php artisan migrate
php artisan db:seed
```

### 4. Assets y storage

```bash
npm run build
php artisan storage:link
```

### 5. Ejecutar en local

```bash
php artisan serve
```

Acceder a `http://localhost:8000`.

### Usuarios de prueba (seed)

| Email | Rol | Uso |
|-------|-----|-----|
| `test@example.com` | user | Usuario normal |
| `maria.rodriguez@umbral.com` | agent | Agente |
| `carlos.mendoza@umbral.com` | agent | Agente |
| `valentina.torres@umbral.com` | agent | Agente |

*(Contraseña por defecto: `password`)*

---

## Despliegue

### Railway / Nixpacks

El proyecto incluye `nixpacks.toml` y `Procfile` para despliegue en Railway u otros entornos compatibles.

- **Build**: `composer install --no-dev`, `npm install && npm run build`
- **Start**: `php artisan serve --host=0.0.0.0 --port=$PORT`
- **Post-deploy**: `php artisan migrate --force && php artisan storage:link`

Variables de entorno recomendadas: `APP_KEY`, `APP_URL`, `DB_*` (MySQL en producción).

---

## Problemas resueltos

### 1. Generación de reportes AMC

- **Problema**: No había suficientes propiedades por sector para generar AMC con comparables válidos.
- **Solución**: Se crearon 8 propiedades por sector (64 en total) con datos variados, imágenes alternadas y distribución entre agentes. Se añadió `PropImageSeeder` para galerías automáticas.

### 2. Propiedades de Los Naranjos

- **Problema**: Todas usaban la misma imagen y nombres repetitivos.
- **Solución**: Nombres genéricos por sector, imágenes cicladas con offset y distribución entre agentes.

### 3. Hero con fondo azul

- **Problema**: El overlay oscurecía la imagen del hero.
- **Solución**: Se eliminó la clase `overlay` de los slides del hero para mostrar la imagen sin filtro.

### 4. Navbar sobre hero

- **Problema**: Navbar con fondo azul sólido tapaba la imagen.
- **Solución**: Navbar transparente con `position: absolute` sobre el hero.

### 5. Visibilidad del texto del navbar

- **Problema**: Texto translúcido (`rgba(255,255,255,0.6)`) poco legible sobre la imagen.
- **Solución**: Color blanco sólido en links y gradiente oscuro en la parte superior del hero (`::after`) para mejorar contraste.

### 6. Menú para agentes

- **Problema**: Los agentes veían "Mis solicitudes" y "Mis favoritos" (solo para usuarios normales).
- **Solución**: El menú muestra solo "Panel de agente" y "Cerrar sesión" cuando el usuario tiene rol `agent`.

---

## Propósito

Este repositorio es con fines **educativos** y de práctica, para materializar progresivamente un proyecto inmobiliario real.
