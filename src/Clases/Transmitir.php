<?php

namespace App\Clases;

use Doctrine\ORM\EntityManager;

class Transmitir
{

    /**
     * @var EntityManager
     */
    private $em = null;

    /**
     * WebServiceFacturaElectronica constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->inicializar();
    }

    private function inicializar()
    {

    }

    public function transferirMovimientos($arrMovimientos)
    {
        $em = $this->em;
        $xml = $this->generarXml($arrMovimientos);

    }

    private function generarXml($arrMovimientos) {
        $xmlDocumento = new \DOMDocument("1.0", "UTF-8");
        $xmlDocumento->xmlStandalone = false;

        $xmlElementoComprobantes = $xmlDocumento->createElement('Comprobantes');
        $xmlElementoComprobantes->setAttribute("schemaLocation", "http://www.dian.gov.co/contratos/facturaelectronica/v1");
        $xmlDocumento->appendChild($xmlElementoComprobantes);
        $xmlElementoComprobantes->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        foreach ($arrMovimientos as $arrMovimiento) {
            $xmlElementoComprobante = $xmlDocumento->createElement("Comprobante");
            $xmlElementoComprobante = $xmlElementoComprobantes->appendChild($xmlElementoComprobante);
            $tipoDocumento = 1; //1-Factura, 2-NotaCredito, 3-NotaDebito

            $xmlElementoInformacionOrganismo = $xmlDocumento->createElement("informacionOrganismo");
            $xmlElementoInformacionOrganismo = $xmlElementoComprobante->appendChild($xmlElementoInformacionOrganismo);
            if ($tipoDocumento == 1) {
                $xmlElementoMovimiento = $xmlDocumento->createElementNS("http://www.dian.gov.co/contratos/facturaelectronica/v1", "fe:Invoice");

            } elseif ($tipoDocumento == 2) {
                //$xmlElementoMovimiento = $xml->createElementNS("http://www.dian.gov.co/contratos/facturaelectronica/v1", "fe:CreditNote");

            } elseif ($tipoDocumento == 3) {
                //$xmlElementoMovimiento = $xml->createElementNS("http://www.dian.gov.co/contratos/facturaelectronica/v1", "fe:DebitNote");
            }
            $xmlElementoMovimiento = $xmlElementoInformacionOrganismo->appendChild($xmlElementoMovimiento);
            $xmlElementoMovimiento->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
            $xmlElementoMovimiento->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
            $xmlElementoMovimiento->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:clm54217', 'urn:un:unece:uncefact:codelist:specification:54217:2001');
            $xmlElementoMovimiento->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:clm66411', 'urn:un:unece:uncefact:codelist:specification:66411:2001');
            $xmlElementoMovimiento->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:clmIANAMIMEMediaType', 'urn:un:unece:uncefact:codelist:specification:IANAMIMEMediaType:2003');
            $xmlElementoMovimiento->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:ext', 'urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2');
            $xmlElementoMovimiento->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:qdt', 'urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2');
            $xmlElementoMovimiento->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:sts', 'http://www.dian.gov.co/contratos/facturaelectronica/v1/Structures');
            $xmlElementoMovimiento->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:udt', 'urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2');
            $xmlElementoMovimiento->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        }


        $xmlDocumento->formatOutput = true;
        $xmlPlano = $xmlDocumento->saveXML();

        return $xmlPlano;
    }

}
