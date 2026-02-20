<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informe de Evidencias - {{ $visitReport->name }}</title>
</head>

<body>
    {{-- TABLA DE CABECERA DEL DOCUMENTO --}}
    <table class="header-table">
        <thead>
            <tr>
                <th>
                    <img src="{{ public_path('images/Logo2.png') }}" alt="Logo" class="header-logo">
                </th>
                <th>
                    <div class="empresa-info">
                        <h1>SAT INDUSTRIALES S.A.C</h1>
                        <p class="direccion">
                            Dirección: Km 9 de la expansión Av. José Aguilar Santisteban. (Pista nueva Curumuy - Fundo
                            las Mercedes)
                        </p>
                        <table class="info-table">
                            <tbody>
                                <tr>
                                    <td class="info-cell">RUC: <a href="20539249640">20539249640</a></td>
                                    <td class="info-cell">Teléfono: <a href="tel:959730981">959 730 981</a></td>
                                    <td class="info-cell">Correo: <a
                                            href="mailto:operaciones@sat-industriales.pe">operaciones@sat-industriales.pe</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </th>
                <th>
                    @if ($mainClient && $mainClient->logo && file_exists(public_path('storage/' . $mainClient->logo)))
                        <img src="{{ public_path('storage/' . $mainClient->logo) }}" alt="Logo Cliente" class="header-logo">
                    @else
                        <div class="cliente-nombre">
                            {{ $mainClient->business_name ?? 'Cliente' }}
                        </div>
                    @endif
                </th>
            </tr>
        </thead>
    </table>

    {{-- BARRA DE TÍTULO --}}
    <table class="info-table">
        <thead>
            <tr>
                <th class="info-table-header-col">
                    <div class="info-cell-gris">
                        Generado: {{ $generatedAt->format('d/m/Y H:i') }}
                    </div>
                </th>
                <th class="info-table-header-col">
                    <h3>Informe de Evidencias de Visita</h3>
                </th>
                <th class="info-table-header-col">
                    <div class="info-cell-gris">
                        RV-{{ $visitReport->id ?? 'N/A' }}
                    </div>
                </th>
            </tr>
        </thead>
    </table>

    {{-- INFORMACIÓN DEL CLIENTE --}}
    @if($mainClient)
        <table class="basic-info-table">
            <tbody>
                <tr>
                    <th style="width: 150px;">Cliente</th>
                    <td style="width: 350px;">{{ $mainClient->business_name ?? 'N/A' }}</td>
                    <th style="width: 150px;">RUC/DNI</th>
                    <td colspan="2">{{ $mainClient->document_number ?? 'N/A' }}</td>
                </tr>
                @if($mainSubClient)
                    <tr>
                        <th style="width: 150px;">Sede/Tienda</th>
                        <td style="width: 350px;">{{ $mainSubClient->name ?? 'N/A' }}</td>
                        <th style="width: 150px;">Dirección</th>
                        <td colspan="2">{{ $mainSubClient->address ?? 'N/A' }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    {{-- INFORMACIÓN DEL REPORTE DE VISITA --}}
    <table class="basic-info-table">
        <tbody>
            <tr>
                <th style="width: 150px;">Proyecto</th>
                <td colspan="3">{{ $project->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th style="width: 150px;">Nombre del Reporte</th>
                <td colspan="3">{{ $visitReport->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th style="width: 150px;">Supervisor</th>
                <td style="width: 350px;">
                    {{ $employee ? $employee->first_name . ' ' . $employee->last_name : 'N/A' }}
                </td>
                <th style="width: 150px;">Fecha</th>
                <td>
                    {{ $visitReport->report_date ? \Carbon\Carbon::parse($visitReport->report_date)->format('d/m/Y') : 'N/A' }}
                </td>
            </tr>
            <tr>
                <th style="width: 150px;">Hora Inicio</th>
                <td style="width: 350px;">
                    {{ $visitReport->start_time ? \Carbon\Carbon::parse($visitReport->start_time)->format('H:i') : 'N/A' }}
                </td>
                <th style="width: 150px;">Hora Fin</th>
                <td>
                    {{ $visitReport->end_time ? \Carbon\Carbon::parse($visitReport->end_time)->format('H:i') : 'N/A' }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- RESUMEN --}}
    <table class="basic-info-table">
        <tbody>
            <tr>
                <th style="width: 150px;">Total de Evidencias</th>
                <td>{{ $visitPhotos->count() }}</td>
            </tr>
        </tbody>
    </table>

    {{-- TRABAJOS A REALIZAR --}}
    @if($visitReport->work_to_do)
        <table class="basic-info-text">
            <thead>
                <tr>
                    <th>Trabajos a Realizar</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{!! $visitReport->work_to_do !!}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- CONCLUSIONES --}}
    @if($visitReport->conclusions)
        <table class="basic-info-text">
            <thead>
                <tr>
                    <th>Conclusiones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{!! $visitReport->conclusions !!}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- RECOMENDACIONES --}}
    @if($visitReport->suggestions)
        <table class="basic-info-text">
            <thead>
                <tr>
                    <th>Recomendaciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{!! $visitReport->suggestions !!}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- EVIDENCIAS FOTOGRÁFICAS DE LA VISITA --}}
    @if($visitPhotos->count() > 0)
        @foreach ($visitPhotos as $photoIndex => $photo)
            <table class="evidence-table">
                <thead>
                    <tr>
                        <th class="evidence-th" colspan="2">Evidencia Fotográfica #{{ $loop->iteration }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="evidence-td" colspan="2">
                            <div class="evidence-img-container">
                                @php
                                    $imagePath = $photo->photo_path
                                        ? public_path('storage/' . $photo->photo_path)
                                        : null;
                                    $defaultImg = public_path('images/image-no-found.png');
                                @endphp

                                @if ($imagePath && file_exists($imagePath))
                                    <img class="photo-image" src="{{ $imagePath }}" alt="Evidencia {{ $loop->iteration }}">
                                @else
                                    <img class="photo-image" src="{{ $defaultImg }}" alt="Sin imagen disponible">
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="evidence-desc" colspan="2">
                            {!! $photo->description ?? 'Sin descripción' !!}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    @else
        <table class="basic-info-text">
            <thead>
                <tr>
                    <th>Evidencias Fotográficas</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>No hay evidencias fotográficas para este reporte de visita.</td>
                </tr>
            </tbody>
        </table>
    @endif

    <div>
        <p class="footer">SAT INDUSTRIALES - Monitor</p>
    </div>

    <style>
        /* Estilo para que las columnas de la tabla de cabecera tengan el mismo ancho y texto centrado */
        .info-table-header-col {
            width: 33.33%;
            text-align: center !important;
        }

        body {
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif !important;
            font-size: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            padding: 8px;
        }

        .header-logo {
            margin: 0 auto;
            display: block;
            width: 180px;
            height: auto;
        }

        .empresa-info {
            text-align: center;
        }

        .empresa-info h1 {
            margin-bottom: 6px;
        }

        .empresa-info p,
        .direccion {
            margin: 2px 40px 0px 40px;
            padding: 0 10px;
            font-weight: normal;
            font-size: 15px;
            text-align: center;
        }

        .empresa-info table {
            margin-left: auto;
            margin-right: auto;
        }

        .info-cell-gris {
            padding: 8px 18px;
            background-color: #fff;
            border: 3px solid #e2e2e2;
            border-radius: 20px;
            font-size: 15px;
            font-weight: normal;
        }

        .empresa-info td,
        .empresa-info th,
        .info-table td.info-cell,
        .info-table th.info-cell {
            text-align: center;
            font-weight: normal;
            font-size: 15px;
        }

        .info-table {
            width: 100%;
            margin-left: auto;
            margin-top: 4px !important;
            margin-right: auto;
            border-collapse: collapse;
        }

        .info-table td.info-cell {
            width: 33.33%;
            padding: 8px;
        }

        .cliente-nombre {
            text-align: center;
            font-weight: bold;
            font-size: 15px;
            padding: 20px;
        }

        .photo-image {
            display: block;
            margin: 0 auto;
            max-width: 90%;
            max-height: 450px;
            object-fit: contain;
        }

        /* Estilos para la tabla de evidencias fotográficas */
        .evidence-table {
            width: 100%;
            page-break-inside: avoid;
        }

        .evidence-th,
        .evidence-td {
            width: 50%;
            text-align: center;
            border: 1px solid #ddd;
        }

        .evidence-th {
            background-color: #f2f2f2;
        }

        .evidence-desc {
            text-align: center;
            padding: 8px;
            border: 1px solid #ddd;
            font-style: italic;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 13px;
            border-top: 1px solid #bbb;
            padding-top: 24px;
        }

        .basic-info-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #bbb;
            overflow: hidden;
        }

        .basic-info-table th,
        .basic-info-table td {
            border: 1px solid #ddd;
            padding: 10px 8px;
        }

        .basic-info-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .basic-info-table tr:last-child th,
        .basic-info-table tr:last-child td {
            border-bottom: none;
        }

        .basic-info-text {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cccccc;
            margin-bottom: 20px;
        }

        .basic-info-text th,
        .basic-info-text td {
            border: 1px solid #cccccc;
            padding: 8px;
        }

        .basic-info-text th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .header-table {
            margin-bottom: 0px;
        }

        .no-data {
            color: #999;
            font-style: italic;
        }
    </style>
</body>

</html>