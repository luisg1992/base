<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $data['filename'] }}</title>
    <style>
        body {
            margin: 0cm !important;
        }
    </style>
</head>

<body>

    <body>
        <div class="t-block">
            <table width="100%" class="tabla-sin-bordes">
                <tr>
                    <td width="100%" colspan="12" class="t-text-center negrita">{{ $data['Hospital'] }}</td>
                </tr>
                <tr>
                    <td width="100%" class="tamanio-8 negrita" style=" text-align:center;">ADMISIÓN EMERGENCIA</td>
                </tr>
                <tr>
                    <td width="100%" class="tamanio-11 negrita" style=" text-align:center;">{{ $data['TickedAe'] }}
                    </td>
                </tr>
            </table>

            <hr />

            <table width="100%" class="tabla-sin-bordes">
                <tr class="tabla-sin-bordes">
                    <td width="25%" colspan="1" class="tamanio-9 mayuscula negrita">CUENTA</td>
                    <td width="2%" colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td class="tamanio-9 mayuscula negrita">{{ $data['Cuenta'] }}</td>
                </tr>
                <tr>
                    <td colspan="1" class="tamanio-9 mayuscula negrita">FINANC.</td>
                    <td colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td class="tamanio-9 mayuscula negrita">{{ $data['Financiamiento'] }}</td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td width="25%" colspan="1" class="tamanio-9 mayuscula negrita">FECHA</td>
                    <td width="2%" colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td class="tamanio-9 mayuscula negrita">{{ $data['Fecha'] }}</td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td colspan="1" class="tamanio-9 mayuscula negrita">SERVICIO</td>
                    <td colspan="1" class="tamanio-9 mayuscula">:</td>

                    <td class="tamanio-9 mayuscula negrita">{{ $data['Servicio'] }}</td>
                </tr> 

            </table>

            <hr />

            <table width="100%" class="tabla-sin-bordes">
                <tr class="tabla-sin-bordes">
                    <td width="25%" colspan="1" class="tamanio-9 mayuscula negrita">PACIENTE</td>
                    <td width="2%" colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td class="tamanio-9 mayuscula negrita">{{ $data['Paciente'] }}</td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td width="25%" colspan="1" class="tamanio-9 mayuscula negrita">H.C</td>
                    <td width="2%" colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td class="tamanio-9 mayuscula negrita">{{ $data['HistoriaClinica'] }}</td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td colspan="1" class="tamanio-9 mayuscula negrita">DOC.IDE.</td>
                    <td colspan="1" class="tamanio-9 mayuscula">:</td>

                    <td class="tamanio-9 mayuscula negrita">{{ $data['NroDocumento'] }}</td>
                </tr>

            </table>

            <hr />
            <br>
            <table width="100%" class="tabla-sin-bordes" style="text-align: right">
                <tr>
                    <td olspan="3" class="tamanio-7 negrita">
                        Usuario: {{ $data['Usuario'] }}<br>
                        Hostname: {{ $data['Terminal'] }} <br>
                        F.IMP: {{ \Carbon\Carbon::now()->format('d/m/Y') }} <br>
                        H.IMP:{{ \Carbon\Carbon::now()->format('h:i A') }}
                    </td>
                </tr>
            </table>

        </div>
    </body>
</body>

</html>
