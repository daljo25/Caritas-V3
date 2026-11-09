# Sistema de Gestión Parroquial Caritas-V3

Aplicación web para la gestión integral de Cáritas parroquiales, desarrollada con Laravel 13 y FilamentPHP 5.

## 🧱 Stack tecnológico

| Componente | Versión |
| --- | --- |
| PHP | 8.3+ (producción: 8.4) |
| Laravel | 13.x |
| FilamentPHP | 5.x |
| Livewire | 4.x |
| Tailwind CSS | 4.x |
| Vite | 8.x |
| Pest | 5.x |
| MySQL / MariaDB | 8.0+ / 10.3+ |

## 📜 Licencia y Uso
**Este software tiene una licencia propietaria restringida**:
- Uso exclusivo para Cáritas parroquiales autorizadas
- Prohibida su redistribución sin autorización expresa
- Modificaciones permitidas solo para adaptaciones locales
- Requiere acuerdo escrito para instalación en nuevas parroquias

[Ver licencia completa](LICENSE)

## ✨ Características Principales
- **Gestión de beneficiarios** (usuarios/asistentes)
- **Administración de voluntarios**
- **Control de ayudas y donaciones**
- **Registro de donantes**
- **Panel administrativo** con:
  - Tableros de control
  - Reportes personalizables
  - Gestión de permisos

## 🚀 Requisitos Técnicos
- PHP 8.3+ con las extensiones habituales de Laravel
- Composer 2.5+
- MySQL 8.0+/MariaDB 10.3+
- Node.js 20.19+ o 22.12+ (solo para compilación de assets)

## ⚙️ Instalación (Para parroquias autorizadas)

### 1. Preparación del entorno
```bash
git clone https://github.com/daljo25/Caritas-V3.git
cd Caritas-V3
```
### 2. Configuración inicial
```bash
composer install --no-dev
cp .env.example .env
php artisan key:generate
```
### 3. Configuración de base de datos
Editar el archivo .env con tus credenciales y luego ejecutar:
```bash
php artisan migrate
php artisan storage:link
```
### 4. Compilación de frontend
`public/build` no se versiona, así que este paso es obligatorio en cada instalación:
```bash
npm ci && npm run build
```
## 🔐 Acceso al Sistema
URL administrativa: /login

Credenciales iniciales: (proporcionadas por el administrador/licenciante)

> En Filament 5 el modelo de usuario debe implementar
> `Filament\Models\Contracts\FilamentUser`; fuera de `local` el panel devuelve
> 403 a quien no lo implemente. `App\Models\User` ya lo implementa mediante
> `canAccessPanel()`.

## ⚠️ Notas Importantes
Cada parroquia debe tener su propia instalación independiente

Para instalaciones multi-parroquia en un mismo servidor:

Se requieren bases de datos separadas

Dominios/subdominios independientes

Prohibido usar en organizaciones no parroquiales

## 📬 Soporte y Contacto

Para solicitudes de:

Nueva instalación autorizada

Soporte técnico

Personalizaciones

Contactar al desarrollador:
Daljomar Morillo [daljo25](https://github.com/daljo25)
