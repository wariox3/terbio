<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\FormatoTipoRepository")
 */
class FormatoTipo
{

    /**
     * @ORM\Id
     * @ORM\Column(name="codigo_formato_tipo_pk", type="string", length=10)
     */
    private $codigoFormatoTipoPk;

    /**
     * @ORM\Column(name="nombre", type="string", length=50, nullable=true)
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity="Formato", mappedBy="formatoTipoRel")
     */
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
