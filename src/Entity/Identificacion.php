<?php

namespace App\Entity;

use App\Repository\IdentificacionRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: IdentificacionRepository::class)]
class Identificacion
{

    #[ORM\Id]
    #[ORM\Column(type: "string", name: "codigo_identificacion_pk", length: 3, nullable: false, unique: true)]
    private $codigoIdentificacionPk;


    #[ORM\Column(name: "nombre", type: "string", length: 30, nullable: true)]
    private $nombre;


    #[ORM\OneToMany(targetEntity: Usuario::class, mappedBy: "identificacionRel")]
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
