<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Untitled Document</title>
    <style>
        body {
            font-family: "roboto-regular", sans-serif;
            font-weight: initial;
            font-size: 0.9rem;
            overflow-x: hidden;
            overflow-y: auto;
            margin: 1.8cm 0.5cm 3.3cm 0.5cm;
        }

        header {
            position: fixed;
            top: 0.2cm;
            left: 0.5cm;
            right: 0.5cm;
            height: 40cm;
        }

        /* Pie de Página (en la parte inferior) */
        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            text-align: center;
            border-top: 1px solid #ccc;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
            font-size: 11px;
        }

        footer table {
            margin: auto;
            width: 95%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Asegura un ancho uniforme para las columnas */
        }

        footer th,
        footer td {
            text-align: center;
        }

        @media print {
            header::after {
                content: counter(page) " de " counter(pages);
                position: absolute;
                right: 0;
                /* Alinea a la derecha */
                top: 2cm;
                font-size: 12px;
                font-weight: normal;
                padding-right: 10px;
                /* Espacio desde el borde derecho */
                background-color: #FFFFA4;
                /* Fondo amarillo */
                border: 0.5px solid black;
                /* Borde negro */
                border-color: #F5FC83;
                padding: 3px 10px;
                /* Espaciado interno */
                border-radius: 4px;
                /* Bordes redondeados */
            }

            /* Usamos counter(pages) para mostrar el número total de páginas */
            @page {
                counter-increment: page;
            }
        }
    </style>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px;">
    <!-- Cabecera -->
    <header>
        <table style="width: 100%;">
            <tr>
                <td colspan="25" class="tamanio-7" style="width:50%;">
                    @if ($data['LogoOficial'])
                        <img src="data:image/png;base64,{{ $data['LogoOficial'] }}" width="100%" height="2.6%">
                    @endif
                </td>
                <td colspan="25" style="text-align: right;" class="tamanio-7"></td>
            </tr>
        </table>
    </header>

    <!-- Pie de página -->
    <footer style="font-size: 11px !important; ">
        <table style="width:100%;" class="tabla-sin-bordes">
            <tr>
                <td colspan="50" class="tamanio-5" style="text-align:left;">
                    Usuario: {{ $data['Usuario'] }}<br> 
                    Hostname: {{ $data['Terminal'] }} <br>
                    Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y') }} - Hora: {{ \Carbon\Carbon::now()->format('h:i A') }}
                </td>
            </tr>
        </table>
    </footer>

    <main>

        <table width="100%">
            <tr>
                <td style="height: 10cm; vertical-align: top;">
                    <table style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                        <tr>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="50" style="text-align: center" class="tamanio-13  negrita mayuscula">FORMATO
                                DE PRESCRIPCIÓN Y DISPENSACIÓN - RECETA - PACIENTE</td>
                        </tr>
                        <tr>
                            <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
                        </tr>
                        <tr class="tabla-sin-bordes">
                            <td colspan="4" class="tamanio-7 mayuscula">Paciente</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="14" class="tamanio-7 mayuscula">{{ $data['Pacientes'] }}</td>
                            <td colspan="3" class="tamanio-7 mayuscula">Nº Doc</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="12" class="tamanio-7 mayuscula">{{ $data['DocPaciente'] }}</td>
                            <td colspan="5" class="tamanio-7 mayuscula">Cuenta</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="9" class="tamanio-7 mayuscula">{{ $data['Cuenta'] }}</td>
                        </tr>
                        <tr class="tabla-sin-bordes">
                            <td colspan="4" class="tamanio-7 mayuscula">Servicio</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="14" class="tamanio-7 mayuscula">{{ $data['Consultorio'] }}</td>
                            <td colspan="3" class="tamanio-7 mayuscula">Nº HC</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="12" class="tamanio-7 mayuscula">{{ $data['HC'] }}</td>
                            <td colspan="5" class="tamanio-7 mayuscula">Financidor</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="9" class="tamanio-7 mayuscula">{{ $data['Financiador'] }}</td>
                        </tr>
                        <tr class="tabla-sin-bordes">
                            <td colspan="4" class="tamanio-7 mayuscula">Edad</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="14" class="tamanio-7 mayuscula">{{ $data['Edad'] }}</td>
                            <td colspan="3" class="tamanio-7 mayuscula">Sexo</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="27" class="tamanio-7 mayuscula">{{ $data['Sexo'] }}</td>
                        </tr>
                        <br>
                        <tr>
                            <td colspan="2" style="border: 1px solid Black; background-color:#efefef "
                                class="tamanio-6 mayuscula centrado negrita"> ITEM </td>
                            <td colspan="20" style="border: 1px solid Black; background-color:#efefef "
                                class="tamanio-6 mayuscula centrado negrita">
                                MEDICAMENTO O INSUMO - CONCENTRACION - PRESENTACIÓN </td>
                            <td colspan="5" style="border: 1px solid Black; background-color:#efefef "
                                class="tamanio-6 mayuscula centrado negrita">
                                PRESENTACIÓN </td>
                            <td colspan="19" style="border: 1px solid Black; background-color:#efefef "
                                class="tamanio-6 mayuscula centrado negrita"> INDICACIONES </td>
                            <td colspan="5" style="border: 1px solid Black; background-color:#efefef "
                                class="tamanio-6 mayuscula centrado negrita">CANTIDAD </td>
                        </tr>
                        @foreach ($data['ItemRecetas'] as $item)
                            <tr style="border: 1px solid Black">
                                <td colspan="2" style="border: 1px solid Black; background-color:#efefef;"
                                    class="tamanio-6 mayuscula centrado">{{ $item['Item'] }}</td>
                                <td colspan="20" style="border: 1px solid Black" class="tamanio-6 mayuscula">
                                    {{ $item['Medicamento'] }}</td>
                                <td colspan="5" style="border: 1px solid Black"
                                    class="tamanio-6 mayuscula centrado">
                                    {{ $item['Unidad'] }}</td>
                                <td colspan="19" style="border: 1px solid Black" class="tamanio-6 mayuscula">
                                    {{ $item['Indicaciones'] }}</td>
                                <td colspan="5" style="border: 1px solid Black"
                                    class="tamanio-6 mayuscula centrado">
                                    {{ $item['Cantidad'] }}</td>
                            </tr>
                        @endforeach 
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                        <tr>
                            <td colspan="50" class="tamanio-5" style="text-align:left;">
                                Usuario: Luis <br>
                                Hostname: Ltafur <br>
                                Fecha: 21/01/2025 - Hora: 12:20
                            </td>
                        </tr>
                        <tr>
                            <td colspan="50" class="tamanio-7 lineapunteada">&nbsp;</td>
                        </tr>
                        <tr>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                            <td style="width:15.86px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="50" style="text-align: center" class="tamanio-13  negrita mayuscula">
                                FORMATO DE PRESCRIPCIÓN Y DISPENSACIÓN - RECETA - FARMACIA
                            </td>
                        </tr>
                        <tr>
                            <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
                        </tr>
                        <tr class="tabla-sin-bordes">
                            <td colspan="4" class="tamanio-7 mayuscula">Paciente</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="14" class="tamanio-7 mayuscula">{{ $data['Pacientes'] }}</td>
                            <td colspan="3" class="tamanio-7 mayuscula">Nº Doc</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="12" class="tamanio-7 mayuscula">{{ $data['DocPaciente'] }}</td>
                            <td colspan="5" class="tamanio-7 mayuscula">Cuenta</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="9" class="tamanio-7 mayuscula">{{ $data['Cuenta'] }}</td>
                        </tr>
                        <tr class="tabla-sin-bordes">
                            <td colspan="4" class="tamanio-7 mayuscula">Servicio</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="14" class="tamanio-7 mayuscula">{{ $data['Consultorio'] }}</td>
                            <td colspan="3" class="tamanio-7 mayuscula">Nº HC</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="12" class="tamanio-7 mayuscula">{{ $data['HC'] }}</td>
                            <td colspan="5" class="tamanio-7 mayuscula">Financidor</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="9" class="tamanio-7 mayuscula">{{ $data['Financiador'] }}</td>
                        </tr>
                        <tr class="tabla-sin-bordes">
                            <td colspan="4" class="tamanio-7 mayuscula">Edad</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="14" class="tamanio-7 mayuscula">{{ $data['Edad'] }}</td>
                            <td colspan="3" class="tamanio-7 mayuscula">Sexo</td>
                            <td colspan="1" class="tamanio-7 mayuscula">:</td>
                            <td colspan="27" class="tamanio-7 mayuscula">{{ $data['Sexo'] }}</td>
                        </tr>
                        <br>
                        <tr>
                            <td colspan="2" style="border: 1px solid Black; background-color:#efefef "
                                class="tamanio-6 mayuscula centrado negrita"> ITEM </td>
                            <td colspan="20" style="border: 1px solid Black; background-color:#efefef "
                                class="tamanio-6 mayuscula centrado negrita">
                                MEDICAMENTO O INSUMO - CONCENTRACION - PRESENTACIÓN </td>
                            <td colspan="5" style="border: 1px solid Black; background-color:#efefef "
                                class="tamanio-6 mayuscula centrado negrita">
                                PRESENTACIÓN </td>
                            <td colspan="19" style="border: 1px solid Black; background-color:#efefef "
                                class="tamanio-6 mayuscula centrado negrita"> INDICACIONES </td>
                            <td colspan="5" style="border: 1px solid Black; background-color:#efefef "
                                class="tamanio-6 mayuscula centrado negrita">CANTIDAD </td>
                        </tr>

                        @foreach ($data['ItemRecetas'] as $item)
                            <tr style="border: 1px solid Black">
                                <td colspan="2" style="border: 1px solid Black; background-color:#efefef;"
                                    class="tamanio-6 mayuscula centrado">{{ $item['Item'] }}</td>
                                <td colspan="20" style="border: 1px solid Black" class="tamanio-6 mayuscula">
                                    {{ $item['Medicamento'] }}</td>
                                <td colspan="5" style="border: 1px solid Black"
                                    class="tamanio-6 mayuscula centrado">
                                    {{ $item['Unidad'] }}</td>
                                <td colspan="19" style="border: 1px solid Black" class="tamanio-6 mayuscula">
                                    {{ $item['Indicaciones'] }}</td>
                                <td colspan="5" style="border: 1px solid Black"
                                    class="tamanio-6 mayuscula centrado">
                                    {{ $item['Cantidad'] }}</td>
                            </tr>
                        @endforeach 
                        <tr>
                            <td height="78" colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
                            <td colspan="20" style="border-bottom: 1px solid Black;" class="tamanio-7 mayuscula">
                                &nbsp;</td>
                            <td colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
                            <td colspan="20" style="text-align: center;" class="tamanio-7 mayuscula">
                                {{ $data['Pacientes'] }}</td>
                            <td colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="17" class="tamanio-7 mayuscula">&nbsp;</td>
                            <td colspan="16" style="text-align: center;" class="tamanio-7 mayuscula">
                                {{ $data['DocPaciente'] }}
                            </td>
                            <td colspan="17" class="tamanio-7 mayuscula">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="50" class="tamanio-7  negrita mayuscula">&nbsp;</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </main>
</body>

</html>
