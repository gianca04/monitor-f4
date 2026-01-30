# Configuración de Tailwind CSS v4 con Filament v4

Esta guía documenta los pasos necesarios para configurar Tailwind CSS v4 en un proyecto Laravel con Filament v4.

---

## 📦 Instalación de Dependencias

```bash
npm install -D tailwindcss@4.1.18 @tailwindcss/vite@4.1.18
```

---

## ⚙️ Configuración de Vite

Archivo: `vite.config.js`

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
```

---

## 🎨 Archivo CSS Principal

Archivo: `resources/css/app.css`

```css
@import 'tailwindcss';
@custom-variant dark (&:where(.dark, .dark *));

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.js';
```

> **Nota:** Usa `@source` para indicar a Tailwind dónde escanear clases CSS adicionales (paquetes de terceros, vistas personalizadas, etc.)

---

## 🎭 Tema Personalizado de Filament

### Paso 1: Crear el tema

```bash
php artisan make:filament-theme
```

### Paso 2: Configurar el archivo del tema

Archivo: `resources/css/filament/dashboard/theme.css`

```css
@import '../../../../vendor/filament/filament/resources/css/theme.css';

@source '../../../../app/Filament/**/*';
@source '../../../../resources/views/filament/**/*';
```

### Paso 3: Registrar el tema en el Panel Provider

Archivo: `app/Providers/Filament/DashboardPanelProvider.php`

```php
->viteTheme('resources/css/filament/dashboard/theme.css')
```

---

## 🚀 Comandos de Desarrollo

```bash
# Desarrollo (con hot reload)
npm run dev

# Producción (build optimizado)
npm run build
```

---

## 📋 Resumen de Archivos Modificados

| Archivo | Propósito |
|---------|-----------|
| `vite.config.js` | Configuración de Vite con plugin de Tailwind |
| `resources/css/app.css` | CSS principal con imports y sources |
| `resources/css/filament/dashboard/theme.css` | Tema personalizado de Filament |
| `DashboardPanelProvider.php` | Registro del tema en el panel |

---

### Warning "Unknown at rule @source"

Este es un warning del IDE, no un error. Tailwind v4 usa `@source` pero algunos editores aún no lo reconocen.