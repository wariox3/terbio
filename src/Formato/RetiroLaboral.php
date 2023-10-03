<?php


namespace App\Formato;


use App\Controller\FuncionesController;
use App\Entity\Formato;
use App\Entity\FormatoImagen;

class RetiroLaboral extends \FPDF
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
        $pdf->Output("RetiroLaboral.pdf", 'D');
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

        $fechaActual = $dateNow = new \DateTime('now');

        $parametrosContrato = ['codigoContrato' => $codigoContrato];
        $arrContrato = FuncionesController::consumirApi(self::$arUsuario->getEmpresaRel(), $parametrosContrato, "/recursohumano/api/contrato/terminado/detalle");
        $arContrato = $arrContrato[0];
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetXY(25, 36);
        $fecha = strftime("%d de " . $this->MesesEspañol($fechaActual->format('m')) . " de %Y", strtotime($fechaActual->format('Y/m/d')));
        $ciudad = ucfirst(strtolower($arContrato->ciudad));

        $pdf->Cell(8, 10, "$ciudad,  $fecha");
        $pdf->Ln(24);
        $fechaDesde = date_create($arContrato->fechaDesde);
        $fechaHasta = date_create($arContrato->fechaHasta);
        $arFormato = self::$em->getRepository(Formato::class)->findOneBy(array('codigoFormatoTipoFk' => "RL", 'codigoEmpresaFk' => self::$arUsuario->getCodigoEmpresaFk()));
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
            $cadenaCambiada = preg_replace($patron1, $arContrato->nombreEmpleado, $cadena);
            $cadenaCambiada = preg_replace($patron2, $arContrato->numeroIdentificacion, $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron3, strftime("%d de " . $this->MesesEspañol($fechaDesde->format('m')) . " de %Y", strtotime($fechaDesde->format('Y/m/d'))), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron4, strftime("%d de " . $this->MesesEspañol($fechaHasta->format('m')) . " de %Y", strtotime($fechaHasta->format('Y/m/d'))), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron5, $arContrato->contratoTipo, $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron6, $arContrato->nombreCargo, $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron7, utf8_decode($arContrato->pension), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron8, utf8_decode($arContrato->salud), $cadenaCambiada);
            $cadenaCambiada = preg_replace($patron9, strftime("%d de " . $this->MesesEspañol($fechaActual->format('m')) . " de %Y", strtotime($fechaActual->format('Y/m/d'))), $cadenaCambiada);
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

    public static function mesesEspañol($mes)
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
}