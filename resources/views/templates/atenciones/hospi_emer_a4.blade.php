<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $data['filename'] }}</title>
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
            <tr>
                <td colspan="50" style="text-align: center" class="tamanio-13 mayuscula">ORDEN DE HOSPITALIZACIÓN</td>
            </tr>
            <tr>
                <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" style="border: 1px solid Black; background-color:#DBDBDB"
                    class="tamanio-7 mayuscula">
                    Paciente
                </td>
                <td colspan="21" style="border: 1px solid Black; text-align:left" class="tamanio-7 mayuscula">
                    {{ $data['Paciente'] }}</td>
                <td colspan="3" rowspan="2">&nbsp;</td>
                <td colspan="13" style="border: 1px solid Black; background-color:#DBDBDB"
                    class="tamanio-7 mayuscula">Nº
                    Historia
                    Clinica</td>
                <td colspan="8" class="tamanio-7 mayuscula" style="border: 1px solid Black; text-align: left">
                    {{ $data['HistoriaClinica'] }}</td>
            </tr>
            <tr>
                <td colspan="5" style="border: 1px solid Black; background-color:#DBDBDB"
                    class="tamanio-7 mayuscula">
                    Edad</td>
                <td colspan="8" class="tamanio-7 mayuscula" style="border: 1px solid Black; text-align: left">
                    {{ $data['Edad'] }}
                </td>
                <td colspan="5" style="border: 1px solid Black; background-color:#DBDBDB"
                    class="tamanio-7 mayuscula">
                    Sexo</td>
                <td colspan="8" class="tamanio-7 mayuscula" style="border: 1px solid Black; text-align:left">
                    {{ $data['Sexo'] }}
                </td>
                <td colspan="13" style="border: 1px solid Black; background-color:#DBDBDB"
                    class="tamanio-7 mayuscula">
                    Tipo Seguro
                </td>
                <td colspan="8" class="tamanio-7 mayuscula" style="border: 1px solid Black; text-align: left">
                    {{ $data['TipoSeguro'] }}</td>
            </tr>
            <tr>
                <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="11" style="border-top: 1px solid Black; border-left: 1px solid Black;"
                    class="tamanio-7 mayuscula">
                    Fecha de hospitalización :</td>
                <td colspan="12" style="border-top: 1px solid Black; text-align:left;" class="tamanio-7 mayuscula">
                    &nbsp;
                </td>
                <td colspan="9" style="border-top: 1px solid Black;" class="tamanio-7 mayuscula">Hora :</td>
                <td colspan="8" style="border-top: 1px solid Black; text-align:left;" class="tamanio-7 mayuscula">
                    &nbsp;
                </td>
                <td colspan="5" style="border-top: 1px solid Black;" class="tamanio-7 mayuscula">Nº Cama :</td>
                <td colspan="5"
                    style="border-top: 1px solid Black; border-right: 1px solid Black; text-align:left;"
                    class="tamanio-7 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4" style="border-left: 1px solid Black;" class="tamanio-7 mayuscula">Servicio :</td>
                <td colspan="19" class="tamanio-7 mayuscula negrita" style="text-align:left;">
                    {{ $data['Servicio'] }}</td>
                <td colspan="9" class="tamanio-7 mayuscula">Grupo sanguineo:</td>
                <td colspan="8" class="tamanio-7 mayuscula" style="text-align:left;">{{ $data['GrupoS'] }}</td>
                <td colspan="5" class="tamanio-7 mayuscula">Factor Rh:</td>
                <td colspan="5" style="border-right: 1px solid Black; text-align:left;"
                    class="tamanio-7 mayuscula">
                    {{ $data['FactorRh'] }}</td>
            </tr>
            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">&nbsp;
                </td>
            </tr>
            <tr>
                <td style="border-left: 1px solid Black;" class="tamanio-7 mayuscula">&nbsp;</td>
                <td colspan="9" style="border: 1px solid Black; text-align:center; background-color:#DBDBDB"
                    class="tamanio-7 mayuscula">Diagnostico</td>
                <td colspan="39" style="border: 1px solid Black; text-align:center; background-color:#DBDBDB"
                    class="tamanio-7 mayuscula">Descripcion</td>
                <td style="border-right: 1px solid Black;" class="tamanio-7 mayuscula">&nbsp;</td>
            </tr>

            @if ($data['ItemDiagnosticos'])
                @foreach ($data['ItemDiagnosticos'] as $item)
                    <tr>
                        <td style="border-left: 1px solid Black;" class="tamanio-7 mayuscula">&nbsp;</td>
                        <td colspan="9" style="border: 1px solid Black; text-align:center;"
                            class="tamanio-7 mayuscula">
                            {{ $item['CodigoDx'] }}
                        </td>
                        <td colspan="39" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $item['DescripcionDx'] }}
                        </td>
                        <td style="border-right: 1px solid Black;" class="tamanio-7 mayuscula">&nbsp;</td>
                    </tr>
                @endforeach
            @endif


            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">&nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">
                    <b>Información complementaria al diagnóstico :</b>
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">
                    {{ $data['InfoCompDiagnostico'] }}</td>
            </tr>
            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">&nbsp;
                </td>
            </tr>

            
            @if ($data['ItemProcedimientos'])
                <tr>
                    <td style="border-left: 1px solid Black;" class="tamanio-7 mayuscula">&nbsp;</td>
                    <td colspan="48" style="border: 1px solid Black; text-align:center; background-color:#DBDBDB"
                        class="tamanio-7 mayuscula">DESCRIPCIÓN - PROCEDIMIENTO</td>
                    <td style="border-right: 1px solid Black;" class="tamanio-7 mayuscula">&nbsp;</td>
                </tr>

                @foreach ($data['ItemProcedimientos'] as $item)
                    <tr>
                        <td style="border-left: 1px solid Black;" class="tamanio-7 mayuscula">&nbsp;</td>
                        <td colspan="48" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $item['Procedimiento'] }}
                        </td>
                        <td style="border-right: 1px solid Black;" class="tamanio-7 mayuscula">&nbsp;</td>
                    </tr>
                @endforeach
            @endif


            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">&nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">
                    <b>Información complementaria al procedimiento :</b>
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula"> {{ $data['InfoCompProcedimiento'] }}
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">&nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">
                    <b>Información complementaria al destino de atención :</b>
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">
                    {{ $data['Indicaciones'] }}</td>
            </tr>
            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">&nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border-left: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">
                    <b>Hospitalización indicada por :</b>
                </td>
            </tr>
            <tr>
                <td colspan="50" class="tamanio-7 mayuscula"
                    style="border-left: 1px solid Black; border-right: 1px solid Black;">&nbsp;
                </td>
            </tr>
            <tr>
                <td height="78" colspan="15" class="tamanio-7 mayuscula" style="border-left: 1px solid Black;">
                    &nbsp;
                </td>
                <td colspan="20" style="border-bottom: 1px solid Black;" class="tamanio-7 mayuscula">&nbsp;</td>
                <td colspan="15" class="tamanio-7 mayuscula" style="border-right: 1px solid Black;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="15" class="tamanio-7 mayuscula" style="border-left: 1px solid Black;">&nbsp;</td>
                <td colspan="20" style="text-align: center;" class="tamanio-7 mayuscula">{{ $data['Medico'] }}
                </td>
                <td colspan="15" class="tamanio-7 mayuscula" style="border-right: 1px solid Black;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="17" class="tamanio-7 mayuscula" style="border-left: 1px solid Black;">&nbsp;</td>
                <td colspan="16" style="text-align: center;" class="tamanio-7 mayuscula">{{ $data['DocIdeM'] }} -
                    RNE:
                    {{ $data['RNE'] }} - COLEGIATURA: {{ $data['Colegiatura'] }}</td>
                <td colspan="17" class="tamanio-7 mayuscula" style="border-right: 1px solid Black;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="50"
                    style="border-left: 1px solid Black; border-bottom: 1px solid Black; border-right: 1px solid Black;"
                    class="tamanio-7 mayuscula">&nbsp;</td>
            </tr>
        </table>
    </main>
</body>

</html>
