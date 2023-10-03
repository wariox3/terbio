<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EnlaceRepository")
 */
class Enlace
{
    /**
     * @ORM\Id
     * @ORM\Column(name="codigo_enlace_pk", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $codigoEnlacePk;

    /**
     * @ORM\Column(name="codigo_empresa_fk", type="integer", nullable=true)
     */
    private $codigoEmpresaFk;

    /**
     * @ORM\Column(name="nombre", type="string", length=500, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\Column(name="enlace", type="string", length=500, nullable=true)
     */
    private $enlace;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Empresa", inversedBy="enlacesEmpresaRel")
     * @ORM\JoinColumn(name="codigo_empresa_fk",referencedColumnName="codigo_empresa_pk")
     */
    protected $empresaRel;

    /**
     * @return mixed
     */
    public function getCodigoEnlacePk()
    {
        return $this->codigoEnlacePk;
    }

    /**
     * @param mixed $codigoEnlacePk
     */
    public function setCodigoEnlacePk($codigoEnlacePk): void
    {
        $this->codigoEnlacePk = $codigoEnlacePk;
    }

    /**
     * @return mixed
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     */
    public function setNombre($nombre): void
    {
        $this->nombre = $nombre;
    }

    /**
     * @return mixed
     */
    public function getEnlace()
    {
        return $this->enlace;
    }

    /**
     * @param mixed $enlace
     */
    public function setEnlace($enlace): void
    {
        $this->enlace = $enlace;
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
    public function getEmpresaRel()
    {
        return $this->empresaRel;
    }

    /**
     * @param mixed $empresaRel
     */
    public function setEmpresaRel($empresaRel): void
    {
        $this->empresaRel = $empresaRel;
    }



}
