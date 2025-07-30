<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $data['FileName'] }}</title>
</head>

<body>
<div class="t-block">
    <table class="tabla-sin-bordes">
        <tr class="tamanio-12">
            <td colspan="12" class="t-text-center negrita">
                {{ $data['HospitalRazonSocial'] }} <br/>
                RUC: {{ $data['HospitalRuc'] }} <br/>
                {{ $data['DocumentoTipoNombre'] }} <br/>
                {{ $data['NroSerie'] }} {{ $data['NroDocumento'] }}
            </td>
        </tr>
        <tr>
            <td colspan="12">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="4" class="tamanio-9 negrita">Fecha de Emisión</td>
            <td colspan="8" class="tamanio-8 negrita">{{ $data['FechaHoraEmision'] }}</td>
        </tr>
        <tr>
            <td colspan="4" class="tamanio-9 negrita">Condición de pago:</td>
            <td colspan="8" class="tamanio-9 negrita">{{ $data['CondicionPagoNombre'] }}</td>
        </tr>
        {{--             <tr>--}}
        {{--                <td colspan="4" class="tamanio-9 negrita">Fecha de Vencimiento: </td>--}}
        {{--                <td  colspan="8" class="tamanio-9 negrita">22/03/2025</td>--}}
        {{--            </tr>--}}
        @if($data['EmpresaNombre'])
            <tr>
                <td colspan="4" class="tamanio-9 negrita">CLIENTE:</td>
                <td colspan="8" class="tamanio-9 negrita">{{ $data['EmpresaNombre'] }}</td>
            </tr>
            <tr>
                <td colspan="4" class="tamanio-9 negrita">RUC:</td>
                <td colspan="8" class="tamanio-9 negrita">{{ $data['EmpresaRuc'] }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="4" class="tamanio-9 negrita">PACIENTE:</td>
            <td colspan="8" class="tamanio-9 negrita">{{ $data['PacienteNombre'] }}</td>
        </tr>
        <tr>
            <td colspan="4" class="tamanio-9 negrita">SD:</td>
            <td colspan="8" class="tamanio-9 negrita">{{ $data['PacienteNumero'] }}</td>
        </tr>
    </table>
</div>
<div class="t-block">
    <table class="tabla-sin-bordes">
        <tr>
            <td colspan="12">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="12" class="tamanio-9 negrita">DESCRIPCION:</td>
        </tr>
        <tr>
            <td colspan="2" class="tamanio-9 negrita">CANT</td>
            <td colspan="1" class="tamanio-9 negrita">UNID</td>
            <td colspan="1" class="tamanio-9 negrita">P.UNIT</td>
            <td colspan="1" class="tamanio-9 negrita">IMPORTE</td>
        </tr>
        <tr>
            <td colspan="12">
                <hr>
            </td>
        </tr>
        <tr>
            <td colspan="12" class="tamanio-9 negrita">JUEGO DE CLISESS CELINA:</td>
        </tr>
        <tr>
            <td colspan="2" class="tamanio-9 negrita">1</td>
            <td colspan="1" class="tamanio-9 negrita">NIU</td>
            <td colspan="1" class="tamanio-9 negrita">2,454.40</td>
            <td colspan="1" class="tamanio-9 negrita">2,454.40</td>
        </tr>
    </table>
</div>
<div class="t-block">
    <table class="tabla-sin-bordes">
        <tr>
            <td colspan="4" class="tamanio-9 negrita">OP.GRAVADAS :</td>
            <td colspan="3" class="tamanio-9 negrita">{{ $data['TotalGravado'] }}</td>
        </tr>
        <tr>
            <td colspan="4" class="tamanio-9 negrita">OP.EXONERADAS :</td>
            <td colspan="3" class="tamanio-9 negrita">{{ $data['TotalExonerado'] }}</td>
        </tr>
        <tr>
            <td colspan="4" class="tamanio-9 negrita">OP.INAFECTAS :</td>
            <td colspan="3" class="tamanio-9 negrita">{{ $data['TotalInafecto'] }}</td>
        </tr>
        <tr>
            <td colspan="4" class="tamanio-9 negrita">SUBTOTAL :</td>
            <td colspan="3" class="tamanio-9 negrita">{{ $data['Subtotal'] }}</td>
        </tr>
        <tr>
            <td colspan="4" class="tamanio-9 negrita">TOTAL IGV :</td>
            <td colspan="3" class="tamanio-9 negrita">{{ $data['TotalIGV'] }}</td>
        </tr>
        <tr>
            <td colspan="4" class="tamanio-9 negrita">TOTAL :</td>
            <td colspan="3" class="tamanio-9 negrita">{{ $data['Total'] }}</td>
        </tr>
        <tr>
            <td colspan="12" class="tamanio-9 negrita">SON : DOS MIS CUATROCIENTOS CINCUENTA Y CUATRO CON 40/100 DOLARES
                AMERICAOS
            </td>
        </tr>
        <tr>
            <td colspan="12" style="text-align:center" class="tamanio-9 negrita"><img width="200" height="150"
                                                                                      src="QR.png" align="CENTER"/><br/>wv06ZbBjDn7HcVigsI/CyR4ujFM=
            </td>
        </tr>
        <tr>
            <td colspan="9" class="tamanio-9 negrita">cuentas bancarias: <br/> BCP: $ 191-2495942-1-43</td>
        </tr>
        <tr>
            <td colspan="2" class="tamanio-9 negrita" style="border:1px solid black; text-align:center;"># de cuota:
            </td>
            <td colspan="5" class="tamanio-9 negrita" style="border:1px solid black; text-align:center;">F.
                Vencimiento
            </td>
            <td colspan="5" class="tamanio-9 negrita" style="border:1px solid black; text-align:center;">Monto</td>
        </tr>
        <tr>
            <td colspan="2" class="tamanio-9 negrita" style="border:1px solid black; text-align:center;">cuota 1</td>
            <td colspan="5" class="tamanio-9 negrita" style="border:1px solid black; text-align:center;">22/03/2025</td>
            <td colspan="5" class="tamanio-9 negrita" style="border:1px solid black; text-align:center;">2,454.40</td>
        </tr>
        <tr>
            <td colspan="12" class="tamanio-9 negrita">Representacion impresa de la factura electonica <br/> Para
                consultar el comprobante ingresar a <br/>https://comprobantes.intipos.pe/
            </td>
        </tr>
    </table>

</div>
</body>
</html>
