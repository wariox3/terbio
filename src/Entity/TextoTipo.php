<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TextoTipoRepository")
 */
class TextoTipo
{
    /**
     * @ORM\Id
     * @ORM\Column(name="codigo_texto_tipo_pk", type="string", length=10)
     */
    private $codigoTextoTipoPk;

    /**
     * @ORM\Column(name="descripcion", type="string", length=300, nullable=true)
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Texto", mappedBy="textoTipoRel")
     */
    protected $textosTextoTipoRel;

    /**
     * @return mixed
     */
    public function getCodigoTextoTipoPk()
    {
        return $this->codigoTextoTipoPk;
    }

    /**
     * @param mixed $codigoTextoTipoPk
     */
    public function setCodigoTextoTipoPk($codigoTextoTipoPk): void
    {
        $this->codigoTextoTipoPk = $codigoTextoTipoPk;
    }

    /**
     * @return mixed
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @param mixed $descripcion
     */
    public function setDescripcion($descripcion): void
    {
        $this->descripcion = $descripcion;
    }

}