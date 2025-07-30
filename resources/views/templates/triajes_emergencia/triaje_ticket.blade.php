<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $data['filename'] }}</title>
</head>

<body>

    <body>
        <div class="t-block">
            <table width="100%" class="tabla-sin-bordes">
                <tr>
                    <td width="100%" colspan="12" class="t-text-center negrita">{{ $data['Hospital'] }}</td>
                </tr>
                <tr>
                    <td width="100%" class="tamanio-8 negrita" style=" text-align:center;">{{ $data['ServicioTriaje'] }}
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="tamanio-12 negrita" style=" text-align:center;">{{ $data['Ticked'] }}</td>
                </tr>
            </table>

            <hr />

            <table width="100%" class="tabla-sin-bordes">
                <tr class="tabla-sin-bordes">
                    <td width="25%" colspan="1" class="tamanio-9 mayuscula negrita">FECHA </td>
                    <td width="2%" colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td colspan="6" class="tamanio-12 mayuscula negrita">{{ $data['Fecha'] }}</td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td width="25%" colspan="1" class="tamanio-9 mayuscula negrita">NRO. DOC </td>
                    <td width="2%" colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td colspan="6" class="tamanio-12 mayuscula negrita">{{ $data['NroDocumento'] }}</td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td colspan="1" class="tamanio-9 mayuscula negrita">NRO.HIST </td>
                    <td colspan="1" class="tamanio-9 mayuscula">:</td>

                    <td colspan="6" class="tamanio-12 mayuscula negrita">{{ $data['HistoriaClinica'] }}</td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td colspan="1" class="tamanio-9 mayuscula negrita">PACIENTE </td>
                    <td colspan="1" class="tamanio-9 mayuscula">:</td>

                    <td colspan="6" class="tamanio-9 mayuscula negrita">{{ $data['Paciente'] }}</td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td colspan="1" class="tamanio-9 mayuscula negrita">FEC.NACI </td>
                    <td colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td colspan="6" class="tamanio-9 mayuscula negrita">{{ $data['FechNacimiento'] }}</td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td colspan="1	" class="tamanio-9 mayuscula negrita">SEXO </td>
                    <td colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td width="90%" class="tamanio-9 mayuscula negrita">
                        @if ($data['Sexo'] == 'F')
                            FEMENINO
                        @else
                            MASCULINO
                        @endif
                    </td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td colspan="-1" class="tamanio-9 mayuscula negrita">EDAD </td>
                    <td colspan="-1" class="tamanio-9 mayuscula">:</td>
                    <td class="tamanio-9 mayuscula negrita">{{ $data['Edad'] }}</td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td colspan="-1" class="tamanio-9 mayuscula negrita"><span
                            class="tamanio-12 mayuscula negrita">DESTINO</span></td>
                    <td colspan="-1" class="tamanio-9 mayuscula">:</td>
                    <td class="tamanio-9 mayuscula negrita">{{ $data['Destino'] }}</td>
                </tr>
                <tr class="tabla-sin-bordes">
                    <td colspan="3" style="text-align:center" class="tamanio-12 mayuscula negrita">PRIORIDAD
                        {{ $data['Prioridad'] }}</td>
                </tr>
            </table>

            <hr />

            <table width="100%" class="tabla-sin-bordes">
                <tr class="tabla-sin-bordes">
                    <td width="3%" colspan="1" class="tamanio-9 mayuscula negrita">P.A</td>
                    <td width="1%" colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td width="20%" colspan="1" class="tamanio-10 mayuscula negrita">{{ $data['PA'] }}</td>

                    <td width="3%" colspan="1" class="tamanio-9 mayuscula negrita">FCAR </td>
                    <td width="1%" colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td width="20%" colspan="1" class="tamanio-10 mayuscula negrita">{{ $data['FC'] }}</td>
                </tr>

                <tr class="tabla-sin-bordes">
                    <td width="5%" colspan="1" class="tamanio-9 mayuscula negrita">FR</td>
                    <td width="1%" colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td width="20%" colspan="1" class="tamanio-10 mayuscula negrita">{{ $data['FR'] }}</td>

                    <td width="5%" colspan="1" class="tamanio-9 mayuscula negrita">SATO2 </td>
                    <td width="1%" colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td width="20%" colspan="1" class="tamanio-10 mayuscula negrita">{{ $data['SATo2'] }}</td>
                </tr>

                <tr class="tabla-sin-bordes">
                    <td width="5%" colspan="1" class="tamanio-9 mayuscula negrita">T</td>
                    <td width="1%" colspan="1" class="tamanio-9 mayuscula">:</td>
                    <td width="20%" colspan="1" class="tamanio-10 mayuscula negrita">{{ $data['T'] }}</td>
                </tr>
            </table>
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
