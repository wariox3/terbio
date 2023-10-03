<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IdentificacionRepository")
 */
class Identificacion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=3, nullable=false, unique=true)
     */
    private $codigoIdentificacionPk;

    /**
     * @ORM\Column(name="nombre", type="string", length=30, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Usuario", mappedBy="identificacionRel")
     */
    protected $usuariosIdentificacionRel;

    /**
     * @return mixed
     */
    public function getCodigoIdentificacionPk()
    {
        return $this->codigoIdentificacionPk;
    }

    /**
     * @param mixed $codigoIdentificacionPk
     */
    public function setCodigoIdentificacionPk($codigoIdentificacionPk): void
    {
        $this->codigoIdentificacionPk = $codigoIdentificacionPk;
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
    public function getUsuariosIdentificacionRel()
    {
        return $this->usuariosIdentificacionRel;
    }

    /**
     * @param mixed $usuariosIdentificacionRel
     */
    public function setUsuariosIdentificacionRel($usuariosIdentificacionRel): void
    {
        $this->usuariosIdentificacionRel = $usuariosIdentificacionRel;
    }



}
