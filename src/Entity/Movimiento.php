<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MovimientoType
 * @ORM\Entity(repositoryClass="App\Repository\MovimientoRepository")
 */
class Movimiento
{
    /**
     * @ORM\Id
     * @ORM\Column(name="codigo_movimiento_pk", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $codigoMovimientoPk;

    /**
     * @ORM\Column(name="codigo_empresa_fk", type="string",length=10, nullable=true)
     */
    private $codigoEmpresaFk;

    /**
     * @ORM\Column(name="resolucion_numero", type="string",length=20, nullable=true)
     */
    private $resolucionNumero;

    /**
     * @ORM\Column(name="resolucion_fecha_desde", type="string",length=20, nullable=true)
     */
    private $resolucionFechaDesde;

    /**
     * @ORM\Column(name="resolucion_desde_hasta", type="string",length=20, nullable=true)
     */
    private $resolucionDesdeHasta;

    /**
     * @ORM\Column(name="resolucion_prefijo", type="string",length=20, nullable=true)
     */
    private $resolucionPrefijo;

    /**
     * @ORM\Column(name="resolucion_desde", type="string",length=20, nullable=true)
     */
    private $resolucionDesde;

    /**
     * @ORM\Column(name="resolucion_hasta", type="string",length=20, nullable=true)
     */
    private $resolucionHasta;

    /**
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\Column(name="numero", type="integer", nullable=true)
     */
    private $numero = 0;

    /**
     * @return mixed
     */
    public function getCodigoMovimientoPk()
    {
        return $this->codigoMovimientoPk;
    }

    /**
     * @param mixed $codigoMovimientoPk
     */
    public function setCodigoMovimientoPk($codigoMovimientoPk): void
    {
        $this->codigoMovimientoPk = $codigoMovimientoPk;
    }

    /**
     * @return mixed
     */
    public function getCodigoEmpresaFk()
    {
        return $this->codigoEmpresaFk;
    }

    /**
     * @param mixed $codigoEmpresaFk
     */
    public function setCodigoEmpresaFk($codigoEmpresaFk): void
    {
        $this->codigoEmpresaFk = $codigoEmpresaFk;
    }

    /**
     * @return mixed
     */
    public function getResolucionNumero()
    {
        return $this->resolucionNumero;
    }

    /**
     * @param mixed $resolucionNumero
     */
    public function setResolucionNumero($resolucionNumero): void
    {
        $this->resolucionNumero = $resolucionNumero;
    }

    /**
     * @return mixed
     */
    public function getResolucionFechaDesde()
    {
        return $this->resolucionFechaDesde;
    }

    /**
     * @param mixed $resolucionFechaDesde
     */
    public function setResolucionFechaDesde($resolucionFechaDesde): void
    {
        $this->resolucionFechaDesde = $resolucionFechaDesde;
    }

    /**
     * @return mixed
     */
    public function getResolucionDesdeHasta()
    {
        return $this->resolucionDesdeHasta;
    }

    /**
     * @param mixed $resolucionDesdeHasta
     */
    public function setResolucionDesdeHasta($resolucionDesdeHasta): void
    {
        $this->resolucionDesdeHasta = $resolucionDesdeHasta;
    }

    /**
     * @return mixed
     */
    public function getResolucionPrefijo()
    {
        return $this->resolucionPrefijo;
    }

    /**
     * @param mixed $resolucionPrefijo
     */
    public function setResolucionPrefijo($resolucionPrefijo): void
    {
        $this->resolucionPrefijo = $resolucionPrefijo;
    }

    /**
     * @return mixed
     */
    public function getResolucionDesde()
    {
        return $this->resolucionDesde;
    }

    /**
     * @param mixed $resolucionDesde
     */
    public function setResolucionDesde($resolucionDesde): void
    {
        $this->resolucionDesde = $resolucionDesde;
    }

    /**
     * @return mixed
     */
    public function getResolucionHasta()
    {
        return $this->resolucionHasta;
    }

    /**
     * @param mixed $resolucionHasta
     */
    public function setResolucionHasta($resolucionHasta): void
    {
        $this->resolucionHasta = $resolucionHasta;
    }

    /**
     * @return mixed
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param mixed $fecha
     */
    public function setFecha($fecha): void
    {
        $this->fecha = $fecha;
    }

    /**
     * @return mixed
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param mixed $numero
     */
    public function setNumero($numero): void
    {
        $this->numero = $numero;
    }



}
