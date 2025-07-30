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
                    Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y') }} - Hora:
                    {{ \Carbon\Carbon::now()->format('h:i A') }}
                </td>
            </tr>
        </table>
    </footer>

    <main>

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
            <tr class="tabla-sin-bordes">
                <td height="33" colspan="50" class="tamanio-13 mayuscula" style="text-align: center;">HOJA DE
                    INTERCONSULTAS</td>
            </tr>
            <tr class="tabla-sin-bordes">
                <td colspan="50" class="tamanio-4 mayuscula" style="text-align: center;">RUC : 20160388570</td>
            </tr>
            <tr class="tabla-sin-bordes">
                <td colspan="50" class="tamanio-4 mayuscula" style="text-align: center;">Av. Miguel Grau 13, Lima
                    15003</td>
            </tr>
            <tr class="tabla-sin-bordes">
                <td colspan="50" class="tamanio-4 mayuscula" style="text-align: center;">981127991</td>
            </tr>
            <tr>
                <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
            </tr>
            <tr class="tabla-sin-bordes">
                <td colspan="4" class="tamanio-7 mayuscula">Paciente</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="21" class="tamanio-7 mayuscula">{{ $data['Pacientes'] }}</td>
                <td colspan="3" class="tamanio-7 mayuscula">Nº Doc</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="8" class="tamanio-7 mayuscula">{{ $data['DocPaciente'] }}</td>
                <td colspan="5" class="tamanio-7 mayuscula">Cuenta</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="5" class="tamanio-7 mayuscula">{{ $data['Cuenta'] }}</td>
            </tr>
            <tr class="tabla-sin-bordes">
                <td colspan="4" class="tamanio-7 mayuscula">Servicio</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="21" class="tamanio-7 mayuscula">{{ $data['Consultorio'] }}</td>
                <td colspan="3" class="tamanio-7 mayuscula">Nº HC</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="8" class="tamanio-7 mayuscula">{{ $data['HC'] }}</td>
                <td colspan="5" class="tamanio-7 mayuscula">Financidor</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="5" class="tamanio-7 mayuscula">{{ $data['Financiador'] }}</td>
            </tr>
            <tr class="tabla-sin-bordes">
                <td colspan="4" class="tamanio-7 mayuscula">Edad</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="21" class="tamanio-7 mayuscula">{{ $data['Edad'] }}</td>
                <td colspan="3" class="tamanio-7 mayuscula">Sexo</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="18" class="tamanio-7 mayuscula">{{ $data['Sexo'] }}</td>
            </tr>

            @if ($data['ItemInterconsultas'])
                @foreach ($data['ItemInterconsultas'] as $item)
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula">
                            <hr style="border: 0.1px solid #999; width: 100%; ">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="tamanio-7 mayuscula">Resumen</td>
                        <td colspan="1" class="tamanio-7 mayuscula">:</td>
                        <td colspan="27" class="tamanio-7 mayuscula">{{ $item['Resumen'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="tamanio-7 mayuscula">Motivo</td>
                        <td colspan="1" class="tamanio-7 mayuscula">:</td>
                        <td colspan="27" class="tamanio-7 mayuscula">{{ $item['Motivo'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="tamanio-7 mayuscula">Diagnostico</td>
                        <td colspan="1" class="tamanio-7 mayuscula">:</td>
                        <td colspan="27" class="tamanio-7 mayuscula">{{ $item['Diagnostico'] }}</td>
                    </tr>
                    <tr class="tabla-sin-bordes">
                        <td colspan="6" class="tamanio-7 mayuscula">Especialidad</td>
                        <td colspan="1" class="tamanio-7 mayuscula">:</td>
                        <td colspan="27" class="tamanio-7 mayuscula">{{ $item['Especialidad'] }}</td>
                        <td colspan="8" class="tamanio-7 mayuscula">Fecha propuesta</td>
                        <td colspan="1" class="tamanio-7 mayuscula">:</td>
                        <td colspan="27" class="tamanio-7 mayuscula">{{ $item['Fecha'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="tamanio-7 mayuscula">Cita </td>
                        <td colspan="1" class="tamanio-7 mayuscula">:</td>
                        <td colspan="27" class="tamanio-7 mayuscula">{{ $item['DetalleCita'] }}</td>
                    </tr>
                @endforeach
            @endif

            <tr>
                <td height="78" colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
                <td colspan="20" style="border-bottom: 1px solid Black;" class="tamanio-7 mayuscula">&nbsp;</td>
                <td colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
                <td colspan="20" style="text-align: center;" class="tamanio-7 mayuscula">{{ $data['Medico'] }}
                </td>
                <td colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="17" class="tamanio-7 mayuscula">&nbsp;</td>
                <td colspan="16" style="text-align: center;" class="tamanio-7 mayuscula">{{ $data['DocIdeM'] }} -
                    RNE: {{ $data['RNE'] }} - COLEGIATURA: {{ $data['Colegiatura'] }}</td>
                <td colspan="17" class="tamanio-7 mayuscula">&nbsp;</td>
            </tr>
        </table>
    </main>
</body>

</html>
