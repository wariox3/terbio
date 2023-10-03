<?php


namespace App\Formato;


use App\Controller\FuncionesController;
use App\Entity\Formato;
use App\Entity\FormatoImagen;

class CertificadoLaboral extends \FPDF
{
    public static $em;
    public static $codigoFormato;
    public static $codigoContrato;
    public static $arUsuario;

    /**
     * @param $em
     * @param $codigoContrato
     */
    public function Generar($em, $codigoContrato, $usuario)
    {
        ob_clean();
        self::$em = $em;
        self::$codigoContrato = $codigoContrato;
        self::$arUsuario = $usuario;
        $pdf = new CertificadoLaboral();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times', '', 12);
        $pdf->SetMargins(25, 15, 25);
        $this->Body($pdf, $codigoContrato);
        $pdf->Output("Carta.pdf", 'D');
    }

    public function Header()
    {
        $this->EncabezadoDetalles();
    }

    public function EncabezadoDetalles()
    {
        $this->Ln(8);
    }

    public function Body($pdf, $codigoContrato)
    {
        /**
         * @var $arContrato Contrato
         */
        $pdf->Ln(24);
        $fechaActual = $dateNow = new \DateTime('now');
        $parametrosContrato = ['codigoContrato' => $codigoContrato];
        $parametrosSalarioPromedio = ['codigoContrato' => $codigoContrato, 'cantidad' => 1];
        $parametrosSalarioPromedioDosUltimosPago = ['codigoContrato' => $codigoContrato, 'cantidad' => 2];

        $arrContrato = FuncionesController::consumirApi(self::$arUsuario->getEmpresaRel(), $parametrosContrato, "/recursohumano/api/contrato/detalle");
        $arContrato = $arrContrato[0];
        $salarioLetras = $this->numtoletras($arContrato->vrSalario);
        $fechaDesde = date_create($arContrato->fechaDesde);
        $fechaHasta = date_create($arContrato->fechaHasta);
        $arFormato = self::$em->getRepository(Formato::class)->findOneBy(array('codigoFormatoTipoFk' => "CL", 'codigoEmpresaFk' => self::$arUsuario->getCodigoEmpresaFk()));
        $salarioPromedio = FuncionesController::consumirApi(self::$arUsuario->getEmpresaRel(), $parametrosSalarioPromedio, '/recursohumano/api/salarioPromedio');
        $salarioPromedioDosUltimosPagos = FuncionesController::consumirApi(self::$arUsuario->getEmpresaRel(), $parametrosSalarioPromedioDosUltimosPago, '/recursohumano/api/salarioPromedio');

        $pdf->SetFont('Arial', '', 11);

        if (!is_null($arFormato)) {
            $arImagenes = self::$em->getRepository(FormatoImagen::class)->findBy(['codigoFormatoFk' => $arFormato->getCodigoFormatoPk()]);
            foreach ($arImagenes as $arImagen) {
                if ($arImagen->getImagen()) {
                    if ($arImagen->getExtension()) {
                        $pdf->Image("data:image/'{$arImagen->getExtension()}';base64," . base64_encode(stream_get_contents($arImagen->getImagen())), $arImagen->getPosicionX(), $arImagen->getPosicionY(), $arImagen->getAncho(), $arImagen->getAlto(), $arImagen->getExtension());
                    }
                }
            }
            $cadena = $arFormato->getContenido();
            $patron1 = '/#1/';
            $patron2 = '/#2/';
            $patron3 = '/#3/';
            $patron4 = '/#4/';
            $patron5 = '/#5/';
            $patron6 = '/#6/';
            $patron7 = '/#7/';
            $patron8 = '/#8/';
            $patron9 = '/#9/';
            $patron10 = '/#a/';
            $patron11 = '/#b/';
            $patron12 = '/#c/';
            $patron13 = '/#d/';
            $patron14 = '/#e/';
            $patron15 = '/#f/';
            $patron16 = '/#g/';
            $patron17 = '/#h/';

            $cadenaCambiada = preg_replace($patron1, $arContrato->nombreEmpleado, $cadena);
            $cadenaCambiada = preg_replace($patron2, $arContrato->numeroIdentificacion, $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron3, strftime("%d de " . $this->MesesEspañol($fechaDesde->format('m')) . " de %Y", strtotime($fechaDesde->format('Y/m/d'))), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron4, $fechaHasta->format('Y-m-d'), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron5, $arContrato->contratoTipo, $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron6, $arContrato->nombreCargo, $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron7, number_format($arContrato->vrSalario, 0, '.', ','), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron8, strftime("%d días de " . $this->MesesEspañol($fechaActual->format('m')) . " de %Y", strtotime($fechaActual->format('Y/m/d'))), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron9, $salarioLetras, $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron10, number_format($arContrato->vrDevengadoPactado, 0, '.', ','), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron11, number_format($salarioPromedio, 0, '.', ','), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron12, number_format($salarioPromedioDosUltimosPagos, 0, '.', ','), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron13, number_format($arContrato->vrAuxilioTransporte, 0, '.', ','), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron14, $arContrato->costoClase, $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron15, number_format($arContrato->vrAdicional, 0, '.', ','), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron16, $arContrato->identificacionTipo, $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron17, strftime("%d de " . $this->MesesEspañol($fechaActual->format('m')) . " de %Y", strtotime($fechaActual->format('Y/m/d'))), $cadenaCambiada);
            $pdf->MultiCell(0, 5, utf8_decode($cadenaCambiada));
            $pdf->ln(30);
            if ($arFormato->getNombreFirma()) {
                $pdf->Cell(70, 4, "", 'B', 0, 'L');
                $pdf->ln(8);
                $pdf->Cell(70, 4, utf8_decode($arFormato->getNombreFirma()), 0, 0, 'L');
                $pdf->ln(5);
                $pdf->Cell(70, 4, utf8_decode($arFormato->getCargoFirma()), 0, 0, 'L');
            }
            $pdf->Ln();
        } else {
            $pdf->MultiCell(0, 5, utf8_decode('No se cuenta con un formato, por favor contactar son soporte técnico, para asesoría de como crearlo'));
        }

    }

    public function Footer()
    {
        $this->Text(170, 290, utf8_decode('Página ') . $this->PageNo() . ' de {nb}');
    }

    public static function MesesEspañol($mes)
    {

        if ($mes == '01') {
            $mesEspañol = "Enero";
        }
        if ($mes == '02') {
            $mesEspañol = "Febrero";
        }
        if ($mes == '03') {
            $mesEspañol = "Marzo";
        }
        if ($mes == '04') {
            $mesEspañol = "Abril";
        }
        if ($mes == '05') {
            $mesEspañol = "Mayo";
        }
        if ($mes == '06') {
            $mesEspañol = "Junio";
        }
        if ($mes == '07') {
            $mesEspañol = "Julio";
        }
        if ($mes == '08') {
            $mesEspañol = "Agosto";
        }
        if ($mes == '09') {
            $mesEspañol = "Septiembre";
        }
        if ($mes == '10') {
            $mesEspañol = "Octubre";
        }
        if ($mes == '11') {
            $mesEspañol = "Noviembre";
        }
        if ($mes == '12') {
            $mesEspañol = "Diciembre";
        }

        return $mesEspañol;
    }

    public function numtoletras($xcifra)
    {
        self::$em;
        $xarray = array(0 => "Cero",
            1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
            "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
            "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
            100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
//
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $xdecimales = "00";
        if (!($xpos_punto === false)) {
            if ($xpos_punto == 0) {
                $xcifra = "0" . $xcifra;
                $xpos_punto = strpos($xcifra, ".");
            }
            $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
            $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }

        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
        $xcadena = "";
        for ($xz = 0; $xz < 3; $xz++) {
            $xaux = substr($XAUX, $xz * 6, 6);
            $xi = 0;
            $xlimite = 6; // inicializo el contador de centenas xi y establezco el límite a 6 dígitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While
            while ($xexit) {
                if ($xi == $xlimite) { // si ya llegó al límite máximo de enteros
                    break; // termina el ciclo
                }

                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres dígitos)
                for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                    switch ($xy) {
                        case 1: // checa las centenas
                            if (substr($xaux, 0, 3) < 100) { // si el grupo de tres dígitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas
                            } else {
                                $key = (int)substr($xaux, 0, 3);
                                if (TRUE === array_key_exists($key, $xarray)) {  // busco si la centena es número redondo (100, 200, 300, 400, etc..)
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Millón, Millones, Mil o nada)
                                    if (substr($xaux, 0, 3) == 100)
                                        $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                } else { // entra aquí si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                    $key = (int)substr($xaux, 0, 1) * 100;
                                    $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                            break;
                        case 2: // checa las decenas (con la misma lógica que las centenas)
                            if (substr($xaux, 1, 2) < 10) {

                            } else {
                                $key = (int)substr($xaux, 1, 2);
                                if (TRUE === array_key_exists($key, $xarray)) {
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux);
                                    if (substr($xaux, 1, 2) == 20)
                                        $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3;
                                } else {
                                    $key = (int)substr($xaux, 1, 1) * 10;
                                    $xseek = $xarray[$key];
                                    if (20 == substr($xaux, 1, 1) * 10)
                                        $xcadena = " " . $xcadena . " " . $xseek;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)
                            break;
                        case 3: // checa las unidades
                            if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada
                            } else {
                                $key = (int)substr($xaux, 2, 1);
                                $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                $xsub = $this->subfijo($xaux);
                                $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                            break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO

            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena .= " DE";

            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena .= " DE";

            // ----------- esta línea la puedes cambiar de acuerdo a tus necesidades o a tu país -------
            if (trim($xaux) != "") {
                switch ($xz) {
                    case 0:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena .= "UN BILLON ";
                        else
                            $xcadena .= " BILLONES ";
                        break;
                    case 1:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena .= "UN MILLON ";
                        else
                            $xcadena .= " MILLONES ";
                        break;
                    case 2:
                        if ($xcifra < 1) {
                            $xcadena = "CERO PESOS M/C";
                        }
                        if ($xcifra >= 1 && $xcifra < 2) {
                            $xcadena = "UN PESO M/C ";
                        }
                        if ($xcifra >= 2) {
                            $xcadena .= " PESOS M/C "; //
                        }
                        break;
                } // endswitch ($xz)
            } // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para México se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR ($xz)
        return trim($xcadena);
    }

    public function subfijo($xx)
    { // esta función regresa un subfijo para la cifra
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
            $xsub = "";
        //
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
            $xsub = "MIL";
        //
        return $xsub;
    }
}