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
            <td colspan="6" class="t-text-center negrita">CITAS PROGRAMADAS</td>
        </tr>
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>
        <tr class="tamanio-12">
            <td colspan="6" class="t-text-center">{{ $data['Hospital'] }}</td>
        </tr>
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>
        <tr>
            <td class="tamanio-9">SERVICIO</td>
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
            <td class="tamanio-11 negrita" colspan="4">{{ $data['NroHistoriaClinica'] }}</td> 
        </tr>
        <tr>
            <td class="tamanio-9">DOCUMENTO</td>
            <td>:</td>
            <td class="tamanio-11 negrita" colspan="4">{{ $data['Documento'] }}</td>
        </tr>
        <tr>
            <td class="tamanio-9">FINANCI.</td>
            <td>:</td>
            <td class="tamanio-11 negrita" colspan="4">{{ $data['Financiamiento'] }}</td>
        </tr> 
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>
        <tr class="tamanio-12">
            <td colspan="6" class="negrita">CITAS</td>
        </tr>

        @if ($data['Citas'])
            <tr>
                <td class="tamanio-9" colspan="6">
                    @foreach ($data['Citas'] as $item)
                            {{ $item['Item'] }}. {{ $item['Fecha'] }}-{{ $item['Hora'] }} < > {{ $item['IdCuentaAtencion'] }} <br>
                    @endforeach
                </td>
            </tr>
        @endif
        <tr>
            <td colspan="6">
                <hr>
            </td>
        </tr>
        <tr>
            <td class="tamanio-9">F.IMP</td>
            <td>:</td>
            <td class="tamanio-10">{{ $data['FechaImpresion'] }} </td>
            <td class="tamanio-9"> HR</td>
            <td>:</td>
            <td class="tamanio-10">{{ $data['HoraImpresion'] }}</td>
        </tr>
        <tr>
            <td class="tamanio-9">USUARIO</td>
            <td>:</td>
            <td class="tamanio-9" colspan="4">{{ $data['Usuario'] }}</td>
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
