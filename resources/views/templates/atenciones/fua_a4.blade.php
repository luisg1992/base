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
        }

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
                top: 2cm;
                font-size: 12px;
                font-weight: normal;
                padding-right: 10px;
                background-color: #FFFFA4;
                border: 0.5px solid black;
                border-color: #F5FC83;
                padding: 3px 10px;
                border-radius: 4px;
            }

            @page {
                counter-increment: page;
            }
        }
    </style>
</head>

<body style="font-size: 10px;">
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
        <table style="width: 100%; table-layout: fixed; border-collapse: collapse; text-align:center">
            <tr>
                <td colspan="21" style="font-size: 14px;  padding-left: 20px;">&nbsp;</td>
                <td colspan="8" style="font-size: 14px; text-align: center;" class="tamanio-5 mayuscula">ANEXO 1</td>
                <td colspan="21" style="font-size: 14px; text-align: right; padding-right: 20px;"> </td>
            </tr>
            <tr>
                <td colspan="16" rowspan="3" style="border-collapse: collapse;">
                    @if ($data['LogoOficial'])
                        <img src="data:image/png;base64,{{ $data['LogoOficial'] }}" width="100%" height="3.5%">
                    @endif
                </td>
                <td colspan="18" class="tamanio-5  mayuscula "
                    style="border: 1px solid Black; border-collapse: collapse; text-align: center; background-color:#fefefe">
                    FORMATO ÚNICO DE ATENCIÓN - FUA</td>
                <td colspan="16" class="tamanio-5  mayuscula " style="border-collapse: collapse;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="18" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">NÚMERO DE FORMATO</td>
                <td colspan="16" rowspan="2">&nbsp;


                </td>
            </tr>
            <tr>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['A'] }}</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['B'] }}</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['C'] }}</td>
            </tr>
            <tr>
                <td colspan="50"
                    style="border-top: 0px solid Black; border-left: 1px solid Black; border-right: 1px solid Black; border-bottom: 0px solid Black;">
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DE LA INSTITUCIÓN PRESTADORA DE SERVICIOS DE SALUD</td>
            </tr>
            <tr>
                <td colspan="12" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CÓDIGO RENIPRESS DE LA IPRESS</td>
                <td colspan="38" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula "> NOMBRE DE LA IPRESS QUE REALIZA LA ATENCIÓN</td>
            </tr>
            <tr>
                <td colspan="12" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['CodigoRenipress'] }}</td>
                <td colspan="38" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['NombreIpress'] }}
                </td>
            </tr>
            <tr>
                <td colspan="12" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">PERSONAL QUE ATIENDE</td>
                <td colspan="7" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">LUGAR DE ATENCIÓN</td>
                <td colspan="7" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ATENCIÓN</td>
                <td colspan="24" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">REFERENCIA REALIZADA POR</td>
            </tr>
            <tr>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DE LA IPRESS</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['DeLaIpress'] }}
                </td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CÓDIGO DE AISPED</td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">INTRAMURAL</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Intramural'] }}
                </td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">AMBULATORIA</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Ambulatoria'] }}
                </td>
                <td colspan="8" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula "> CÓD. RENIPRESS</td>
                <td colspan="8" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">NOMBRE DE LA IPRESS</td>
                <td colspan="8" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">N° HOJA DE REFERENCIA</td>
            </tr>
            <tr>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ITINERANTE</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Itinerante'] }}
                </td>
                <td colspan="5" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['CodigoAisped'] }}</td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">EXTRAMURAL</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Extramural'] }}</td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">REFERENCIA</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Referencia'] }}</td>
                <td colspan="8" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['CodRenipress'] }}</td>
                <td colspan="8" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['NombreDeIpress'] }}</td>
                <td colspan="8" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['HojaReferencia'] }}</td>
            </tr>
            <tr>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">AISPED</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Aisped'] }}
                </td>
                <td colspan="5" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula "> EMERGENCIA</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Emergencia'] }}</td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DEL ASEGURADO</td>
            </tr>
            <tr>
                <td colspan="11" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">IDENTIFICACIÓN</td>
                <td colspan="14" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CÓDIGO DEL ASEGURADO SIS</td>
                <td colspan="25" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ASEGURADO DE OTRA IAFAS</td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">TDI</td>
                <td colspan="9" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5  mayuscula ">N° DOCUMENTO DE IDENTIDAD</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DIRESA / OTROS</td>
                <td colspan="10" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">NÚMERO</td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">INSTITUCIÓN</td>
                <td colspan="19" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Institucion'] }}</td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['TDI'] }}
                </td>
                <td colspan="9" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['DocumentoIdentidad'] }}</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['DiresaOtros'] }}</td>
                <td colspan="10" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Numero'] }}
                </td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">COD. SEGURO</td>
                <td colspan="19" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['CodSeguro'] }}
                </td>
            </tr>
            <tr>
                <td colspan="25" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">APELLIDO PATERNO</td>
                <td colspan="25" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">APELLIDO MATERNO</td>
            </tr>
            <tr>
                <td colspan="25" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['ApellidoPaterno'] }}</td>
                <td colspan="25" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['ApellidoMaterno'] }}</td>
            </tr>
            <tr>
                <td colspan="25" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">PRIMER NOMBRE</td>
                <td colspan="25" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">OTROS NOMBRES</td>
            </tr>
            <tr>
                <td colspan="25" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['PrimerNombre'] }}</td>
                <td colspan="25" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['OtrosNombres'] }}</td>
            </tr>
            <tr>
                <td colspan="7" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">SEXO</td>
                <td colspan="8" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">FECHA</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DIA</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">MES</td>
                <td colspan="7" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">AÑO</td>
                <td colspan="10" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">N° DE HISTORIA CLÍNICA</td>
                <td colspan="10" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ETNIA</td>
            </tr>
            <tr>
                <td colspan="5" style="border: 1px solid Black; text-align: center;" class="tamanio-5 mayuscula">
                    MASCULINO</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Masculino'] }}
                </td>
                <td colspan="8" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5  mayuscula ">FECHA PROBABLE DE PARTO/FECHA DE PARTO</td>
                <td colspan="4" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['FechaPartoD'] }}</td>
                <td colspan="4" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['FechaPartoM'] }}</td>
                <td colspan="7" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['FechaPartoA'] }}</td>
                <td colspan="10" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['HistoriaClinica'] }}</td>
                <td colspan="10" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Etnia'] }}</td>
            </tr>
            <tr>
                <td colspan="5" style="border: 1px solid Black; text-align: center;" class="tamanio-5 mayuscula">
                    FEMENINO</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Femenino'] }}
                </td>
            </tr>
            <tr>
                <td colspan="7" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">SALUD MATERNA</td>
                <td colspan="8" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">FECHA DE NACIMIENTO</td>
                <td colspan="4" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['FechaNacimientoD'] }}</td>
                <td colspan="4" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['FechaNacimientoM'] }}</td>
                <td colspan="7" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['FechaNacimientoA'] }}</td>
                <td colspan="10" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DNI/CNV/AFILIACIÓN DEL RN 1</td>
                <td colspan="10" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['RN1'] }}
                </td>
            </tr>
            <tr>
                <td colspan="5" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">GESTANTE</td>
                <td colspan="2" rowspan="2" style="border: 1px solid Black">{{ $data['Gestante'] }}</td>
                <td colspan="10" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DNI/CNV/AFILIACIÓN DEL RN 2</td>
                <td colspan="10" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['RN2'] }}
                </td>
            </tr>
            <tr>
                <td colspan="8" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">FECHA DE FALLECIMIENTO</td>
                <td colspan="4" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['FechaFallecimientoD'] }}</td>
                <td colspan="4" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['FechaFallecimientoM'] }}</td>
                <td colspan="7" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['FechaFallecimientoA'] }}</td>
                <td colspan="10" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DNI/CNV/AFILIACIÓN DEL RN 3</td>
                <td colspan="10" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['RN3'] }}
                </td>
            </tr>
            <tr>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">PUERPERA</td>
                <td colspan="2" style="border: 1px solid Black">{{ $data['Puerpera'] }}</td>

            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DE LA ATENCIÓN</td>
            </tr>
            <tr>
                <td colspan="16" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">FECHA DE ATENCIÓN</td>
                <td colspan="5" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">HORA</td>
                <td colspan="3" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">UPS</td>
                <td colspan="3" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5  mayuscula ">CÓD. PRESTA.</td>
                <td colspan="6" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5  mayuscula ">CÓD. PRESTACION (ES) ADICIONAL (ES)</td>
                <td colspan="1" rowspan="5" class="tamanio-5 mayuscula"
                    style="border: 1px solid black; text-align: center; font-size: 6px !important;">
                    <b>
                        H<br />
                        O<br />
                        S<br />
                        P<br />
                        I<br />
                    </b>
                </td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">FECHA</td>
                <td colspan="2" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DIA</td>

                <td colspan="2" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">MES</td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">AÑO</td>
            </tr>
            <tr>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DIA</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">MES</td>
                <td colspan="8" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">AÑO</td>
                <td colspan="6" style="border: 1px solid Black;text-align: center; background-color:#fefefe"
                    class="tamanio-5 mayuscula"> DE INGRESO </td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['fIngresoD'] }}
                </td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['fIngresoM'] }}
                </td>
                <td colspan="6" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['fIngresoA'] }}
                </td>
            </tr>
            <tr>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['DiaAtencion'] }}</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['MesAtencion'] }}</td>
                <td colspan="8" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['AnoAtencion'] }}</td>
                <td colspan="5" class="tamanio-5 mayuscula" style="border: 1px solid Black; text-align: center;">
                    {{ $data['Hora'] }}</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['UPS'] }}
                </td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['CodPrestacional'] }}</td>
                <td colspan="6" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['CodPrestacionAdi'] }}</td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DE ALTA</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['fAltaD'] }}
                </td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['fAltaM'] }}
                </td>
                <td colspan="6" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['fAltaA'] }}
                </td>
            </tr>
            <tr>
                <td colspan="8" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">REPORTE VINCULADO</td>
                <td colspan="13" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CÓD. AUTORIZACIÓN</td>
                <td colspan="12" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">N° FUA A VINCULAR</td>
                <td colspan="6" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5  mayuscula ">DE CORTE ADMINISTRATIVO</td>
                <td colspan="2" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['fCorteAdminisD'] }}</td>
                <td colspan="2" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['fCorteAdminisM'] }}</td>
                <td colspan="6" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['fCorteAdminisA'] }}</td>
            </tr>
            <tr>
                <td colspan="13" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['CodAutorizacion'] }}</td>
                <td colspan="12" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['FuaVincular'] }}</td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CONCEPTO PRESTACIONAL</td>
            </tr>
            <tr>
                <td colspan="6" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ATENCIÓN DIRECTA</td>
                <td colspan="20" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['AtencionDirecta'] }}</td>
                <td colspan="24" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">SEPELIO</td>
            </tr>
            <tr>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">NATIMUERTO</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Natimuerto'] }}</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">OBITO</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Obito'] }}
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">OTRO</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Otro'] }}
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DEL DESTINO DEL ASEGURADO/USUARIO</td>
            </tr>
            <tr>
                <td colspan="2" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ALTA</td>
                <td rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['dAlta'] }}
                </td>
                <td colspan="2" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CITA</td>
                <td rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['dCita'] }}
                </td>
                <td colspan="7" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">HOSPITALIZACIÓN</td>
                <td rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['dHospitalizacion'] }}</td>
                <td colspan="18" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">REFERIDO</td>
                <td colspan="4" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CONTRA REFERIDO</td>
                <td colspan="2" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Contrareferido'] }}</td>
                <td colspan="4" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">FALLECIDO</td>
                <td colspan="2" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Fallecido'] }}</td>
                <td colspan="3" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CORTE ADMINIS.</td>
                <td colspan="3" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['CorteAdministrativo'] }}</td>
            </tr>
            <tr>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">EMERGENCIA</td>
                <td style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['rEmergencia'] }}</td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5  mayuscula ">CONSULTA EXTERNA</td>
                <td style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['rConsultaExterna'] }}</td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">APOYO AL DIAGNÓSTICO</td>
                <td style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['rApoyoDiagnostico'] }}
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">SE REFIERE/CONTRARREFIERE A:</td>
            </tr>
            <tr>
                <td colspan="16" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CÓDIGO RENIPRESS DE LA IPRESS</td>
                <td colspan="18" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">NOMBRE DE LA IPRESS A LA QUE SE REFIERE / CONTRARREFIERE</td>
                <td colspan="16" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">N° HOJA DE REFER / CONTRARR.</td>
            </tr>
            <tr>
                <td height="10" colspan="16" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['rcCodigoRenipress'] }}</td>
                <td colspan="18" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['rcNombreIpress'] }}</td>
                <td colspan="16" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['rcNumeroHoja'] }}</td>
            </tr>
            <tr>
                <td colspan="31" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ACTIVIDADES PREVENTIVAS Y OTROS</td>
                <td colspan="19" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">VACUNAS N° DE DOSIS</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">PESO (Kg)</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Peso'] }}
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">TALLA (cm)</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Talla'] }}
                </td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">P.A. (mmHg)</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['PA'] }}
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">IMC (Kg/m2)</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['IMC'] }}
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">P.AB (cm)</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['PAB'] }}
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">BCG</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">{{ $data['Bcg'] }}
                </td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">INFLUENZA</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['Influenza'] }}
                </td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ANTIAMARILICA</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['Antiamarilica'] }}</td>
            </tr>
            <tr>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DE LA GESTANTE</td>
                <td colspan="7" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DEL RECIEN NACIDO</td>
                <td colspan="12" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">GESTANTE / RN/NIÑO / ADOLESCENTE / JOVEN Y ADULTO / ADULTO MAYOR</td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">JOVEN Y ADULTO</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DPT</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Dpt'] }}
                </td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">PAROTID</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['Parotid'] }}
                </td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ANTINEUMOC</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['Antineumoc'] }}</td>
            </tr>
            <tr>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CPN (N°)</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['CNP'] }}
                </td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">EDAD GEST RN(SEM)</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['EdadGestRn'] }}</td>
                <td colspan="4" rowspan="1"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CRED N°</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">{{ $data['Cred'] }}
                </td>
                <td colspan="6" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">EVALUACIÓN INTEGRAL</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['EvaluacionInteg'] }}</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">APO</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Apo'] }}
                </td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">RUBEOLA</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['Rubeola'] }}
                </td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ANTITETANICA</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['Antitetanica'] }}</td>
            </tr>
            <tr>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">EDAD GEST.</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['EdadGest'] }}
                </td>
                <td colspan="3" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">APGAR</td>
                <td rowspan="2" style="border: 1px solid Black; text-align: center;" class="tamanio-5 mayuscula">
                    1º
                </td>
                <td rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Uno'] }}
                </td>
                <td rowspan="2" style="border: 1px solid Black; text-align: center;" class="tamanio-5 mayuscula">
                    5º
                </td>
                <td rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Cinco'] }}
                </td>
                <td colspan="4" rowspan="1"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">R.N. PREMATURO</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['rnPrematuro'] }}</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">TAP/ EEDP o TEPSI</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['TapEedpTepsi'] }}</td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ADULTO MAYOR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ASA</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Asa'] }}
                </td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ROTAVIRUS</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['Rotavirus'] }}</td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">COMPLETAS PARA LA EDAD (410)</td>
                <td style="border: 1px solid Black; text-align: center;" class="tamanio-5 mayuscula">SI</td>
                <td style="border: 1px solid Black; text-align: center;" class="tamanio-5 mayuscula">NO</td>
            </tr>
            <tr>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ALTURA UTERINA</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['AlturaUterina'] }}</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">BAJO PESO AL NACER</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['BajoPesoAlNacer'] }}</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CONSEJERIA NUTRICIONAL</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['ConsejeriaNutricio'] }}</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">VACAM</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Vacam'] }}
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">SPR</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Spr'] }}
                </td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DT ADULTO (N° DOSIS)</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['DtAdulto'] }}
                </td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">VPH</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">{{ $data['Vph'] }}
                </td>
            </tr>
            <tr>
                <td colspan="4" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">PARTO VERTICAL</td>
                <td colspan="2" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['PartoVertical'] }}</td>
                <td colspan="5" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5  mayuscula ">Corte Tardío de Cordón (2 a 3 min)</td>
                <td colspan="2" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['CorteTardioCordon'] }}</td>
                <td colspan="4" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5 mayuscula ">ENFER. CONGENITA / SECUELA AL NACER</td>
                <td colspan="2" rowspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['EnferCongeSecueNacer'] }}</td>
                <td colspan="4" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5  mayuscula ">CONSEJERIA INTEGRAL</td>
                <td colspan="2" rowspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['ConsejeriaInteg'] }}</td>
                <td colspan="4" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">TAMIZAJE DE SALUD MENTAL</td>
                <td colspan="2" style="border: 1px solid Black; text-align: center;" class="tamanio-5 mayuscula">
                    {{ $data['Pat'] }}</td>
                <td colspan="3" rowspan="1"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">SR</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Sr'] }}
                </td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">IPV</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">{{ $data['Ipv'] }}
                </td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">OTRA VACUNA</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['OtraVacuna'] }}</td>
            </tr>
            <tr>
                <td colspan="2" style="border: 1px solid Black; text-align: center;" class="tamanio-5 mayuscula">
                    {{ $data['Nor'] }}</td>
                <td colspan="3" rowspan="1"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">HVB</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Hvb'] }}
                </td>
                <td colspan="4" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">PENTAVAL</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    {{ $data['Pentaval'] }}
                </td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">________</td>
                <td colspan="2" class="tamanio-5 mayuscula" style="border: 1px solid Black;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="4" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CONTROL PUERP (N°)</td>
                <td colspan="2" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['ControlPuerp'] }}</td>
                <td colspan="25" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">TAMIZAJE DE PATOLOGÍAS CRÓNICAS</td>
                <td colspan="3" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">GRUPO DE RIESGO HVB</td>
                <td colspan="2" rowspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['GrupoRiesgoHvb'] }}</td>
                <td colspan="14" rowspan="2" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-3-5 mayuscula">
                    GRUPO DE RIESGO HVB: 1.TRABAJADOR DE SALUD 2.TRABAJAD.SEXUALES 3.HSH 4.PRIVADO LIBERTAD 5.FF.AA.
                    6.POLICIA
                    NACIONAL 7.ESTUDIANTES DE SALUD 8.POLITRANFUNDIDOS 9.DROGO DEPENDIENTES</td>
            </tr>
            <tr>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5  mayuscula ">HB.GLICOSILADA (mg/dL)</td>
                <td colspan="2" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['HbGlicosilada'] }}</td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5  mayuscula ">DOSAJE DE ALBUMINA EN ORINA (ug/mL)</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['DosajeAlbumina'] }}</td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-4-5  mayuscula ">DEPURACION DE CREATININA (mL/min)</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['DepuracionCreatina'] }}</td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DIAGNÓSTICOS</td>
            </tr>
            <tr>
                <td colspan="2" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">Nº</td>
                <td colspan="21" rowspan="2"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DESCRIPCIÓN</td>
                <td colspan="15" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">INGRESO</td>
                <td colspan="12" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">EGRESO</td>
            </tr>
            <tr>
                <td colspan="9" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">TIPO DE DX</td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CIE - 10</td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">TIPO DE DX</td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">CIE - 10</td>
            </tr>

            @if ($data['ItemDiagnosticos'])
                @foreach ($data['ItemDiagnosticos'] as $diagnostico)
                    <tr>
                        <td colspan="2" style="border: 1px solid Black; text-align: center;"
                            class="tamanio-5 mayuscula"> {{ $loop->iteration }} </td>
                        <td colspan="21" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                            {{ $diagnostico['DescripcionDx'] }}</td>
                        <td colspan="9" style="border: 1px solid Black; text-align: center;"
                            class="tamanio-5 mayuscula">{{ $diagnostico['TipoDx'] }} </td>
                        <td colspan="6" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                            {{ $diagnostico['CodigoDx'] }}</td>
                        <td colspan="6" style="border: 1px solid Black; text-align: center;"
                            class="tamanio-5 mayuscula">&nbsp;</td>
                        <td colspan="6" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                    </tr>
                @endforeach
            @endif
            <tr>
                <td colspan="16" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">N° DE DNI</td>
                <td colspan="18" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">NOMBRE DEL RESPONSABLE DE LA ATENCIÓN</td>
                <td colspan="16" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula "> N° DE COLEGIATURA</td>
            </tr>
            <tr>
                <td colspan="16" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['nDNI'] }}
                </td>
                <td colspan="18" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['NombreRespAtenc'] }}</td>
                <td colspan="16" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['NumeroColegiatura'] }}</td>
            </tr>
            <tr>
                <td colspan="8" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">RESPONSABLE DE LA ATENCIÓN</td>
                <td colspan="10" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['ResponsableAtencion'] }}</td>
                <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">ESPECIALIDAD</td>
                <td colspan="6" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Especialidad'] }}</td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">Nº RNE</td>
                <td colspan="5" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['nRne'] }}
                </td>
                <td colspan="5" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">EGRESADO</td>
                <td colspan="5" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['Egresado'] }}</td>
            </tr>
            <tr>
                <td colspan="50"
                    style="border-left: 1px solid Black; border-right: 1px solid Black; text-align:left;"
                    class="tamanio-5 mayuscula">1.MÉDICO 2.FARMACEUTICO
                    3.CIRUJANO DENTISTA 4.BIÓLOGO
                    5.OBSTETRIZ
                    6.ENFERMERA 7.TRABAJADORA SOCIAL 8.PSICOLOGA 9.TECNOLOGO MEDICO 10.NUTRICION 11.TECNICO ENFERMERIA
                    12.AUXILIAR
                    DE ENFERMERIA 13.OTRO</td>
            </tr>
            <tr>
                <td colspan="2" rowspan="8" style="border-left: 1px solid Black;">&nbsp;</td>
                <td colspan="13" rowspan="2">&nbsp;</td>
                <td rowspan="8">&nbsp;</td>
                <td colspan="5" style="text-align:left;" class="tamanio-5 mayuscula">FIRMA</td>
                <td>&nbsp;</td>
                <td colspan="5" rowspan="3" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="11" rowspan="3" style="border-bottom: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="11">&nbsp;</td>
                <td rowspan="8" style="border-right: 1px solid Black; text-align:left;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" class="tamanio-5 mayuscula" style="text-align:left;">ASEGURADO</td>
                <td class="tamanio-5 mayuscula" style="border: 1px solid Black;">{{ $data['Asegurado'] }}</td>
                <td colspan="3" rowspan="3">&nbsp; </td>
                <td colspan="7" rowspan="5" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    &nbsp;
                </td>
                <td rowspan="5" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="13" rowspan="5" style="border-bottom: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="5" class="tamanio-5 mayuscula" style="text-align:left;">REPRESENTANTE</td>
                <td style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Representante'] }}</td>
            </tr>
            <tr>
                <td colspan="22">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="10" class="tamanio-5 mayuscula" style="text-align:left;">REPRESENTANTE DEL
                    ASEGURADO
                </td>
                <td class="tamanio-5 mayuscula" style="text-align:left;">:</td>
                <td colspan="13" class="tamanio-5 mayuscula">{{ $data['RNombresApellidos'] }}</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="24">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="10" class="tamanio-5 mayuscula" style="text-align:left;">DNI o CE DEL REPRESENTANTE
                </td>
                <td class="tamanio-5 mayuscula" style="text-align:left;">:</td>
                <td colspan="13" class="tamanio-5 mayuscula" style="text-align:left;">
                    {{ $data['DniCeRepresentante'] }} </td>
                <td colspan="9" rowspan="2" style="text-align:center;" class="tamanio-5 ">
                    Huella Digital del Asegurado o del Representante
                </td>
            </tr>
            <tr>
                <td colspan="13" style="text-align: center;" class="tamanio-5 mayuscula">FIRMA Y SELLO DEL
                    RESPONSABLE DE LAATENCIÓN</td>
                <td colspan="11">&nbsp;</td>
                <td colspan="11">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="width:10px; border-left: 1px solid Black; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black;">&nbsp;</td>
                <td style="width:10px; border-bottom: 1px solid Black; border-right: 1px solid Black;">&nbsp;</td>
            </tr>
        </table>

        <div style="page-break-before: always;"><br><br></div>
        </div>

        <table
            style="width: 100%; table-layout: fixed; border: 1px solid Black;border-collapse: collapse; text-align: center !important">
            <tr>
                <td colspan="34" rowspan="2" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    TERAPEUTICA, INSUMOS, PROCEDIMIENTOS Y APOYO AL DIAGNOSTICO</td>
                <td colspan="16" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">FORMATO DE ATENCIÓN Nº</td>
            </tr>
            <tr>
                <td colspan="5" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['A'] }}
                </td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['B'] }}
                </td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">
                    {{ $data['C'] }}
                </td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black;" class="tamanio-5 mayuscula"></td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">PRODUCTOS FARMACEUTICOS / MEDICAMENTOS</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CÓDIGO SISMED
                </td>
                <td colspan="7" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    NOMBRE</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    FF
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CONCENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    PRES</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    ENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    DX
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CÓDIGO SISMED
                </td>
                <td colspan="7" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    NOMBRE</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    FF
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CONCENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    PRES</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    ENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    DX
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black;" class="tamanio-5 mayuscula"></td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">DISPOSITIVOS MÉDICOS / PRODUCTOS SANITARIOS</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CÓDIGO</td>
                <td colspan="7" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    NOMBRE</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    FF
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CONCENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    PRES</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    ENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    DX
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CÓDIGO</td>
                <td colspan="7" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    NOMBRE</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    FF
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CONCENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    PRES</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    ENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    DX
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black;" class="tamanio-5 mayuscula"></td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">PROCEDIMIENTOS/ DIAGNÓSTICO POR IMÁGENES/ LABORATORIO</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CÓDIGO</td>
                <td colspan="7" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    NOMBRE</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    FF
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CONCENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    PRES</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    ENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    DX
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CÓDIGO</td>
                <td colspan="7" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    NOMBRE</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    FF
                </td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CONCENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    PRES</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    ENTR</td>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    DX
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="7" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="3" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black;" class="tamanio-5 mayuscula"></td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black; text-align: center; background-color:#fefefe"
                    class="tamanio-5  mayuscula ">SUB COMPONENTE PRESTACIONAL (PROCEDIMIENTOS)</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CÓDIGO</td>
                <td colspan="13" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    NOMBRE</td>
                <td colspan="9" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    CARACT</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    IND/ PRES</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    EJE/ ENTR</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    DX
                </td>
                <td colspan="4" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    RES</td>
                <td colspan="4" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    N°
                    TICKET</td>
                <td colspan="5" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">
                    PO
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">&nbsp;


                </td>
                <td colspan="13" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="9" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="5" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">&nbsp;


                </td>
                <td colspan="13" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="9" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="5" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">&nbsp;


                </td>
                <td colspan="13" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="9" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="5" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">&nbsp;


                </td>
                <td colspan="13" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="9" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="5" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">&nbsp;


                </td>
                <td colspan="13" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="9" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="5" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">&nbsp;


                </td>
                <td colspan="13" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="9" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="4" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="5" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="50"
                    style="border: 1px solid Black; text-align: center; background-color:#fefefe; font-family: Arial, Helvetica, sans-serif;"
                    class="tamanio-5  mayuscula ">OBSERVACIONES</td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="50" style="border: 1px solid Black;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="50" style="text-align:left;" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" rowspan="8">&nbsp;</td>
                <td colspan="13" rowspan="2">&nbsp;</td>
                <td rowspan="8">&nbsp;</td>
                <td colspan="5" class="tamanio-5 mayuscula" style="text-align:left;">FIRMA</td>
                <td>&nbsp;</td>
                <td colspan="5" rowspan="3" class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="11" rowspan="3" style="border-bottom: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="11">&nbsp;</td>
                <td rowspan="8">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="5" class="tamanio-5 mayuscula" style="text-align:left;">ASEGURADO</td>
                <td class="tamanio-5 mayuscula" style="border: 1px solid Black;">{{ $data['Asegurado'] }}</td>
                <td colspan="3" rowspan="3">&nbsp; </td>
                <td colspan="7" rowspan="5" class="tamanio-5 mayuscula" style="border: 1px solid Black;">
                    &nbsp;
                </td>
                <td rowspan="5" class="tamanio-5 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="13" rowspan="5" style="border-bottom: 1px solid Black; text-align: center;"
                    class="tamanio-5 mayuscula">&nbsp;</td>
                <td colspan="5" class="tamanio-5 mayuscula" style="text-align:left;">REPRESENTANTE</td>
                <td style="border: 1px solid Black;" class="tamanio-5 mayuscula">{{ $data['Representante'] }}</td>
            </tr>
            <tr>
                <td colspan="22">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="10" class="tamanio-5 mayuscula" style="text-align:left;">REPRESENTANTE DEL
                    ASEGURADO
                </td>
                <td class="tamanio-5 mayuscula" style="text-align:left;">:</td>
                <td colspan="13" class="tamanio-5 mayuscula">{{ $data['RNombresApellidos'] }}</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="24">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="10" class="tamanio-5 mayuscula" style="text-align:left;">DNI o CE DEL REPRESENTANTE
                </td>
                <td class="tamanio-5 mayuscula" style="text-align:left;">:</td>
                <td colspan="13" class="tamanio-5 mayuscula" style="text-align:left;">
                    {{ $data['DniCeRepresentante'] }} </td>
                <td colspan="9" rowspan="2" style="text-align:center;" class="tamanio-5 ">
                    Huella Digital del Asegurado o del Representante
                </td>
            </tr>
            <tr>
                <td colspan="13" style="text-align: center;" class="tamanio-5 mayuscula">FIRMA Y SELLO DEL
                    RESPONSABLE DE LAATENCIÓN</td>
                <td colspan="11">&nbsp;</td>
                <td colspan="11">&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
                <td style="width:10px">&nbsp;</td>
            </tr>
        </table>
    </main>
</body>

</html>
