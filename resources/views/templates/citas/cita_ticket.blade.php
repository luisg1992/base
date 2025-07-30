<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $data['filename'] }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <table class="tabla-sin-bordes">
        <tr class="tamanio-12">
            <td colspan="6" class="t-text-center">{{ $data['Hospital'] }}</td>
        </tr>
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>

        @if ($data['EsCitaAdicional'] == 1)
            <tr class="t-text-center">
                <td colspan="6" class="t-text-center">
                    <b class="t-text-center negrita">PACIENTE ADICIONAL</b>
                    <hr>
                </td>
            </tr>
        @endif
        <tr>
            @if ($data['NumCupo'] != 0 && $data['NumCupo'] != null)
                <td class="tamanio-9" style="width: 70px">N° CUPO</td>
                <td>:</td>
            @endif
            <td class="tamanio-10" colspan="4">
                @if ($data['NumCupo'] != 0 && $data['NumCupo'] != null)
                    <span class="negrita">{{ $data['NumCupo'] }}</span>
                @endif
                @if ($data['IdOrdenPago'])
                    -ORD.PAGO: <span class="negrita">{{ $data['IdOrdenPago'] }}</span>
                @endif
            </td>
        </tr>

        <tr>
            <td class="tamanio-9" style="width: 70px">CUENTA</td>
            <td>:</td>
            <td class="tamanio-11 negrita" colspan="4">{{ $data['CuentaAtencion'] }} - {{ $data['Financiamientos'] }}
            </td>
        </tr>
        <tr>
            <td class="tamanio-9">FECHA</td>
            <td style="width: 10px; vertical-align: top">:</td>
            <td class="tamanio-9 negrita">{{ $data['FechaCita'] }}</td>
            @if ($data['EsCitaAdicional'] == 0)
                <td class="tamanio-9">HR</td>
                <td style="width: 10px">:</td>
                <td class="tamanio-9 negrita">{{ $data['HoraCita'] }}</td>
            @else
                @if ($data['Turno'] != null)
                    <td class="tamanio-9 negrita">{{ $data['Turno'] }}</td>
                @endif
            @endif
        </tr>
        <tr>
            <td class="tamanio-9">CONSULT</td>
            <td>:</td>
            <td class="tamanio-9 negrita" colspan="4">{{ $data['Consultorio'] }}</td>
        </tr>
        <tr>
            <td class="tamanio-9 vertical-align-top">MÉDICO</td>
            <td class="vertical-align-top ">:</td>
            <td class="tamanio-9 vertical-align-top" colspan="4">{{ $data['Medico'] }}</td>
        </tr>
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>
        <tr>
            <td class="tamanio-9 vertical-align-top">PACIENTE</td>
            <td class="vertical-align-top">:</td>
            <td class="tamanio-9 vertical-align-top" colspan="4">{{ $data['Paciente'] }}</td>
        </tr>
        <tr>
            <td class="tamanio-9">N° HC</td>
            <td>:</td>
            <td class="tamanio-9" colspan="4">{{ $data['NroHistoriaClinica'] }}</td>
        </tr>
        <tr>
            <td class="tamanio-9">DOCUMENTO</td>
            <td>:</td>
            <td class="tamanio-11 negrita" colspan="4">{{ $data['Documento'] }}</td>
        </tr>
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>
        <tr>
            <td class="tamanio-9">GENERA</td>
            <td>:</td>
            <td class="tamanio-9" colspan="4">{{ $data['Genera'] }}</td>
        </tr>
        <tr>
            <td class="tamanio-9">IMPRIME</td>
            <td>:</td>
            <td class="tamanio-9" colspan="4">{{ $data['Imprime'] }}</td>
        </tr>
        <tr>
            <td class="tamanio-9">TERMINAL</td>
            <td>:</td>
            <td class="tamanio-9" colspan="4">{{ $data['Terminal'] }}</td>
        </tr>
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>
        <tr>
            <td class="tamanio-9 t-text-center" colspan="6">{{ $data['Mensaje'] }}</td>
        </tr>
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>
    </table>
</body>

</html>
