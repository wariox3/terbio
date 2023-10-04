<?php

namespace App\Entity;

use App\Repository\FormatoTipoRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: FormatoTipoRepository::class)]
class FormatoTipo
{

    #[ORM\Id]
    #[ORM\Column(type: "string", name: "codigo_formato_tipo_pk", length: 10)]
    private $codigoFormatoTipoPk;


    #[ORM\Column(type: "string", name: "nombre", length: 10, nullable: true)]
    private $nombre;


    #[ORM\OneToMany(targetEntity: Formato::class, mappedBy: "formatoTipoRel")]
    protected $formatosFormatoTipoRel;

    /**
     * @return mixed
     */
    public function getCodigoFormatoTipoPk()
    {
        return $this->codigoFormatoTipoPk;
    }

    /**
     * @param mixed $codigoFormatoTipoPk
     */
    public function setCodigoFormatoTipoPk($codigoFormatoTipoPk): void
    {
        $this->codigoFormatoTipoPk = $codigoFormatoTipoPk;
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
    public function getFormatosFormatoTipoRel()
    {
        return $this->formatosFormatoTipoRel;
    }

    /**
     * @param mixed $formatosFormatoTipoRel
     */
    public function setFormatosFormatoTipoRel($formatosFormatoTipoRel): void
    {
        $this->formatosFormatoTipoRel = $formatosFormatoTipoRel;
    }


}
