workspace "Sistema Monitor SAT" "Documentación de arquitectura C4 para el Sistema Monitor SAT Industriales" {

    model {
        # --- PERSONAS / ACTORES ---
        adminUser = person "Administrador / Operador de Oficina" "Usuario administrativo que gestiona proyectos, cotizaciones, almacenes, tiempos y reportes desde el panel web." "User"
        fieldTechnician = person "Técnico / Operario de Campo" "Personal operativo que registra asistencia, partes de trabajo, reportes de visita y fotografías de evidencia desde campo." "User"

        # --- SISTEMAS EXTERNOS ---
        s3Storage = softwareSystem "Servicio de Almacenamiento S3 / MinIO" "Almacenamiento de objetos para imágenes de evidencias, archivos adjuntos y documentos PDF generados." "External System"
        pushService = softwareSystem "Servicio Push (WebPush / VAPID)" "Servidor de notificaciones push de navegadores web y dispositivos móviles." "External System"

        # --- SISTEMA PRINCIPAL ---
        monitorSystem = softwareSystem "Sistema Monitor SAT" "Sistema integral de gestión de proyectos, cotizaciones, parte diario de trabajo, control de almacén y reportes de campo." {
            
            # --- CONTENEDORES ---
            webServer = container "Servidor Web Nginx" "Servidor HTTP y Proxy Inverso que gestiona peticiones Web, API y WebSocket, entregando activos estáticos y SSL." "Nginx 1.2x" "Web Server"
            
            backendApp = container "Aplicación Backend & Panel Web" "Monolito Laravel 12 con Panel Administrativo Filament 4, API REST y motores de generación de documentos." "PHP 8.3 / Laravel 12 / Filament 4" "Web & API Application" {
                
                # --- COMPONENTES DEL BACKEND ---
                filamentAdminModule = component "Módulo Admin Filament" "Proporciona la interfaz administrativa web interactiva para gestión de clientes, proyectos, cotizaciones y personal." "Filament v4 / Livewire / Blade"
                apiControllersModule = component "Controladores API REST" "Expone endpoints HTTP JSON securizados para autenticación, sincronización de datos y registro de trabajo en campo." "Laravel Controllers / Sanctum"
                domainServicesModule = component "Servicios del Dominio" "Contiene la lógica de negocio central (QuoteService, WorkReportService, ConsumptionService, RequestConsolidatedService)." "PHP Services"
                documentEngineModule = component "Motor de Generación de Documentos" "Genera reportes técnicos en PDF, hojas de trabajo Excel y documentos Word (DomPDF, mPDF, PhpSpreadsheet, PhpWord)." "PHP Libraries"
                persistenceModule = component "Capa de Persistencia y Observers" "Modelos Eloquent y observadores de eventos para la gestión de ciclo de vida de entidades e integridad de datos." "Laravel Eloquent / Observers"
                notificationModule = component "Módulo de Notificaciones y Eventos" "Gestiona la emisión de eventos en tiempo real y despacho de notificaciones push (MessageSent, GeneralPushNotification)." "Laravel Events / Notifications"
            }

            websocketServer = container "Servidor WebSockets (Laravel Reverb)" "Servicio en tiempo real para comunicación bidireccional de chat interno y notificaciones reactivas." "Laravel Reverb / PHP CLI" "WebSocket Server"
            
            queueWorker = container "Procesador de Colas (Queue Worker)" "Proceso en segundo plano para la optimización asíncrona de imágenes (WebP), reportes pesados y notificaciones." "PHP CLI / Laravel Worker" "Background Worker"
            
            database = container "Base de Datos Relacional" "Almacena los datos estructurados del sistema, sesiones de usuarios y cola de trabajos persistida." "MySQL / MariaDB" "Database"
            
            cacheServer = container "Almacén de Caché" "Almacenamiento de valores clave-valor para optimización de consultas y caché de aplicación." "Redis / File Cache" "Cache"
        }

        # --- RELACIONES NIVEL 1 (CONTEXTO) ---
        adminUser -> monitorSystem "Gestiona proyectos, aprueba reportes, emite cotizaciones y consulta métricas" "HTTPS"
        fieldTechnician -> monitorSystem "Registra asistencias, reportes de trabajo, evidencias fotográficas y consultas desde campo" "HTTPS / REST API"
        monitorSystem -> s3Storage "Almacena y recupera imágenes de evidencia, fotos de perfil y reportes PDF" "S3 API / HTTPS"
        monitorSystem -> pushService "Envía notificaciones push a navegadores y dispositivos registrados" "WebPush / HTTPS"

        # --- RELACIONES NIVEL 2 (CONTENEDORES) ---
        adminUser -> webServer "Navega e interactúa con el panel de administración" "HTTPS"
        fieldTechnician -> webServer "Envía peticiones API y se conecta al chat en tiempo real" "HTTPS / WSS"
        
        webServer -> backendApp "Redirige peticiones HTTP / API al socket PHP-FPM" "FastCGI / HTTP"
        webServer -> websocketServer "Realiza proxy inverso de conexiones WebSocket" "WSS / HTTP"
        
        backendApp -> database "Lee y escribe información de proyectos, usuarios, reportes y cotizaciones" "PDO / MySQL (3306)"
        backendApp -> cacheServer "Almacena y consulta caché de aplicación" "Redis Protocol / File System"
        backendApp -> websocketServer "Emite eventos broadcasting en tiempo real (Reverb API)" "HTTP (8080)"
        backendApp -> s3Storage "Sube fotos de trabajo, evidencias y reportes generados" "S3 API (9000/443)"
        backendApp -> pushService "Despacha notificaciones push VAPID" "HTTPS"
        
        queueWorker -> database "Obtiene y procesa trabajos pendientes de la tabla 'jobs'" "PDO / MySQL (3306)"
        queueWorker -> backendApp "Ejecuta clases Job de la aplicación (ConvertImageToWebPJob)" "Internal PHP Execution"
        queueWorker -> s3Storage "Guarda imágenes convertidas a formato WebP" "S3 API"

        # --- RELACIONES NIVEL 3 (COMPONENTES BACKEND) ---
        adminUser -> filamentAdminModule "Interactúa mediante la interfaz visual en el navegador" "HTTPS"
        fieldTechnician -> apiControllersModule "Consume endpoints REST JSON securizados con Sanctum" "HTTPS REST API"

        filamentAdminModule -> domainServicesModule "Invoca lógica de negocio para cálculos de cotización y consolidados" "PHP Direct Call"
        filamentAdminModule -> persistenceModule "Consulta y actualiza registros Eloquent" "Eloquent ORM"
        filamentAdminModule -> documentEngineModule "Solicita descarga y vista previa de PDFs/Excel/Word" "PHP Direct Call"
        filamentAdminModule -> notificationModule "Dispara notificaciones push y eventos en tiempo real" "Laravel Event Bus"

        apiControllersModule -> domainServicesModule "Delega procesamiento de reportes y partes de trabajo" "PHP Direct Call"
        apiControllersModule -> persistenceModule "Persiste registros de asistencia, fotos y reportes" "Eloquent ORM"
        apiControllersModule -> notificationModule "Emite notificaciones sobre nuevos reportes de campo" "Laravel Event Bus"

        domainServicesModule -> persistenceModule "Lee y modifica modelos del dominio" "Eloquent ORM"
        domainServicesModule -> documentEngineModule "Proporciona datos estructurados para la generación de documentos" "PHP Data Passing"

        documentEngineModule -> s3Storage "Almacena copias de reportes generados o consume plantillas/imágenes" "S3 Client API"
        persistenceModule -> database "Ejecuta consultas SQL contra las tablas relacionales" "PDO MySQL"
        persistenceModule -> cacheServer "Guarda/obtiene fragmentos en caché" "Cache API"
        notificationModule -> websocketServer "Envía payloads de eventos real-time vía Reverb Client" "HTTP REST API"
        notificationModule -> pushService "Envia paquetes WebPush firmados con VAPID" "HTTPS Client"
        notificationModule -> database "Encola trabajos asíncronos en la tabla 'jobs'" "Eloquent / Database Queue"
    }

    views {
        systemContext monitorSystem "SystemContext" {
            title "C4 — Nivel 1: Contexto del Sistema Monitor SAT"
            include *
            autolayout lr
        }

        container monitorSystem "Containers" {
            title "C4 — Nivel 2: Diagrama de Contenedores del Sistema Monitor SAT"
            include *
            autolayout lr
        }

        component backendApp "BackendComponents" {
            title "C4 — Nivel 3: Componentes de la Aplicación Backend & Panel Web"
            include *
            autolayout lr
        }

        styles {
            element "Person" {
                background #08427b
                color #ffffff
                shape person
            }
            element "Software System" {
                background #1168bd
                color #ffffff
            }
            element "External System" {
                background #999999
                color #ffffff
            }
            element "Container" {
                background #438dd5
                color #ffffff
            }
            element "Web Server" {
                background #226699
                color #ffffff
                shape Pipe
            }
            element "Web & API Application" {
                background #438dd5
                color #ffffff
            }
            element "WebSocket Server" {
                background #2b78c5
                color #ffffff
            }
            element "Background Worker" {
                background #5c9cd8
                color #ffffff
            }
            element "Database" {
                background #2b78c5
                color #ffffff
                shape Cylinder
            }
            element "Cache" {
                background #2b78c5
                color #ffffff
                shape Cylinder
            }
            element "Component" {
                background #85bbf0
                color #000000
            }
        }
    }
}
