<?php


namespace App\Entity;

use App\Repository\IncapacidadTipoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: IncapacidadTipoRepository::class)]
class IncapacidadTipo
{

    #[ORM\Id]
    #[ORM\Column(type:"string", name:"codigo_incapacidad_tipo_pk", length: 10)]
    #[Assert\Length(max: 10, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $codigoIncapacidadTipoPk;


    #[ORM\Column(name:"nombre", type:"string", length: 80, nullable: true)]
    #[Assert\Length(max: 80, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $nombre;


    #[ORM\OneToMany(targetEntity: Incapacidad::class, mappedBy: "incapacidadTipoRel")]
    protected $incapacidadesIncapacidadTipoRel;

    /**
     * @return mixed
     */
    public function getCodigoIncapacidadTipoPk()
    {
        return $this->codigoIncapacidadTipoPk;
    }

    /**
     * @param mixed $codigoIncapacidadTipoPk
     */
    public function setCodigoIncapacidadTipoPk($codigoIncapacidadTipoPk): void
    {
        $this->codigoIncapacidadTipoPk = $codigoIncapacidadTipoPk;
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
    public function getIncapacidadesIncapacidadTipoRel()
    {
        return $this->incapacidadesIncapacidadTipoRel;
    }

    /**
     * @param mixed $incapacidadesIncapacidadTipoRel
     */
    public function setIncapacidadesIncapacidadTipoRel($incapacidadesIncapacidadTipoRel): void
    {
        $this->incapacidadesIncapacidadTipoRel = $incapacidadesIncapacidadTipoRel;
    }
}