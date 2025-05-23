<?php


namespace App\Formato;

use App\Controller\FuncionesController;
use App\Entity\Empresa;
use App\Entity\Usuario;
use Doctrine\ORM\EntityRepository;

class Guias extends \FPDF
{
    public static $em;
    public static $codigoPago;
    public static $codigoEmpresa;
    public static $arUsuario;
    public static $codigoRol;

    public function Generar($em, $usuario, $arrGuias)
    {
        self::$em = $em;
        self::$arUsuario = $usuario;
        self::$codigoRol = $usuario->getCodigoRolFk();
        self::$codigoEmpresa = $usuario->getCodigoEmpresaFk();
        $pdf = new Guias();
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont('Times', '', 12);
        $pdf->Header();
        $this->Body($pdf, $arrGuias);
        $pdf->Output("guias.pdf", 'D');
    }

    public function Header()
    {
        $arEmpresa = self::$em->getRepository(Empresa::class)->find(self::$codigoEmpresa);
        $date = new \DateTime('now');
        $this->SetFont('Arial', '', 5);
        $this->Text(168, 8, $date->format('Y-m-d H:i:s') . ' [Semantica | ERP]');
        $this->SetFillColor(200, 200, 200);
        $this->SetFont('Arial', 'B', 10);
        //Logo
        $this->SetXY(53, 10);
        try {
            if ($arEmpresa) {
                $this->Image("data:image/'{$arEmpresa->getExtension()}';base64," . base64_encode(stream_get_contents($arEmpresa->getLogo())), 10, 5, 40, 35, $arEmpresa->getExtension());
            }
        } catch (\Exception $exception) {
        }
        $this->Cell(147, 7, "RELACION GUIAS", 0, 0, 'C', 1); //$this->Cell(150, 7, utf8_decode("COMPROBANTE PAGO ". $arPago->getPagoTipoRel()->getNombre().""), 0, 0, 'C', 1);
        $this->SetXY(53, 18);
        $this->SetFont('Arial', 'B', 9);
        $this->Cell(20, 4, "EMPRESA:", 0, 0, 'L', 1);
        $this->Cell(100, 4, utf8_decode($arEmpresa->getNombre()), 0, 0, 'L', 0);
        $this->SetXY(53, 22);
        $this->Cell(20, 4, "NIT:", 0, 0, 'L', 1);
        $this->Cell(100, 4, $arEmpresa->getNit() . '-' . $arEmpresa->getDigitoVerificacion(), 0, 0, 'L', 0);
        $this->SetXY(53, 26);
        $this->Cell(20, 4, utf8_decode("DIRECCIÓN:"), 0, 0, 'L', 1);
        $this->Cell(100, 4, substr(utf8_decode($arEmpresa->getDireccion()), 0, 45), 0, 0, 'L', 0);
        $this->SetXY(53, 30);
        $this->Cell(20, 4, utf8_decode("TELÉFONO:"), 0, 0, 'L', 1);
        $this->Cell(100, 4, $arEmpresa->getTelefono(), 0, 0, 'L', 0);
        $this->SetXY(53, 34);
        $this->Cell(20, 4, utf8_decode("CLIENTE:"), 0, 0, 'L', 1);
        $this->Cell(100, 4, self::$arUsuario->getNombres(), 0, 0, 'L', 0);

        $this->EncabezadoDetalles();
    }

    public function EncabezadoDetalles()
    {
        $this->SetXY(10, 55);
            $header = array('GUIA', 'TIPO', 'FECHA', 'DOCUMENTO','ORIGEN', 'DESTINO', 'UND', 'PESO', 'VOL', 'TOTAL');
        $this->SetFillColor(200, 200, 200);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.2);
        $this->SetFont('', 'B', 7);

        //Creamos la cabecera de la tabla.
        $w = array(15, 18, 17, 20, 30, 40, 10, 10, 10, 20);
        for ($i = 0; $i < count($header); $i++)
            if ($i == 0 || $i == 1) {
                $this->Cell($w[$i], 4, $header[$i], 1, 0, 'L', 1);
            } else {
                $this->Cell($w[$i], 4, $header[$i], 1, 0, 'C', 1);
            }

        //Restauración de colores y fuentes
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        $this->Ln(4);
    }

    public function Body($pdf, $arrGuias)
    {
        $guias = 0;
        $unidades = 0;
        $peso = 0;
        $volumen = 0;
        $total = 0;
        foreach ($arrGuias as $arGuia) {
            $fechaIngreso = date_create($arGuia->fechaIngreso);
            $pdf->Cell(15, 4, $arGuia->codigoGuiaPk, 1, 0, 'L');
            $pdf->Cell(18, 4, substr($arGuia->guiaTipoNombre, 0, 8), 1, 0, 'L');
            $pdf->Cell(17, 4, $fechaIngreso->format('Y-m-d'), 1, 0, 'L');
            $pdf->Cell(20, 4, $arGuia->documentoCliente, 1, 0, 'L');
            $pdf->Cell(30, 4, utf8_decode($arGuia->ciudadOrigen), 1, 0, 'L');
            $pdf->Cell(40, 4, utf8_decode($arGuia->ciudadDestino), 1, 0, 'L');
            $pdf->Cell(10, 4, number_format($arGuia->unidades), 1, 0, 'R');
            $pdf->Cell(10, 4, number_format($arGuia->pesoReal), 1, 0, 'R');
            $pdf->Cell(10, 4, number_format($arGuia->pesoVolumen), 1, 0, 'R');
            $pdf->Cell(20, 4, number_format($arGuia->vrFlete + $arGuia->vrManejo), 1, 0, 'R');
            $pdf->Ln();
            $pdf->SetAutoPageBreak(true, 15);
            $unidades += $arGuia->unidades;
            $peso += $arGuia->pesoReal;
            $volumen += $arGuia->pesoVolumen;
            $total += $arGuia->vrFlete + $arGuia->vrManejo;
            $guias++;
        }
        $pdf->Cell(140, 4, "TOTAL: $guias guias", 1, 0, 'L');
        $pdf->Cell(10, 4, number_format($unidades), 1, 0,'R');
        $pdf->Cell(10, 4, number_format($peso), 1, 0,'R');
        $pdf->Cell(10, 4, number_format($volumen), 1, 0,'R');
        $pdf->Cell(20, 4, number_format($total), 1, 0,'R');
        $pdf->Ln();
        $pdf->SetAutoPageBreak(true, 15);

    }

    public function Footer()
    {

    }

}