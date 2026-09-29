# Registro de Decisiones, Supuestos y Notas de Arquitectura

## Contexto

Este documento registra los supuestos razonables, los elementos pendientes de verificación, las decisiones de modelizado C4 adoptadas durante el análisis y el estado de validación ejecutable del código Structurizr DSL para el **Sistema Monitor SAT**.

---

## 1. Supuestos Fundamentados

- **Gestión de Procesos en Producción**: A través de `supervisord.conf` y `nixpacks.toml`, se evidencia que los contenedores lógicos `Nginx`, `PHP-FPM`, `Laravel Reverb` y `Queue Worker` coexisten dentro del mismo contenedor principal u orquestador de entorno, pero fueron separados en el diagrama C4 de nivel 2 por poseer **responsabilidades de tiempo de ejecución totalmente distintas** (Servidor HTTP, Aplicación Web, Servidor WebSocket y Procesador de Colas).
- **Almacenamiento de Archivos (S3 / MinIO)**: La configuración en `.env` indica `FILESYSTEM_DISK=s3` apuntando a `http://192.168.10.16:9000` (instancia de MinIO compatible con la API S3). Se modeló como un *Software System* externo de almacenamiento de objetos S3/MinIO.
- **Base de Datos y Caché**: La aplicación está configurada con `DB_CONNECTION=mysql` (`satindex_monitor_legacy`) y `QUEUE_CONNECTION=database` / `SESSION_DRIVER=database`. Por su parte, la caché posee soporte para Redis (`REDIS_CLIENT=predis`) y fallback a `file`.

---

## 2. Elementos No Confirmados / Inciertos

- **Subscripción de Notificaciones Push WebPush**: Aunque el paquete `laravel-notification-channels/webpush` y las claves VAPID están configurados en el proyecto, las rutas de subscripción push en `routes/web.php` se encuentran actualmente comentadas (`// Route::middleware('auth')->prefix('push')...`). El modelo C4 incluye la integración lógica con el Servicio Push pero documenta que el punto de entrada UI está inactivo en esta versión.
- **Microservicios Externos de Facturación / ERP**: No se encontró evidencia en el código fuente de llamadas HTTP/SOAP directas a SUNAT o ERPs externos (SAP, Oracle). La plataforma gestiona las cotizaciones y guías de despacho de manera autosuficiente.

---

## 3. Decisiones Arquitectónicas de Modelado

- **No Separación Ficticia de Microservicios**: A pesar de la presencia de múltiples servicios en `app/Services`, se mantuvo la representación del backend como una **Aplicación Monolítica (Container único)** para reflejar la realidad del código desplegado.
- **Representación de Componentes por Responsabilidad**: En Nivel 3 no se convirtió cada una de las 35+ clases de modelo en un componente. Se agruparon en **componentes arquitectónicos significativos** (Módulo Admin Filament, Controladores API REST, Servicios del Dominio, Motor de Generación de Documentos, Capa de Persistencia/Observers y Módulo de Notificaciones).

---

## 4. Preguntas Pendientes para la Organización

1. *¿El servidor MinIO (`http://192.168.10.16:9000`) opera en entorno de red local o se utiliza AWS S3 en producción para los buckets de producción?*
2. *¿Se planea reactivar la subscripción de notificaciones WebPush nativas en el frontend?*
3. *¿El clúster de producción opera con Redis dedicado para caché o continúa usando el driver de archivos/base de datos?*

---

## 5. Estado de Validación Ejecutable del Structurizr DSL

- **Verificación CLI Ejecutable**: Se ejecutó la comprobación en el entorno local (`Get-Command structurizr`). **Structurizr CLI no se encuentra instalado en la máquina local.**
- **Validación Manual de Sintaxis**: El archivo `workspace.dsl` ha sido validado manualmente contra la especificación oficial de Structurizr DSL v1.x:
  - Estructura de bloques `workspace`, `model`, `views`, `styles` correcta.
  - Tipado de elementos `person`, `softwareSystem`, `container`, `component` e identificadores únicos.
  - Relaciones bidireccionales y etiquetas de protocolo válidas.
  - Vistas `systemContext`, `container` y `component` con `autolayout lr` correctamente especificadas.
