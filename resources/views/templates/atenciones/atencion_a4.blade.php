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

<body>
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
        <table Style="width:100%" class="tabla-sin-bordes">
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
                <td colspan="50" class="tamanio-13" style="text-align: center;">HISTORIA CLINICA CONSULTA EXTERNA
                </td>
            </tr>
            <tr>
                <td colspan="50">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" class="tamanio-7 mayuscula">Paciente
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="21" class="tamanio-7 mayuscula">{{ $data['Pacientes'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula">Nº Doc
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="6" class="tamanio-7 mayuscula">{{ $data['DocPaciente'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula">Nº HC
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="6" class="tamanio-7 mayuscula">{{ $data['HC'] }}</td>
            </tr>
            <tr>
                <td colspan="6" class="tamanio-7 mayuscula">Telefono
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="21" class="tamanio-7 mayuscula">{{ $data['Telefono'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula">F.Nac.
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="6" class="tamanio-7 mayuscula">{{ $data['FechaNacimiento'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula">Sexo
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="6" class="tamanio-7 mayuscula">{{ $data['Sexo'] }}</td>
            </tr>
            <tr>
                <td colspan="6" class="tamanio-7 mayuscula">
                    Religión
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="21" class="tamanio-7 mayuscula">{{ $data['Religion'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula"><span style="text-align: left">Est. Civil</span></td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="6" class="tamanio-7 mayuscula">{{ $data['EstadoCivil'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula">Edad
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="6" class="tamanio-7 mayuscula">{{ $data['Edad'] }}</td>
            </tr>
            <tr>
                <td colspan="6" class="tamanio-7 mayuscula">
                    Ocupación
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="21" class="tamanio-7 mayuscula">{{ $data['Ocupacion'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula">
                    G.Instruc.</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="17" class="tamanio-7 mayuscula">{{ $data['GraInstruccion'] }}</td>
            </tr>
            <tr>
                <td colspan="6" class="tamanio-7 mayuscula">Lug.
                    Nac.</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="21" class="tamanio-7 mayuscula">{{ $data['LugarNacimiento'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula">Lug.
                    Res.</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="17" class="tamanio-7 mayuscula">{{ $data['LugarResidencia'] }}</td>
            </tr>
            <tr>
                <td colspan="6" class="tamanio-7 mayuscula">Lug. Pro.</td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="43" class="tamanio-7 mayuscula">{{ $data['LugarProcedencia'] }}</td>
            </tr>
            <tr>
                <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="50" class="tamanio-7  mayuscula negrita"><span style="text-align: left">Datos del
                        Acompañante</span></td>
            </tr>
            <tr>
                <td colspan="6" class="tamanio-7 mayuscula">Nombre
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="43" class="tamanio-7 mayuscula">{{ $data['DatosAcompañante'] }}</td>
            </tr>
            <tr>
                <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" class="tamanio-7 mayuscula">Cuenta
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="21" class="tamanio-7 mayuscula">{{ $data['Cuenta'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula">
                    Financia.
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="17" class="tamanio-7 mayuscula">{{ $data['Financiador'] }}</td>
            </tr>
            <tr>
                <td colspan="6" class="tamanio-7 mayuscula">
                    Consultorio
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="21" class="tamanio-7 mayuscula">{{ $data['Consultorio'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula">Fecha
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="6" class="tamanio-7 mayuscula">{{ $data['Fecha'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula">Hora
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="6" class="tamanio-7 mayuscula">{{ $data['Hora'] }}</td>
            </tr>
            <tr>
                <td colspan="6" class="tamanio-7 mayuscula">
                    Médico
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="21" class="tamanio-7 mayuscula">{{ $data['Medico'] }} - ({{ $data['Especialidad'] }})
                </td>
                <td colspan="4" class="tamanio-7 mayuscula">RNE
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="6" class="tamanio-7 mayuscula">{{ $data['RNE'] }}</td>
                <td colspan="4" class="tamanio-7 mayuscula">Coleg.
                </td>
                <td colspan="1" class="tamanio-7 mayuscula">:</td>
                <td colspan="6" class="tamanio-7 mayuscula">{{ $data['Colegiatura'] }}</td>
            </tr>
        </table>

        @if ($data['Personales'] || $data['Familiares'] || $data['Psicosociales'] || $data['SexuRepro'] || $data['Obstetricos'])
            <hr style="border: 1px solid #999; width: 100%; margin: 15px auto;">

            <table cellpadding="0" style="width:100%;  border-collapse: collapse; ">
                <tr>
                    <td colspan="50" style="text-align: left" class="tamanio-9 negrita mayuscula">ANTECEDENTES</td>
                </tr>

                @if ($data['Personales'])
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula" style="text-align: left">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula negrita" style="text-align: left">Personales:
                        </td>
                    </tr>
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula" style="text-align: left">
                            {{ $data['Personales'] }}</td>
                    </tr>
                @endif

                @if ($data['Familiares'])
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula" style="text-align: left">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula negrita" style="text-align: left">Familiares:
                        </td>
                    </tr>
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula" style="text-align: left">
                            {{ $data['Familiares'] }}</td>
                    </tr>
                @endif

                @if ($data['Psicosociales'])
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula" style="text-align: left">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula negrita" style="text-align: left">
                            Psicosociales:
                        </td>
                    </tr>
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula" style="text-align: left">
                            {{ $data['Psicosociales'] }} </td>
                    </tr>
                @endif

                @if ($data['SexuRepro'])
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula" style="text-align: left">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula negrita" style="text-align: left">Sexuales y
                            reproductivos:
                        </td>
                    </tr>
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula" style="text-align: left">
                            {{ $data['SexuRepro'] }}
                        </td>
                    </tr>
                @endif

                @if ($data['Obstetricos'])
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula" style="text-align: left">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula negrita" style="text-align: left">Obstétrico:
                        </td>
                    </tr>
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula" style="text-align: left">
                            {{ $data['Obstetricos'] }}</td>
                    </tr>
                @endif
            </table>
        @endif

        @if (
            $data['PA'] &&
                $data['FR'] &&
                $data['T'] &&
                $data['FC'] &&
                $data['SatO2'] &&
                $data['Peso'] &&
                $data['Talla'] &&
                $data['Imc']
        )
            <hr style="border: 1px solid #999; width: 100%; margin: 15px auto;">
            <table cellpadding="0.1px" style="width:100%; border-collapse: collapse;">
                <tr style="text-align: left">
                    <td colspan="50" class="tamanio-7  mayuscula negrita">FUNCIONES VITALES</td>
                </tr>
                <tr>
                    <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                        class="tamanio-7 mayuscula">PA</td>
                    <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                        class="tamanio-7 mayuscula">FR</td>
                    <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                        class="tamanio-7 mayuscula">T°</td>
                    <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                        class="tamanio-7 mayuscula">FC</td>
                    <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                        class="tamanio-7 mayuscula">SAT O2
                    </td>
                    <td colspan="6" style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                        class="tamanio-7 mayuscula">PESO
                    </td>
                    <td colspan="7" style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                        class="tamanio-7 mayuscula">TALLA
                    </td>
                    <td colspan="7" style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                        class="tamanio-7 mayuscula">IMC</td>
                </tr>
                <tr>
                    <td colspan="6" style="border: 1px solid Black; text-align: center"
                        class="tamanio-7 mayuscula">
                        {{ $data['PA'] }}
                    </td>
                    <td colspan="6" style="border: 1px solid Black; text-align: center"
                        class="tamanio-7 mayuscula">
                        {{ $data['FR'] }}
                    </td>
                    <td colspan="6" style="border: 1px solid Black; text-align: center"
                        class="tamanio-7 mayuscula">
                        {{ $data['T'] }}
                    </td>
                    <td colspan="6" style="border: 1px solid Black; text-align: center"
                        class="tamanio-7 mayuscula">
                        {{ $data['FC'] }}
                    </td>
                    <td colspan="6" style="border: 1px solid Black; text-align: center"
                        class="tamanio-7 mayuscula">
                        {{ $data['SatO2'] }}
                    </td>
                    <td colspan="6" style="border: 1px solid Black; text-align: center"
                        class="tamanio-7 mayuscula">
                        {{ $data['Peso'] }}
                    </td>
                    <td colspan="7" style="border: 1px solid Black; text-align: center"
                        class="tamanio-7 mayuscula">
                        {{ $data['Talla'] }}
                    </td>
                    <td colspan="7" style="border: 1px solid Black; text-align: center"
                        class="tamanio-7 mayuscula">
                        {{ $data['Imc'] }}
                    </td>
                </tr>
            </table>
        @endif

        @if ($data['FunBiologicas'])
            <hr style="border: 1px solid #999; width: 100%; margin: 15px auto;">
            <table cellpadding="0" style="width:100%;  border-collapse: collapse;">
                <tr style=" text-align: left">
                    <td colspan="100" class="tamanio-7 mayuscula ">
                        <b class="negrita">FUNCIONES BIOLOGICAS: </b>{{ $data['FunBiologicas'] }}
                    </td>
                </tr>
            </table>
        @endif

        @if ($data['TiempoEnfermedad'] || $data['MotivoConsulta'])
            <hr style="border: 1px solid #999; width: 100%; margin: 15px auto;">

            <table cellpadding="0" style="width:100%;">
                @if ($data['TiempoEnfermedad'])
                    <tr style="text-align: left">
                        <td colspan="100" class="tamanio-7  mayuscula">
                            <b class="negrita">TIEMPO DE ENFERMEDAD: </b> {{ $data['TiempoEnfermedad'] }}
                        </td>
                    </tr>
                @endif
                @if ($data['MotivoConsulta'])
                    <tr style="text-align: left">
                        <td colspan="100" class="tamanio-7  mayuscula">
                            <b class="negrita">MOTIVO DE CONSULTA: </b> {{ $data['MotivoConsulta'] }}
                        </td>
                    </tr>
                @endif
            </table>
        @endif


        @if ($data['RelatoCronolo'])
            <hr style="border: 1px solid #999; width: 100%; margin: 15px auto;">

            <table cellpadding="0" style="width:100%;">
                <tr style="text-align: left">
                    <td colspan="50" class="tamanio-7  mayuscula negrita">RELATO CRONOLÓGICO</td>
                </tr>
                <tr style="text-align: left">
                    <td colspan="50" class="tamanio-7 mayuscula">{{ $data['RelatoCronolo'] }}</td>
                </tr>
            </table>
        @endif

        @if ($data['TratamientoRecib'])
            <hr style="border: 1px solid #999; width: 100%; margin: 15px auto;">

            <table cellpadding="0" style="width:100%;">
                <tr style="text-align: left">
                    <td colspan="50" class="tamanio-7  mayuscula negrita">TRATAMIENTO RECIBIDO (Cumplimiento y
                        Resultados)
                    </td>
                </tr>
                <tr style="text-align: left">
                    <td colspan="50" class="tamanio-7 mayuscula">{{ $data['TratamientoRecib'] }}</td>
                </tr>
            </table>
        @endif

        @if ($data['ExaGeneral'] || $data['ExaRegional'])
            <hr style="border: 1px solid #999; width: 100%; margin: 15px auto;">

            <table cellpadding="0" style="width:100%;">
                <tr style="text-align: left">
                    <td colspan="50" class="tamanio-7 mayuscula negrita">EXAMEN FISICO</td>
                </tr>
                @if ($data['ExaGeneral'])
                    <tr style="text-align: left">
                        <td colspan="50" class="tamanio-7  mayuscula negrita">&nbsp;</td>
                    </tr>
                    <td colspan="100" class="tamanio-7  mayuscula">
                        <b class="negrita">A. Examen General: </b> {{ $data['ExaGeneral'] }}
                    </td>
                @endif

                @if ($data['ExaRegional'])
                    <tr style="text-align: left">
                        <td colspan="50" class="tamanio-7  mayuscula negrita">&nbsp;</td>
                    </tr>
                    <td colspan="100" class="tamanio-7  mayuscula">
                        <b class="negrita">B. Examen Regional: </b> {{ $data['ExaRegional'] }}
                    </td>
                @endif
            </table>
        @endif


        @if ($data['ItemDiagnosticos']) <br>
            <div style="page-break-inside: avoid;">
                <table cellpadding="0.1px" style="width:100%; border-collapse: collapse;">
                    <tr style="text-align: left">
                        <td colspan="50" class="tamanio-7  mayuscula negrita">DIAGNOSTICO</td>
                    </tr>
                    <tr>
                        <td colspan="6"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">Codigo
                        </td>
                        <td colspan="34"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula"> Descripción
                        </td>
                        <td colspan="10"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">Tipo Diagnostico</td>
                    </tr>
                    @foreach ($data['ItemDiagnosticos'] as $item)
                        <tr>
                            <td colspan="6" style="border: 1px solid Black; text-align: center"
                                class="tamanio-7 mayuscula"> {{ $item['CodigoDx'] }}
                            </td>
                            <td colspan="34" style="border: 1px solid Black; text-align: left"
                                class="tamanio-7 mayuscula">
                                {{ $item['DescripcionDx'] }}
                            </td>
                            <td colspan="10" style="border: 1px solid Black; text-align: center"
                                class="tamanio-7 mayuscula">{{ $item['TipoDx'] }}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif


        @if ($data['ItemOrdenesLaboratorio']) <br>
            <div style="page-break-inside: avoid;">
                <table cellpadding="0.1px" style="width:100%; text-align: center; border-collapse: collapse;">
                    <tr>
                        <td colspan="50" style="text-align: left" class="tamanio-9 mayuscula negrita">PLAN DE
                            TRABAJO
                        </td>
                    </tr>
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="50" style="text-align: left" class="tamanio-7  mayuscula negrita">Exámenes de
                            Laboratorio
                            / Anatomía Patológica:</td>
                    </tr>
                    <tr>
                        <td colspan="6"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">Cantidad
                        </td>
                        <td colspan="34"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">Examen
                        </td>
                        <td colspan="10"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">Servicio
                        </td>
                    </tr>
                    @foreach ($data['ItemOrdenesLaboratorio'] as $item)
                        <tr>
                            <td colspan="6" style="border: 1px solid Black;" class="tamanio-7 mayuscula centrado">
                                {{ $item['Cantidad'] }}
                            </td>
                            <td colspan="34" style="border: 1px solid Black; text-align: left;"
                                class="tamanio-7 mayuscula">
                                {{ $item['Medicamento'] }}
                            </td>
                            <td colspan="10" style="border: 1px solid Black;" class="tamanio-7 mayuscula centrado">
                                {{ $item['Servicio'] }}
                            </td>
                        </tr>
                    @endforeach
                </table><br>
            </div>
        @endif


        @if ($data['ItemOrdenesImagenes'])
            <div style="page-break-inside: avoid;">
                <table cellpadding="0.1px" style="width:100%; border-collapse: collapse;">
                    <tr>
                        <td colspan="50" class="tamanio-7  mayuscula negrita">Exámenes de Diagnóstico de Imágenes:
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">Cantidad
                        </td>
                        <td colspan="34"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">Examen
                        </td>
                        <td colspan="10"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">Servicio
                        </td>
                    </tr>
                    @foreach ($data['ItemOrdenesImagenes'] as $item)
                        <tr>
                            <td colspan="6" style="border: 1px solid Black;" class="tamanio-7 mayuscula centrado">
                                {{ $item['Cantidad'] }}
                            </td>
                            <td colspan="34" style="border: 1px solid Black; text-align: left"
                                class="tamanio-7 mayuscula">
                                {{ $item['Medicamento'] }}
                            </td>
                            <td colspan="10" style="border: 1px solid Black;" class="tamanio-7 mayuscula centrado">
                                {{ $item['Servicio'] }}
                            </td>
                        </tr>
                    @endforeach
                </table><br>
            </div>
        @endif


        @if ($data['ItemProcedimientos'])
            <div style="page-break-inside: avoid;">
                <table cellpadding="0.1px" style="width:100%; text-align: left;  border-collapse: collapse;">
                    <tr>
                        <td colspan="50" style="text-align: left" class="tamanio-7  mayuscula negrita">
                            Procedimientos:
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">CANTIDAD
                        </td>
                        <td colspan="44" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">CPMS - PROCEDIMIENTO
                        </td>
                    </tr>
                    @foreach ($data['ItemProcedimientos'] as $item)
                        <tr>
                            <td colspan="6" style="border: 1px solid Black;" class="tamanio-7 mayuscula centrado">
                                {{ $item['Cantidad'] }}
                            </td>
                            <td colspan="44" style="border: 1px solid Black; text-align: left"
                                class="tamanio-7 mayuscula">
                                {{ $item['Procedimiento'] }}
                            </td>
                        </tr>
                    @endforeach
                </table><br>
            </div>
        @endif


        @if ($data['ItemRecetas'])
            <div style="page-break-inside: avoid;">
                <table cellpadding="0.1px" style="width:100%;  border-collapse: collapse;">
                    <tr>
                        <td colspan="50" class="tamanio-7 mayuscula negrita">Terapeutica:</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">Cantidad
                        </td>
                        <td colspan="17" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">
                            Medicamentos
                        </td>
                        <td colspan="4" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">Dosis
                        </td>
                        <td colspan="6" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">Via Adm
                        </td>
                        <td colspan="18" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">
                            Indicaciones
                        </td>
                    </tr>
                    @foreach ($data['ItemRecetas'] as $item)
                        <tr>
                            <td colspan="5" style="border: 1px solid Black" class="tamanio-7 mayuscula centrado">
                                {{ $item['Cantidad'] }}</td>
                            <td colspan="17" style="border: 1px solid Black" class="tamanio-7 mayuscula">
                                {{ $item['Medicamento'] }}</td>
                            <td colspan="4" style="border: 1px solid Black" class="tamanio-7 mayuscula centrado">
                                {{ $item['Dosis'] }}</td>
                            <td colspan="6" style="border: 1px solid Black" class="tamanio-7 mayuscula centrado">
                                {{ $item['Via'] }}</td>
                            <td colspan="18" style="border: 1px solid Black" class="tamanio-7 mayuscula">
                                {{ $item['Indicaciones'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        @if ($data['ItemInterconsultas']) <br>
            <div style="page-break-inside: avoid;">
                <table cellpadding="0.1px" style="width:100%; border-collapse: collapse;">
                    <tr>
                        <td colspan="50" style="text-align: left" class="tamanio-7 mayuscula negrita">
                            INTERCONSULTAS
                        </td>
                    </tr>
                    <tr>
                        <td colspan="17" style="border: 1px solid Black; background-color:#DBDBDB;"
                            class="tamanio-7 mayuscula centrado">
                            Especialidad
                        </td>
                        <td colspan="17" style="border: 1px solid Black; background-color:#DBDBDB;"
                            class="tamanio-7 mayuscula centrado">
                            Diagnóstico de Interconsulta
                        </td>
                        <td colspan="16" style="border: 1px solid Black; background-color:#DBDBDB;"
                            class="tamanio-7 mayuscula centrado">
                            Motivo
                        </td>
                    </tr>
                    @foreach ($data['ItemInterconsultas'] as $item)
                        <tr>
                            <td colspan="17" style="border: 1px solid Black;" class="tamanio-7 mayuscula centrado">
                                {{ $item['Especialidad'] }}
                            </td>
                            <td colspan="17" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                                {{ $item['Diagnostico'] }}
                            </td>
                            <td colspan="16" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                                {{ $item['Motivo'] }}
                            </td>

                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        @if ($data['NumeroReferencia'] || $data['NumeroContraref'])
            <hr style="border: 1px solid #999; width: 100%; margin: 15px auto;">
        @endif

        @if ($data['NumeroReferencia'])
            <div style="page-break-inside: avoid;">
                <table cellpadding="0.1px" style="width:100%;  border-collapse: collapse;">
                    <tr style=" text-align: left">
                        <td colspan="50" class="tamanio-7  mayuscula negrita">REFERENCIA</td>
                    </tr>
                    <tr style="text-align: left">
                        <td colspan="7" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">Número Referencia</td>
                        <td colspan="4" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">Fecha</td>
                        <td colspan="13" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">
                            Establecimiento</td>
                        <td colspan="13" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">Diagnostico
                        </td>
                        <td colspan="13" style="border: 1px solid Black; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula centrado">
                            Especialidad
                        </td>
                    </tr>
                    <tr style="text-align: left">
                        <td colspan="7" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $data['NumeroReferencia'] }}</td>
                        <td colspan="4" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $data['FechaR'] }}
                        </td>
                        <td colspan="13" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $data['EstablecimientoR'] }}</td>
                        <td colspan="13" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $data['DiagnosticoR'] }}</td>
                        <td colspan="13" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $data['EspecialidadR'] }}</td>
                    </tr>
                </table>
            </div>
        @endif

        @if ($data['NumeroContraref'])
            <br>
            <div style="page-break-inside: avoid;">
                <table cellpadding="0.1px" style="width:100%;  border-collapse: collapse;">
                    <tr>
                        <td colspan="50" style="text-align: left" class="tamanio-7  mayuscula negrita">
                            CONTRAREFERENCIA
                        </td>
                    </tr>
                    <tr style="text-align: left">
                        <td colspan="8"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">Número Contrareferencia</td>
                        <td colspan="4"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">Fecha</td>
                        <td colspan="12"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">
                            Establecimiento</td>
                        <td colspan="13"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">Diagnostico
                        </td>
                        <td colspan="13"
                            style="border: 1px solid Black; text-align: center; background-color:#DBDBDB "
                            class="tamanio-7 mayuscula">
                            Especialidad
                        </td>
                    </tr>
                    <tr style="text-align: left">
                        <td colspan="8" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $data['NumeroContraref'] }}</td>
                        <td colspan="4" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $data['FechaCr'] }}
                        </td>
                        <td colspan="12" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $data['EstablecimientoCr'] }}</td>
                        <td colspan="13" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $data['DiagnosticoCr'] }}</td>
                        <td colspan="13" style="border: 1px solid Black;" class="tamanio-7 mayuscula">
                            {{ $data['EspecialidadCr'] }}</td>
                    </tr>
                </table>
            </div>
        @endif

        @if ($data['FechaProxCita'] || $data['Observaciones'])
            <hr style="border: 1px solid #999; width: 100%; ">
        @endif


        <table cellpadding="0" style="width:100%;">
            @if ($data['FechaProxCita'])
                <tr>
                    <td colspan="50" style="text-align: left" class="tamanio-7 mayuscula">
                        <b class="negrita">Fecha Proxima Cita: </b> {{ $data['FechaProxCita'] }}
                    </td>
                </tr>
            @endif

            @if ($data['Observaciones'])
                <tr>
                    <td colspan="50" style="text-align: left" class="tamanio-7 mayuscula">
                        <b class="negrita">Observaciones: </b> {{ $data['Observaciones'] }}
                    </td>
                </tr>
            @endif
        </table>
    </main>
</body>

</html>
