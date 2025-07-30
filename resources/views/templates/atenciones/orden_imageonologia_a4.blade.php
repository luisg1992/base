<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $data['filename'] }}</title>
</head>


<body>
    <table width="100%" class="tabla-sin-bordes">
        <tr>
            <td colspan="25">
                @if ($data['LogoOficial'])
                    <img src="data:image/png;base64,{{ $data['LogoOficial'] }}" width="100%" height="4%">
                @endif
            </td>
            <td colspan="25" style="text-align: right;"> </td>
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
            <td colspan="16"> </td>
            <td colspan="18" style="text-align: center" class="tamanio-13 mayuscula">ORDEN MÉDICA IMAGENOLOGÍA Nº
                1327150
            </td>
            <td colspan="16" style="text-align: right"> </td>
        </tr>
        <tr>
            <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
        </tr>
        <tr class="tabla-sin-bordes">
            <td colspan="4" class="tamanio-7 mayuscula">Paciente</td>
            <td colspan="1" class="tamanio-7 mayuscula">:</td>
            <td colspan="23" class="tamanio-7 mayuscula">{{ $data['Pacientes'] }}</td>
            <td colspan="3" class="tamanio-7 mayuscula">Nº Doc</td>
            <td colspan="1" class="tamanio-7 mayuscula">:</td>
            <td colspan="8" class="tamanio-7 mayuscula">{{ $data['DocPaciente'] }}</td>
            <td colspan="5" class="tamanio-7 mayuscula">Cuenta</td>
            <td colspan="1" class="tamanio-7 mayuscula">:</td>
            <td colspan="4" class="tamanio-7 mayuscula">{{ $data['Cuenta'] }}</td>
        </tr>
        <tr class="tabla-sin-bordes">
            <td colspan="4" class="tamanio-7 mayuscula">Servicio</td>
            <td colspan="1" class="tamanio-7 mayuscula">:</td>
            <td colspan="23" class="tamanio-7 mayuscula">{{ $data['Consultorio'] }}</td>
            <td colspan="3" class="tamanio-7 mayuscula">Nº HC</td>
            <td colspan="1" class="tamanio-7 mayuscula">:</td>
            <td colspan="8" class="tamanio-7 mayuscula">{{ $data['HC'] }}</td>
            <td colspan="5" class="tamanio-7 mayuscula">Financidor</td>
            <td colspan="1" class="tamanio-7 mayuscula">:</td>
            <td colspan="4" class="tamanio-7 mayuscula">{{ $data['Financiador'] }}</td>
        </tr>
        <tr class="tabla-sin-bordes">
            <td colspan="4" class="tamanio-7 mayuscula">Edad</td>
            <td colspan="1" class="tamanio-7 mayuscula">:</td>
            <td colspan="23" class="tamanio-7 mayuscula">{{ $data['Edad'] }}</td>
            <td colspan="3" class="tamanio-7 mayuscula">Sexo</td>
            <td colspan="1" class="tamanio-7 mayuscula">:</td>
            <td colspan="18" class="tamanio-7 mayuscula">{{ $data['Sexo'] }}</td>
        </tr>
        <tr>
            <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
        </tr>



        @if ($data['ItemOrdenesImagenes'])
            <tr>
                <td colspan="2" style="border: 1px solid Black; background-color:#efefef "
                    class="tamanio-6 mayuscula  negrita centrado"> ITEM </td>
                <td colspan="4" style="border: 1px solid Black; background-color:#DBDBDB"
                    class="tamanio-7 mayuscula centrado negrita">CODIGO</td>
                <td colspan="19" style="border: 1px solid Black; background-color:#DBDBDB"
                    class="tamanio-7 mayuscula centrado negrita">DESCRIPCIÓN CPT</td>
                <td colspan="3" style="border: 1px solid Black; background-color:#DBDBDB"
                    class="tamanio-7 mayuscula centrado negrita">CANT</td>
                <td colspan="22" style="border: 1px solid Black; background-color:#DBDBDB"
                    class="tamanio-7 mayuscula centrado negrita">JUSTIFICACIÓN</td>
            </tr>
            @foreach ($data['ItemOrdenesImagenes'] as $item)
                <tr>
                    <td colspan="2" style="border: 1px solid Black; background-color:#efefef;"
                        class="tamanio-6 mayuscula centrado">
                        {{ $item['Item'] }}
                    </td>
                    <td colspan="4" style="border: 1px solid Black;"
                        class="tamanio-7 mayuscula centrado">
                        {{ $item['Codigo'] }}
                    </td>
                    <td colspan="19" style="border: 1px solid Black; text-align: left;"
                        class="tamanio-7 mayuscula">
                        {{ $item['Medicamento'] }}
                    </td>
                    <td colspan="3" style="border: 1px solid Black; text-align: center;"
                        class="tamanio-7 mayuscula">
                        {{ $item['Cantidad'] }}
                    </td>
                    <td colspan="22" style="border: 1px solid Black; text-align: center; text-align: left;"
                        class="tamanio-7 mayuscula">
                        {{ $item['Justificacion'] }}
                    </td>
                </tr>
            @endforeach
        @endif




        <tr>
            <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="50" class="tamanio-7 mayuscula">RESUMEN HC :</td>
        </tr>
        <tr>
            <td colspan="50" class="tamanio-7 mayuscula">
                {{-- {{ $item['Observacion'] }} --}}
            </td>
        </tr>
        <tr>
            <td height="78" colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
            <td colspan="20" style="border-bottom: 1px solid Black;" class="tamanio-7 mayuscula">&nbsp;</td>
            <td colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
            <td colspan="20" style="text-align: center;" class="tamanio-7 mayuscula">{{ $data['Medico'] }}</td>
            <td colspan="15" class="tamanio-7 mayuscula">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="17" class="tamanio-7 mayuscula">&nbsp;</td>
            <td colspan="16" style="text-align: center;" class="tamanio-7 mayuscula">{{ $data['DocIdeM'] }} - RNE:
                {{ $data['RNE'] }} - COLEGIATURA: {{ $data['Colegiatura'] }}</td>
            <td colspan="17" class="tamanio-7 mayuscula">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
        </tr>
    </table>
</body>


</html>
