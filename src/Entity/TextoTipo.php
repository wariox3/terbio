<?php


namespace App\Entity;

use App\Repository\TextoTipoRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: TextoTipoRepository::class)]
class TextoTipo
{

    #[ORM\Column(type: "string", name: "codigo_texto_tipo_pk", length: 10)]
    #[ORM\Id]
    private $codigoTextoTipoPk;


    #[ORM\Column(name: "descripcion", type: "string", length: 300, nullable: true)]
    private $descripcion;


    #[ORM\OneToMany(targetEntity: Texto::class, mappedBy: "textoTipoRel")]
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