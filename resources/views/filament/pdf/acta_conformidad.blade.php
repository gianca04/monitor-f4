<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Acta de Conformidad - SAT Industriales S.A.C.</title>
    <style>
        /* ===== Reset y Fuentes ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            background-color: #f5f5f5;
            padding: 20px;
            color: #000;
            margin: 0;
        }

        /* ===== Contenedor Principal ===== */
        .page {
            width: 100%;
            max-width: 900px;
            background: white;
            padding: 10px 15px;
            border: 1px solid #00a99d;
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* ===== Encabezado con Tabla ===== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .header-table td {
            vertical-align: middle;
            padding: 5px;
        }

        .header-title {
            text-align: center;
        }

        .header-title h1 {
            font-size: 16px;
            color: #000;
            font-weight: normal;
            margin-bottom: 2px;
        }

        .header-title h2 {
            font-size: 12px;
            color: #000;
            font-weight: normal;
        }

        .header-number {
            text-align: right;
            color: #FF0000;
            font-size: 18px;
            font-weight: bold;
        }

        /* ===== Estilos de Sección ===== */
        .section {
            margin-bottom: 8px;
            border: 1px solid #00a99d;
        }

        .section-header {
            background-color: #00a99d;
            color: white;
            padding: 3px 8px;
            font-weight: bold;
            font-size: 11px;
        }

        .section-content {
            padding: 8px;
            background: #fff;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .form-table td {
            padding: 2px 5px;
            vertical-align: bottom;
        }

        .form-label {
            font-weight: bold;
            font-size: 10px;
            white-space: nowrap;
        }

        .form-value {
            border-bottom: 1px solid #00a99d;
            font-size: 10px;
        }

        .composite-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border: 1px solid #00a99d;
        }

        .composite-table td {
            padding: 5px;
            vertical-align: top;
            font-size: 10px;
        }

        .composite-table .label {
            font-weight: bold;
        }

        .composite-table .left {
            width: 130px;
            border-right: 1px solid #00a99d;
        }

        .dates-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .dates-table td {
            padding: 2px 5px;
            vertical-align: middle;
            font-size: 10px;
        }

        .date-box {
            border: 1px solid #00a99d;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 500;
            color: #000;
            display: inline-block;
            min-width: 120px;
            text-align: center;
            background-color: #fff;
        }

        .conformity-note {
            font-size: 10px;
            margin: 10px 0 0 0;
            line-height: 1.3;
        }

        .assets-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .assets-table th {
            color: #00a99d;
            font-style: italic;
            font-weight: bold;
            padding: 2px 5px;
            text-align: center;
            border: 1px solid #00a99d;
            font-size: 10px;
        }

        .assets-table td {
            padding: 2px 5px;
            border: 1px solid #00a99d;
            font-size: 10px;
            height: 18px;
        }

        .observations-box {
            width: 100%;
            min-height: 100px;
            border: 1px solid #00a99d;
            padding: 5px;
            font-size: 10px;
            line-height: 1.4;
        }

        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .signatures-table>tbody>tr>td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-header {
            background-color: #00a99d;
            color: white;
            text-align: center;
            padding: 6px 4px;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 12px;
        }

        .signature-area {
            /* 1. Definimos el tamaño CUADRADO (mismo ancho y alto) */
            width: 100px;
            height: 100px;

            /* 2. Centramos el cuadro dentro de la celda de la tabla */
            margin: 0 auto;

            /* 3. Centramos la imagen dentro del cuadro */
            display: flex;
            justify-content: center;
            align-items: center;

            /* Opcional: Borde suave para guiar la vista (puedes quitarlo si prefieres transparente) */
            border: 1px dashed #ccc;
            background-color: #fff;
            /* Asegura fondo blanco */
            overflow: hidden;
        }

        .signature-area img {
            /* 4. Hacemos que la imagen nunca exceda el cuadro */
            max-width: 100%;
            max-height: 100%;

            /* 5. LA CLAVE: 'contain' ajusta la imagen para que se vea ENTERA dentro del cuadrado sin deformarse */
            object-fit: contain;
        }

        .signature-field-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-field-table td {
            padding: 4px 0;
            font-size: 12px;
        }

        .signature-field-table .label {
            font-weight: bold;
            width: 70px;
        }

        .signature-field-table .value {
            border-bottom: 2px solid #00a99d;
        }

        /* ===== Botones Estilo Nativo Filament (Tailwind-like) ===== */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            align-items: center;
            margin-bottom: 25px;
            padding: 16px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        /* Estilo Base del Botón */
        .fi-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 16px;
            font-size: 14px;
            font-weight: 600;
            line-height: 1.5;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            outline: none;
        }

        /* Botón Volver (Gray/Secondary) */
        .btn-back {
            background-color: #ffffff;
            color: #374151;
            border-color: #d1d5db;
        }

        .btn-back:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
        }

        /* Botón PDF (Danger/Red) */
        .btn-pdf {
            background-color: #ef4444;
            color: #ffffff;
        }

        .btn-pdf:hover {
            background-color: #dc2626;
            box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.2);
        }

        /* Botón Excel (Success/Green) */
        .btn-excel {
            background-color: #22c55e;
            color: #ffffff;
        }

        .btn-excel:hover {
            background-color: #16a34a;
            box-shadow: 0 4px 6px -1px rgba(22, 163, 74, 0.2);
        }

        /* Botón Imprimir (Primary/Indigo) */
        .btn-print {
            background-color: #6366f1;
            color: #ffffff;
        }

        .btn-print:hover {
            background-color: #4f46e5;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        /* Ajuste de Iconos SVG */
        .fi-btn svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        /* Efecto Focus para todos (accesibilidad Filament) */
        .fi-btn:focus {
            ring: 2px;
            outline: 2px solid transparent;
            outline-offset: 2px;
        }

        /* ===== CORRECCIÓN DE IMPRESIÓN ===== */
        @media print {

            html,
            body {
                width: 100%;
                height: 99.5% !important;
                /* Evita que el navegador pida una 2da hoja */
                margin: 0 !important;
                padding: 0 !important;
                background-color: white !important;
                overflow: hidden !important;
                /* Mata el scroll y la hoja extra */
            }

            .page {
                width: 100% !important;
                max-width: 100% !important;
                height: 100% !important;
                margin: 0 !important;
                padding: 10mm 15mm !important;
                /* Márgenes internos de la hoja */
                border: none !important;
                box-shadow: none !important;
                display: block !important;
                overflow: hidden !important;
            }

            .action-buttons,
            script,
            .swal2-container {
                display: none !important;
            }

            @page {
                size: A4 portrait;
                margin: 0;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body>


    <div class="action-buttons">
        <button class="fi-btn btn-back" onclick="cerrarPestana()" title="Cerrar pestaña">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Volver
        </button>

        <button class="fi-btn btn-pdf" onclick="descargarPDF()" title="Descargar como PDF">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            Descargar PDF
        </button>

        <button class="fi-btn btn-excel" onclick="descargarExcel()" title="Descargar como Excel">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 16.5c0-.621.504-1.125 1.125-1.125m17.25 0c.621 0 1.125.504 1.125 1.125" />
            </svg>
            Descargar Excel
        </button>

        <button class="fi-btn btn-print" onclick="imprimirDocumento()" title="Imprimir documento">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.32 0h1.715A2.625 2.625 0 0 0 21.75 15.375V9.375A2.625 2.625 0 0 0 19.125 6.75H17.25m-10.5 0H4.875A2.625 2.625 0 0 0 2.25 9.375v6A2.625 2.625 0 0 0 4.875 18H6.34m11.16-12h-1.284a3.375 3.375 0 0 0-3.375-3.375h-1.5a3.375 3.375 0 0 0-3.375 3.375H6.75" />
            </svg>
            Imprimir
        </button>
    </div>

    <div class="page">

        <!-- ===== ENCABEZADO ===== -->
        <table class="header-table">
            <tr>
                <td style="width: 200px;">
                    @php
                        $logoPath = public_path('images/Logo2.png');
                        $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
                        $logoMime = 'image/png';
                    @endphp
                    @if ($logoData)
                        <img src="data:{{ $logoMime }};base64,{{ $logoData }}" alt="Logo2" style="height: 80px;">
                    @endif
                </td>
                <td class="header-title">
                    <h1>Acta de conformidad</h1>
                    <h2>Gerencia de ingeniería y Mantenimiento</h2>
                </td>
                <td class="header-number" style="width: 150px;">
                    Nº <span style="font-size: 22px;">{{ $numero }}</span>
                </td>
            </tr>
        </table>

        <!-- ===== SECCIÓN A: Información del trabajo realizado ===== -->
        <div class="section">
            <div class="section-header">SECCIÓN A: Información del trabajo realizado</div>
            <div class="section-content">
                <!-- Fila 1: Razón Social y R.U.C. -->
                <table class="form-table">
                    <tr>
                        <td class="form-label" style="width: 100px;">A1) Razón Social:</td>
                        <td class="form-value">{{ $razon_social }}</td>
                        <td class="form-label" style="width: 50px;">R.U.C.:</td>
                        <td class="form-value" style="width: 120px;">{{ $ruc }}</td>
                    </tr>
                </table>

                <!-- Fila 2: Tienda y Dirección -->
                <table class="form-table">
                    <tr>
                        <td class="form-label" style="width: 70px;">A2) Tienda:</td>
                        <td class="form-value">{{ $tienda }}</td>
                        <td class="form-label" style="width: 70px;">Dirección:</td>
                        <td class="form-value">{{ $direccion }}</td>
                    </tr>
                </table>

                <!-- Fila 3: N° de OT y Descripción del Servicio -->
                <table class="composite-table">
                    <tr>
                        <td class="left">
                            <span class="label">A3) N° de Orden de trabajo:</span><br>
                            {{ $work_order_number }}
                        </td>
                        <td>
                            <span class="label">A4) Descripción del Servicio:</span><br>
                            {{ $descripcion_servicio }}
                        </td>
                    </tr>
                    <tr>
                        <td class="left">
                            <span class="label">A5) N° de Solicitud:</span><br>
                            {{ $request_number ?? 'N/A' }}
                        </td>
                    </tr>
                </table>

                <!-- Fila 4: Fechas -->
                <table class="dates-table">
                    <tr>
                        <td class="form-label">A7) Fecha de inicio:</td>
                        <td style="flex-grow: 1;">
                            <span class="date-box">
                                {{ !empty($fecha_inicio) ? $fecha_inicio : 'N/A' }}
                            </span>
                        </td>
                        <td class="form-label" style="padding-left: 30px;">A8) Fecha de fin:</td>
                        <td style="flex-grow: 1;"><span class="date-box">{{ $fecha_fin ?? 'N/A' }}</span></td>
                    </tr>
                </table>

                <!-- Nota de Conformidad -->
                <div class="conformity-note">
                    A9) En el presente documento, se consta la <strong>CONFORMIDAD</strong> de los servicios presentados
                    por la empresa:
                </div>
            </div>
        </div>

        <!-- ===== SECCIÓN B: Disposición de los activos intervenidos ===== -->
        <div class="section">
            <div class="section-header">SECCIÓN B: Disposición de los activos intervenidos</div>
            <div class="section-content">
                <div class="section-subtitle">
                    B1) En esta sección, <strong>el contratista o proveedor del servicio deberá enlistar todos los
                        activos intervenidos durante la actividad ejecutada.</strong>
                </div>

                <table class="assets-table">
                    <thead>
                        <tr>
                            <th style="width: 30px;">N°</th>
                            <th style="width: 220px;">Tipo de Activos</th>
                            <th style="width: 80px;">Cantidad</th>
                            <th>Comentarios</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assets as $index => $asset)
                            <tr>
                                <td class="number">{{ $index + 1 }}</td>
                                <td class="asset-name">{{ $asset['name'] }} ({{ $asset['selected'] ? 'X' : '  ' }})
                                </td>
                                <td style="text-align: center;">{{ $asset['quantity'] }}</td>
                                <td>{{ $asset['comments'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Área de Observaciones -->
                <div class="observations-area">
                    <div class="observations-label">B2) Observaciones Generales del Mantenimiento Preventivo</div>
                    <div class="observations-box">
                        {!! $observaciones !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== SECCIÓN C: Responsabilidad de la conformidad y firmas ===== -->
        <div class="section">
            <div class="section-header">SECCIÓN C: Responsabilidad de la conformidad y firmas</div>
            <div class="section-content">
                <div class="responsibility-text">
                    Quien suscribe esta Acta, declara que se ha concluido el trabajo y no queda nada pendiente de
                    culminar, en la medida que se ha dado cumplimiento a todos los requerimientos solicitados en la
                    incidencia. La firma de esta Acta de Conformidad por quien figura como responsable, es el título que
                    habilita la procedencia del pago en contraprestación al trabajo realizado, y por tanto genera
                    responsabilidad directa en quien suscribe esta Acta debido al impacto económico que genera y a las
                    disposiciones de seguridad que deben tomarse en cuenta, por lo que en caso de incumplimiento la
                    empresa se encontrará en la facultad de tomar las medidas que correspondan. Sino hubiera conformidad
                    con el trabajo que debía realizarse, el responsable debe <strong>Incluir observaciones.</strong>
                </div>

                <div class="client-acceptance">
                    SEÑOR CLIENTE LA FIRMA DE ESTE DOCUMENTO DA POR ACEPTADO EL TRABAJO REALIZADO POR NUESTRO PERSONAL
                </div>

                <!-- Área de Firmas -->
                <table class="signatures-table">
                    <tr>
                        <!-- Firma Cliente -->
                        <td style="width: 50%; padding-right: 20px;">
                            <div class="signature-header">CLIENTE</div>

                            <div class="signature-area">
                                @if ($firma_cliente)
                                    <img src="{{ $firma_cliente }}" alt="Firma Cliente">
                                @endif
                            </div>

                            <div style="text-align: center; font-size: 10px; font-weight: bold; margin-bottom: 8px;">
                                FIRMA</div>

                            <table class="signature-field-table">
                                <tr>
                                    <td class="label">NOMBRE:</td>
                                    <td class="value">{{ $cliente_nombre }}</td>
                                </tr>
                                <tr>
                                    <td class="label">{{ $cliente_tipo_doc }}:</td>
                                    <td class="value">{{ $cliente_documento }}</td>
                                </tr>
                            </table>
                        </td>

                        <!-- Firma SAT Industriales -->
                        <td style="width: 50%; padding-left: 10px;">
                            <div class="signature-header">SAT INDUSTRIALES S.A.C.</div>

                            <div class="signature-area">
                                @if ($firma_empleado)
                                    <img src="{{ $firma_empleado }}" alt="Firma Empleado">
                                @endif
                            </div>

                            <div style="text-align: center; font-size: 10px; font-weight: bold; margin-bottom: 8px;">
                                FIRMA</div>

                            <table class="signature-field-table">
                                <tr>
                                    <td class="label">NOMBRE:</td>
                                    <td class="value">{{ $empleado_nombre }}</td>
                                </tr>
                                <tr>
                                    <td class="label">{{ $empleado_tipo_doc }}:</td>
                                    <td class="value">{{ $empleado_documento }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    @if (isset($isPreview) && $isPreview === true)
        <script>
            function descargarPDF() {
                Swal.fire({
                    title: 'Generando PDF...',
                    text: 'Estamos preparando tu documento, por favor espera.',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();

                        // 1. Iniciamos la apertura del PDF
                        window.open('{{ route('actas.pdf', $id ?? 0) }}', '_blank');

                        // 2. Esperamos un poco más para mostrar el éxito
                        // Subimos a 3000ms (3 segundos) para que se note el proceso
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Documento Generado!',
                                text: 'Si no se abrió automáticamente, revisa tus ventanas emergentes.',
                                timer: 2500, // El mensaje de éxito ahora dura más
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                        }, 3000);
                    }
                });
            }
            const Toast = Swal.mixin({
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false
            });

            function descargarExcel() {
                Swal.fire({
                    title: 'Preparando Excel',
                    html: 'Estamos organizando tus datos...',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                    willClose: () => {
                        console.log("Iniciando descarga de Excel...");
                        window.open('{{ route('actas.excel', parameters: $id ?? 0) }}', '_blank');
                    }

                })
            }

            function imprimirDocumento() {
                Swal.fire({
                    title: '¿Preparar impresión?',
                    text: "Se abrirá el diálogo de impresión del sistema.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, imprimir',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.print();
                    }
                });
            }

            function cerrarPestana() {
                window.close();
                setTimeout(function () {
                    window.history.back();
                }, 300);
            }
        </script>
    @else
        <script>
            // Funciones para los botones de acción
            function descargarPDF() {
                window.location.href = '{{ route('actas.pdf', $id ?? 0) }}';
            }

            function descargarExcel() {
                window.location.href = '{{ route('actas.excel', $id ?? 0) }}';
            }

            function imprimirDocumento() {
                window.print();
            }

            function cerrarPestana() {
                window.close();
            }
        </script>
    @endif
</body>

</html>