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
        $xml = "";
        return $xml;
    }

}
