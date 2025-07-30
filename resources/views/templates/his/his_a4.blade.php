<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title> {{ config('app.name', 'Laravel') }} </title>
    <style>
        @page {
            margin: 0cm 0cm;
        }

        body {
            font-family: "roboto-regular", sans-serif;
            font-weight: initial;
            font-size: 0.9rem;
            overflow-x: hidden;
            overflow-y: auto;
            margin: 5.7cm 0.5cm 3.3cm 0.5cm;
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
            padding: 10px 0;
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
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }
    </style>
</head>


<body>
    <header>
        <table style="width:100%;">
            <thead>
                <tr>
                    <td>
                        <br><br><br>
                    </td>
                </tr>
            </thead>
        </table>
        <!-- Cabecera -->
        <table style="width:100%; text-align: center; " border="0" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <td colspan="23" style="text-align: left">
                        @if ($data['LogoOficial'])
                            <img src="data:image/png;base64,{{ $data['LogoOficial'] }}" width="100%" height="4%">
                        @endif
                    </td>
                    <td colspan="25" style="text-align: right;"> </td>
                </tr>
                <tr>
                    <td colspan="2" rowspan="4">&nbsp;</td>
                    <td colspan="42" rowspan="4" style="text-align: center" class="tamanio-7 mayuscula">
                        <p>MINISTERIO DE SALUD<br />OFICINA GENERAL DE TECNOLOGIAS DE LA INFORMACION<br />OFICINA DE
                            GESTION DE LA
                            INFORMACION<br />Registro Diario de Atencion y otros Actividades de Salud
                    </td>
                    <td width="16" rowspan="4">&nbsp;</td>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">
                        TURNO</td>
                    <td width="286" class="tamanio-4 mayuscula" style="text-align: center">&nbsp;</td>
                </tr>

                <tr>
                    <td colspan="2" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">M
                    </td>
                    <td class="tamanio-4 mayuscula" style="text-align: center">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>

            </thead>
            <tr>
                <td width="29" style="width:15.86px;"></td>
                <td width="29" style="width:15.86px;"></td>
                <td width="74" style="width:15.86px;"></td>
                <td width="1" style="width:15.86px;"></td>
                <td width="45" style="width:15.86px;"></td>
                <td width="43" style="width:15.86px;"></td>
                <td width="42" style="width:15.86px;"></td>
                <td width="21" style="width:15.86px;"></td>
                <td width="21" style="width:15.86px;"></td>
                <td width="22" style="width:15.86px;"></td>
                <td width="28" style="width:15.86px;"></td>
                <td width="28" style="width:15.86px;"></td>
                <td width="28" style="width:15.86px;"></td>
                <td width="26" style="width:15.86px;"></td>
                <td width="26" style="width:15.86px;"></td>
                <td width="26" style="width:15.86px;"></td>
                <td width="25" style="width:15.86px;"></td>
                <td width="33" style="width:15.86px;"></td>
                <td width="33" style="width:15.86px;"></td>
                <td width="32" style="width:15.86px;"></td>
                <td width="38" style="width:15.86px;"></td>
                <td width="38" style="width:15.86px;"></td>
                <td width="38" style="width:15.86px;"></td>
                <td width="39" style="width:15.86px;"></td>
                <td width="29" style="width:15.86px;"></td>
                <td width="29" style="width:15.86px;"></td>
                <td width="29" style="width:15.86px;"></td>
                <td width="25" style="width:15.86px;"></td>
                <td width="25" style="width:15.86px;"></td>
                <td width="26" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td style="width:15.86px;"></td>
                <td style="width:15.86px;"></td>
                <td style="width:15.86px;"></td>
                <td style="width:15.86px;"></td>
                <td style="width:15.86px;"></td>
                <td style="width:15.86px;"></td>
                <td style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
                <td width="16" style="width:15.86px;"></td>
            </tr>
            <tr>
                <td colspan="3"
                    style="width: 6%; border: 1px solid Black; text-align: center; background-color:#DBDBDB"
                    class="tamanio-4 mayuscula">AÑO</td>
                <td colspan="2"
                    style="width: 4%; border: 1px solid Black; text-align: center; background-color:#DBDBDB"
                    class="tamanio-4 mayuscula">MES</td>
                <td colspan="2"
                    style="width: 4%; border: 1px solid Black; text-align: center; background-color:#DBDBDB"
                    class="tamanio-4 mayuscula">DIA</td>
                <td colspan="13"
                    style="width: 26%; border: 1px solid Black; text-align: center; background-color:#DBDBDB"
                    class="tamanio-4 mayuscula">NOMBRE DE ESTABLECIMIENTO DE SALUD(IPRESS)</td>
                <td colspan="12"
                    style="width: 24%; border: 1px solid Black; text-align: center; background-color:#DBDBDB"
                    class="tamanio-4 mayuscula">UNIDAD PRODUCTORA DE SERVICIO(UPSS)</td>
                <td colspan="18"
                    style="width: 36%; border: 1px solid Black; text-align: center; background-color:#DBDBDB"
                    class="tamanio-4 mayuscula">RESPONSABLE DE LA ATENCION</td>
            </tr>
            <tr>
                <td colspan="3" style="border: 1px solid Black; text-align: center" class="tamanio-4 mayuscula">
                    {{ $data['Año'] }}</td>
                <td colspan="2" style="border: 1px solid Black; text-align: center" class="tamanio-4 mayuscula">
                    {{ $data['Mes'] }}</td>
                <td colspan="2" style="border: 1px solid Black; text-align: center" class="tamanio-4 mayuscula">
                    {{ $data['Dia'] }}</td>
                <td colspan="13" style="border: 1px solid Black; text-align: center" class="tamanio-4 mayuscula">
                    {{ $data['EstSalud'] }}</td>
                <td colspan="12" style="border: 1px solid Black; text-align: center" class="tamanio-4 mayuscula">
                    {{ $data['UPS'] }}</td>
                <td colspan="18" style="border: 1px solid Black; text-align: center" class="tamanio-4 mayuscula">
                    {{ $data['ResponAtencion'] }}</td>
            </tr>
            <tr>
                <td colspan="3" rowspan="3" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center">Tipo
                    Doc </td>
                <td colspan="4" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">
                    D.N.I
                </td>
                <td colspan="3" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">
                    FINANC.</td>
                <td colspan="3" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">
                    DISTRITO DE
                    PROC.</td>
                <td colspan="2" rowspan="3" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center">EDAD
                </td>
                <td colspan="2" rowspan="3" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center">SEXO
                </td>
                <td colspan="3" rowspan="3" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center">
                    PERIMETRO CEFALICO Y ABDOMINAL</td>
                <td colspan="4" rowspan="3" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center">
                    EVALUACION ANTROPOMETRICA HEMOGLOBINA</td>
                <td colspan="3" rowspan="3" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center">
                    ESTABLEC</td>
                <td colspan="3" rowspan="3" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center">
                    SERVICIO</td>
                <td colspan="11" rowspan="3" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center">
                    DIAGNOSTIVO MOTIVO DE CONSULTA Y/O ACTIVIDAD DE SALUD</td>
                <td colspan="3" rowspan="2" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center">TIPO
                    DE DIAGNOSTICO</td>
                <td colspan="3" rowspan="2" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center">
                    VALOR LAB.</td>
                <td colspan="3" rowspan="3" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center">
                    CODIGO CIE/CPT</td>
            </tr>
            <tr>
                <td colspan="4" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">
                    HISTORIA CLINICA
                </td>
                <td colspan="3" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center; background-color:#DBDBDB">10</td>
                <td colspan="3" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center; background-color:#DBDBDB">12</td>
            </tr>
            <tr>
                <td colspan="4" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">
                    GESTANTE /
                    PUERPERA</td>
                <td colspan="3" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">
                    ETNIA</td>
                <td colspan="3" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">
                    CENTRO POBLADO
                    (*)</td>
                <td width="32" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">D
                </td>
                <td width="21" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">P
                </td>
                <td width="68" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">R
                </td>
                <td class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">1</td>
                <td width="20" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">2
                </td>
                <td width="39" class="tamanio-4 mayuscula" style="border: 1px solid Black; text-align: center">3
                </td>
            </tr>
        </table>
    </header>

    <!-- Pie de página -->
    <footer style="font-size: 11px !important; ">
        <table width="560" border="1" cellpadding="0" cellspacing="0">
            <tr>
                <td colspan="15" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center; background-color:#DBDBDB">ITEM 09:FINANCIADOR
                    DE SALUD
                </td>
                <td colspan="4" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center; background-color:#DBDBDB">ITEM 12</td>
                <td colspan="4" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center; background-color:#DBDBDB">ITEM 16</td>
                <td colspan="10" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center; background-color:#DBDBDB">ITEM 17 18 (CONDICION
                    DE
                    INGRESO)</td>
                <td colspan="4" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center; background-color:#DBDBDB">ITEM 20</td>
                <td colspan="8" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center; background-color:#DBDBDB">TIPO DOCUMENTO</td>
                <td colspan="5" class="tamanio-4 mayuscula"
                    style="border: 1px solid Black; text-align: center; background-color:#DBDBDB">FECHA DE ULTIMA REGLA
                </td>
            </tr>
            <tr>
                <td colspan="15" class="tamanio-4 mayuscula" style="border: 1px solid Black;">1.USUARIO 5.SANIDAD
                    FAP 10.OTROS
                    2.SEGURO 4.SOAT 11.EXONERACION 3.ESSALUD 6.SANIDAD NAVAL</td>
                <td colspan="4" class="tamanio-4 mayuscula" style="border: 1px solid Black;">Registrar el nombre
                    del centro
                    poblado</td>
                <td colspan="4" class="tamanio-4 mayuscula" style="border: 1px solid Black;">
                    PESO=Kg<br />TALLA=Cm<br />Hb=Valor
                </td>
                <td colspan="10" class="tamanio-4 mayuscula" style="border: 1px solid Black;">
                    <p>N= PACIENTE NUEVO (1ERA VEZ EN SU VIDA)<br />C= PACIENTE CONTINUADOR EN EL AÑO<br />R=PACIENTE
                        REINGRESANTE
                        EN EL AÑO</p>
                </td>
                <td colspan="4" class="tamanio-4 mayuscula" style="border: 1px solid Black;">P=DX
                    PRESUNTIVO<br />D=DX
                    DEFINITIVO<br />R=(CONTROL)</td>
                <td colspan="8" class="tamanio-4 mayuscula" style="border: 1px solid Black;">0=Sin documento
                    3=Pasaporte 1=DNI
                    8=Madre:TipoDoc+DNI+N°Hijo 2=CARNET DE EXTRANGERIA</td>
                <td colspan="5" class="tamanio-4 mayuscula" style="border: 1px solid Black;">Si no se cuenta con
                    el dato se
                    registra fecha de primera ecografía</td>
            </tr>
        </table>
    </footer>



    <main>

        @if ($data['DetalleHis'])
            @foreach ($data['DetalleHis'] as $item)
                <div style="page-break-inside: avoid;">
                    <table style="">
                        <tr>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                            <td style="width:15.86px;"></td>
                        </tr>
                        <tr>
                            <td colspan="25" class="tamanio-4 mayuscula"
                                style="border-bottom: 1px solid Black; border-top: 1px solid Black; border-left: 1px solid Black; text-align:left; background-color:#DBDBDB">
                                {{ $item->Pacientes }}</td>
                            <td colspan="19" class="tamanio-4 mayuscula"
                                style="border-bottom: 1px solid Black; border-top: 1px solid Black; text-align: right;  background-color:#DBDBDB">
                                FECHA
                                NACIMIENTO :</td>
                            <td width="199" colspan="6" class="tamanio-4 mayuscula"
                                style="border-bottom: 1px solid Black; border-top: 1px solid Black; border-right: 1px solid Black; text-align:left; background-color:#DBDBDB">
                                {{ $item->FechaNacimiento }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" rowspan="3" class="tamanio-4 mayuscula"
                                style="width: 6%; border: 1px solid Black; text-align: center">{{ $item->TipDoc }}
                            </td>
                            <td colspan="4" class="tamanio-4 mayuscula"
                                style="width: 8%;border: 1px solid Black; text-align: center">
                                {{ $item->Dni }}
                            </td>
                            <td colspan="3" class="tamanio-4 mayuscula"
                                style="width: 6%; border: 1px solid Black; text-align: center">
                                {{ $item->Financiamiento }}</td>
                            <td colspan="3" class="tamanio-4 mayuscula"
                                style=" width: 6%;border: 1px solid Black; text-align: center">
                                {{ $item->DistritoProc }}</td>
                            <td colspan="2" rowspan="3" class="tamanio-4 mayuscula"
                                style="width: 4%; border: 1px solid Black; text-align: center">{{ $item->Edad }}
                            </td>
                            <td colspan="2" rowspan="3" class="tamanio-4 mayuscula"
                                style="width: 4%; border: 1px solid Black; text-align: center">{{ $item->Sexo }}
                            </td>
                            <td colspan="1" class="tamanio-4 mayuscula"
                                style="width: 2%; border: 1px solid Black; text-align: center">PC</td>
                            <td colspan="2" class="tamanio-4 mayuscula"
                                style="width: 4%; border: 1px solid Black; text-align: center">
                                {{ $item->Pc }}
                            </td>
                            <td colspan="2" class="tamanio-4 mayuscula"
                                style="width: 4%; border: 1px solid Black; text-align: center">
                                PESO</td>
                            <td colspan="2" class="tamanio-4 mayuscula"
                                style="width: 4%; border: 1px solid Black; text-align: center">
                                {{ $item->Peso }}</td>
                            <td colspan="3" rowspan="3" class="tamanio-4 mayuscula"
                                style="width: 6%; border: 1px solid Black; text-align: center">
                                {{ $item->CondEstablec }}</td>
                            <td colspan="3" rowspan="3" class="tamanio-4 mayuscula"
                                style="width: 6%; border: 1px solid Black; text-align: center">
                                {{ $item->CondServicio }}</td>

                            <td colspan="20" rowspan="3" style="width: 20%; border: 1px solid Black;">

                                @php
                                    $listadiagnostico = DB::select(
                                        'EXEC AtencionesDiagnosticosListarIdAtencion ' . $item->IdAtencion,
                                    );
                                    $contardiagnostico = 0;
                                @endphp
                                @if ($listadiagnostico)
                                    <table style="width: 100%; table-layout: fixed; border-collapse: collapse;">
                                        @foreach ($listadiagnostico as $diagnosticos)
                                            @php
                                                $contardiagnostico++;
                                            @endphp
                                            <tr>
                                                <td colspan="11" class="tamanio-4 mayuscula"
                                                    style="border-bottom: 1px solid Black; border-right: 1px solid Black;">
                                                    {{ $contardiagnostico }}:
                                                    {{ Str::limit($diagnosticos->DescripcionDx, 39, '') }}
                                                </td>
                                                <td colspan="3" class="tamanio-4 mayuscula"
                                                    style="border-bottom: 1px solid Black; border-right: 1px solid Black; border-left: 1px solid Black; text-align: center">
                                                    {{ $diagnosticos->TipoDx }}
                                                </td>
                                                <td colspan="3" class="tamanio-4 mayuscula"
                                                    style="border-bottom: 1px solid Black; border-right: 1px solid Black; border-left: 1px solid Black;; text-align: center">
                                                    {{ $diagnosticos->Labs }}
                                                </td>
                                                <td colspan="3" class="tamanio-4 mayuscula"
                                                    style="border-bottom: 1px solid Black; border-left: 1px solid Black; text-align: center">
                                                    {{ $diagnosticos->CodigoDx }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" class="tamanio-4 mayuscula"
                                style="border: 1px solid Black; text-align: center">
                                {{ $item->HistoriaClinica }}</td>
                            <td colspan="3" rowspan="2" class="tamanio-4 mayuscula"
                                style="border: 1px solid Black; text-align: center">{{ $item->Etnia }}</td>
                            <td colspan="3" rowspan="2" class="tamanio-4 mayuscula"
                                style="border: 1px solid Black; text-align: center">{{ $item->CentroPoblado }}</td>
                            <td rowspan="2" class="tamanio-4 mayuscula"
                                style="border: 1px solid Black; text-align: center">
                                PAB</td>
                            <td colspan="2" rowspan="2" class="tamanio-4 mayuscula"
                                style="border: 1px solid Black; text-align: center">{{ $item->Pab }}</td>
                            <td colspan="2" class="tamanio-4 mayuscula"
                                style="border: 1px solid Black; text-align: center">
                                TALLA</td>
                            <td colspan="2" class="tamanio-4 mayuscula"
                                style="border: 1px solid Black; text-align: center">
                                {{ $item->Talla }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="tamanio-4 mayuscula"
                                style="border: 1px solid Black; text-align: center">
                                {{ $item->GestantePuerpera }}</td>
                            <td colspan="2" class="tamanio-4 mayuscula"
                                style="border: 1px solid Black; text-align: center">HB
                            </td>
                            <td colspan="2" class="tamanio-4 mayuscula"
                                style="border: 1px solid Black; text-align: center">
                                {{ $item->Hb }}
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
        @endif
    </main>
</body>

</html>
