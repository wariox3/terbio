<?php


namespace App\Formato;


use App\Entity\Empresa;
use App\Entity\Texto;
use phpDocumentor\Reflection\Type;

class EstadoCuentaFormal extends \FPDF
{
    public static $em;
    public static $arrRegistros;
    public static $arUsuario;
    public static $extension;

    public function Generar($em, $arrRegistros, $arUsuario)
    {
        ob_clean();
        //$em = $miThis->getDoctrine()->getManager();
        self::$em = $em;
        self::$arrRegistros = $arrRegistros;
        self::$arUsuario = $arUsuario;
        $pdf = new EstadoCuentaFormal();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times', '', 12);
        $this->Body($pdf);
        $pdf->Output("EstadoCuentaFormal.pdf", 'D');
    }

    public function Body($pdf)
    {
        $arEmpresa = self::$em->getRepository(Empresa::class)->find(self::$arUsuario->getCodigoEmpresaFk());
        $ciudad = $arEmpresa->getCiudad()?$arEmpresa->getCiudad():"";
        try {
            if ($arEmpresa) {
                $pdf->Image("data:image/'{$arEmpresa->getExtension()}';base64," . base64_encode(stream_get_contents($arEmpresa->getLogo())), 150, 20, 40, 40, $arEmpresa->getExtension());
            }
        } catch (\Exception $exception) {
        }

        $pdf->SetMargins(30, 5, 20);
        $pdf->SetFont('Arial', '', 10);
        setlocale(LC_TIME, 'es_ES.UTF-8');
        $pdf->Text(30, 35, utf8_decode( $ciudad. ',' . ' ' . strftime("%d de %B de %Y", strtotime(date('Y-m-d')))));

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Text(30, 50, utf8_decode("Señores:"));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(29, 52);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY(30, 115);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Text(30, 80, utf8_decode("ASUNTO: ESTADO DE CUENTA"));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(30, 95, utf8_decode("Cordial saludo:"));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(30, 104, utf8_decode("Con el fin de mejorar la comunicación con nuestros clientes y garantizar el cruce de información"));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(30, 109, utf8_decode("correspondiente a su cartera, nos permitimos detallar las facturas pendientes por cancelar a la fecha:"));

        $pdf->SetXY(30, 116);
        $header = array('TIPO', 'NÚMERO', 'FECHA', 'VENCE', 'PLAZO', 'VALOR');
        $pdf->SetFillColor(170, 170, 170);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(.2);
        $pdf->SetFont('arial', 'B', 7);
        //creamos la cabecera de la tabla.
        $w = array(50, 20, 20, 20, 20, 20);

        for ($i = 0; $i < count($header); $i++) {
            if ($i == 0 || $i == 1)
                $pdf->Cell($w[$i], 4, utf8_decode($header[$i]), 1, 0, 'C', 1);
            else
                $pdf->Cell($w[$i], 4, $header[$i], 1, 0, 'C', 1);
            //Restauración de colores y fuentes
        }

        $pdf->SetFont('Arial', '', 7);
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetXY(30, 120);
        $primerCliente = true;
        $totalSaldos = 0;
        foreach (self::$arrRegistros as $arCuentaCobrar) {
            $fecha = new \DateTime($arCuentaCobrar->fecha);
            $fechaVence = new \DateTime($arCuentaCobrar->fechaVence);
            $totalSaldos += $arCuentaCobrar->vrSaldo;

            if ($primerCliente) {
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->SetXY(29, 52);
                $pdf->Cell(20, 3, utf8_decode($arCuentaCobrar->nombreCorto), 0, 0, 'L', 0);
                $pdf->SetXY(29, 56);
                $pdf->Cell(20, 3, 'NIT:' . ' ' . $arCuentaCobrar->numeroIdentificacion, 0, 0, 'L', 0);
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetXY(30, 116);
                $primerCliente = false;
                $cliente = $arCuentaCobrar->codigoTercero;
                $pdf->Ln(4);
            }
            if ($arCuentaCobrar->codigoTercero != $cliente) {
                $pdf->Cell(105, 4, $arCuentaCobrar->nombreCorto, 1, 0, 'L');
                $cliente = $arCuentaCobrar->codigoTercero;
                $pdf->Ln(4);
            }

            $pdf->SetFont('Arial', '', 8.5);
            $pdf->Cell(50, 4, $arCuentaCobrar->tipo, 'LRB', 0, 'L');
            $pdf->Cell(20, 4, $arCuentaCobrar->numeroDocumento, 'LRB', 0, 'L');
            $pdf->Cell(20, 4, $fecha->format('Y-m-d'), 'LRB', 0, 'L');
            $pdf->Cell(20, 4, $fechaVence->format('Y-m-d'), 'LRB', 0, 'L');
            $pdf->Cell(20, 4, $arCuentaCobrar->plazo, 'LRB', 0, 'L');
            $pdf->Cell(20, 4, number_format($arCuentaCobrar->vrSaldo), 'LRB', 0, 'R');

            $pdf->ln();
        }
        $pdf->SetFont('Arial', '', 8.5);
        $pdf->SetX(140);
        $pdf->Cell(20, 4,"Total", 'LRB', 0, 'L');
        $pdf->Cell(20, 4, number_format($totalSaldos), 'LRB', 0, 'R');
        $pdf->ln(8);
        $arTexto = self::$em->getRepository(Texto::class)->findOneBy([
            'codigoEmpresaFk'=>self::$arUsuario->getCodigoEmpresaFk(),
            'codigoTextoTipoFk'=>1
        ]);
        if($arTexto){
            $pdf->MultiCell(150, 5, utf8_decode($arTexto->getTexto()), 0, "J");
        }

        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(30, 229, utf8_decode("En caso de tener alguna observación con la información anexada favor comunicarse a nuestro"));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(30, 234, utf8_decode('departamento de cartera, tel:' . ' ' . $arEmpresa->getTelefono() . ' ' . 'para actualizar dicha información.'));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(30, 245, utf8_decode("Atentamente,"));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Text(30, 254, utf8_decode($arEmpresa->getNombre() ), 0, 0, 'L', 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Text(30, 260, utf8_decode("Departamento de Cartera"));

    }
}