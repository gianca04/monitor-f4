# Documentación de Arquitectura C4 — Sistema Monitor SAT

Esta carpeta contiene la documentación oficial de la arquitectura del **Sistema Monitor SAT** expresada mediante el **Modelo C4** y definida formalmente con **Structurizr DSL**.

La arquitectura documentada refleja fielmente la estructura real del proyecto derivada del análisis de código fuente, configuraciones de entorno (`.env`), gestor de dependencias (`composer.json`, `package.json`), orquestación de servicios (`supervisord.conf`, `nixpacks.toml`) y rutas de la aplicación (`routes/web.php`, `routes/api.php`).

---

## 📁 Estructura de la Documentación

```text
docs/architecture/c4/
├── workspace.dsl            # Definición principal en Structurizr DSL (Niveles 1, 2 y 3)
├── README.md                # Guía general y mapa del sitio de documentación
├── context.md               # Nivel 1 — System Context Diagram
├── containers.md            # Nivel 2 — Container Diagram
├── architecture-notes.md    # Decisiones, evidencias y supuestos arquitectónicos
└── components/
    ├── backend.md           # Nivel 3 — Componentes del Backend & Panel Web
    ├── admin_panel.md       # Nivel 3 — Componentes de Interfaz Administrativa (Filament)
    └── queue_worker.md      # Nivel 3 — Componentes del Procesador de Colas y Eventos
```

---

## 📐 Resumen de los Niveles C4

1. **[Nivel 1: Contexto del Sistema](context.md)**: Visión de alto nivel del Sistema Monitor SAT, sus usuarios (Administradores y Técnicos de Campo) y sus integraciones externas (Almacenamiento S3/MinIO y Servicio Push VAPID).
2. **[Nivel 2: Diagrama de Contenedores](containers.md)**: Desglose en contenedores ejecutables y almacenes (Nginx, Aplicación Backend Laravel 12 / Filament 4, Servidor WebSockets Reverb, Queue Worker, MySQL y Redis/File Cache).
3. **[Nivel 3: Diagramas de Componentes](components/backend.md)**: Estructura interna de los contenedores clave del sistema:
   - **[Backend & API](components/backend.md)**: Controladores API, Servicios de Dominio, Generadores de Documentos y Capa de Persistencia.
   - **[Panel Administrativo](components/admin_panel.md)**: Recursos Filament, Formularios Livewire y Reportes de Gestión.
   - **[Procesamiento Asíncrono & Tiempo Real](components/queue_worker.md)**: Eventos Reverb, Jobs de optimización de imagen y Workers PHP.
4. **[Registro de Decisiones y Evidencias](architecture-notes.md)**: Trazabilidad del código fuente a la arquitectura y matriz de certezas.

---

## 🛠️ Cómo Visualizar y Renderizar los Diagramas

### Opción 1: Structurizr CLI (Local)
Si cuentas con **Structurizr CLI** instalado en tu sistema:

```bash
# Validar la sintaxis del archivo DSL
structurizr validate -workspace docs/architecture/c4/workspace.dsl

# Exportar diagramas a formato PlantUML
structurizr export -workspace docs/architecture/c4/workspace.dsl -format plantuml

# Exportar diagramas a formato Mermaid
structurizr export -workspace docs/architecture/c4/workspace.dsl -format mermaid
```

### Opción 2: Structurizr Lite (Docker)
Puedes ejecutar **Structurizr Lite** localmente mediante Docker para previsualizar interactivamente los diagramas:

```bash
docker run -it --rm -p 8080:8080 -v $(pwd)/docs/architecture/c4:/usr/local/structurizr structurizr/lite
```
Luego abre tu navegador en `http://localhost:8080`.

### Opción 3: Structurizr UI Web (Visualizador En Línea)
Puedes copiar el contenido de `workspace.dsl` directamente en el editor web gratuito de Structurizr en [https://structurizr.com/dsl](https://structurizr.com/dsl).

---

## 🔍 Reglas de Gobernanza Arquitectónica

- **Fidelidad al Código**: Cualquier cambio arquitectónico en el sistema debe ser previamente implementado en el código fuente o configuración antes de actualizar esta documentación.
- **Sin Elementos Ficticios**: No se deben incorporar bases de datos, buses de mensajes o microservicios que no posean evidencia ejecutable dentro de la base de código.
