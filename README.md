# Sistema de Gestión Parroquial Caritas-V3

Aplicación web para la gestión integral de Cáritas parroquiales, desarrollada con Laravel 12 y FilamentPHP 3.

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
- PHP 8.2+
- Composer 2.5+
- MySQL 8.0+/MariaDB 10.3+
- Node.js 18+ (solo para compilación de assets)

## ⚙️ Instalación (Para parroquias autorizadas)

### 1. Preparación del entorno
```bash
git clone https://github.com/daljo25/Caritas-V3.git --branch stable
cd Caritas-V3
```
### 2. Configuración inicial
```bash
composer install --no-dev
cp .env.example .env
php artisan key:generate`
```
### 3. Configuración de base de datos
Editar el archivo .env con tus credenciales y luego ejecutar:
```bash
php artisan migrate
```
### 4. Compilación de frontend (opcional)
```bash
npm install && npm run build
```
## 🔐 Acceso al Sistema
URL administrativa: /login

Credenciales iniciales: (proporcionadas por el administrador/licenciante)

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