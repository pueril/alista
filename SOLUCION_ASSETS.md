# Solución: Assets No Se Cargan (CSS/JS)

## 🔍 Problema

Los estilos no se cargan porque la carpeta `build/` con los assets compilados no está en la ubicación correcta.

---

## ✅ SOLUCIÓN RÁPIDA

### Paso 1: Mover carpeta `build/` a `public_html/`

En tu servidor, necesitas mover la carpeta `build/` desde `public_html/public/build/` a `public_html/build/`

**Estructura correcta:**
```
public_html/
├── index.php
├── .htaccess
├── build/              ← DEBE ESTAR AQUÍ
│   ├── assets/
│   │   ├── app-xxxxx.css
│   │   └── app-xxxxx.js
│   └── manifest.json
├── app/
├── bootstrap/
└── ...
```

### Paso 2: Verificar que los archivos existen

Asegúrate de que en `public_html/build/assets/` existan:
- Un archivo CSS (ej: `app-abc123.css`)
- Un archivo JS (ej: `app-abc123.js`)
- El archivo `manifest.json` en `public_html/build/`

### Paso 3: Limpiar caché de Laravel

Si tienes acceso SSH, ejecuta:
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Si no tienes SSH, elimina manualmente:
- `bootstrap/cache/config.php` (si existe)
- `bootstrap/cache/routes.php` (si existe)
- `bootstrap/cache/services.php` (si existe)

---

## 🔧 SOLUCIÓN ALTERNATIVA: Si los assets siguen sin cargar

### Opción A: Verificar configuración de Vite en producción

Laravel busca los assets usando `@vite()`. En producción, necesita encontrar el `manifest.json`.

Verifica que `public_html/build/manifest.json` existe y tiene contenido.

### Opción B: Cambiar a usar `asset()` directamente (Temporal)

Si nada funciona, puedes cambiar temporalmente en `resources/views/layouts/app.blade.php`:

**Cambiar de:**
```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

**A:**
```blade
<link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
<script src="{{ asset('build/assets/app.js') }}"></script>
```

**PERO** esto requiere que conozcas los nombres exactos de los archivos compilados. Mejor solución: mover `build/` correctamente.

---

## 📋 Checklist

- [ ] Carpeta `build/` movida a `public_html/build/`
- [ ] Archivos CSS y JS existen en `public_html/build/assets/`
- [ ] Archivo `manifest.json` existe en `public_html/build/`
- [ ] Caché de Laravel limpiada
- [ ] Página recargada (Ctrl+F5 para limpiar caché del navegador)

---

## 🧪 Verificación

1. Abre las herramientas de desarrollador (F12)
2. Ve a la pestaña **Network** o **Red**
3. Recarga la página
4. Busca archivos `.css` y `.js`
5. Verifica que se cargan desde: `https://tudominio.com/build/assets/...`

Si ves errores 404 en los archivos CSS/JS, significa que la ruta no es correcta.

---

## 🆘 Si Nada Funciona

1. **Verifica que compilaste los assets localmente:**
   ```bash
   npm run build
   ```

2. **Sube nuevamente la carpeta `build/` completa** desde tu máquina local:
   - De `public/build/` (local) → a `public_html/build/` (servidor)

3. **Verifica permisos:**
   - La carpeta `build/` debe tener permisos 755
   - Los archivos dentro deben tener permisos 644

---

**¡Con estos pasos deberían cargarse los estilos!** 🎨
