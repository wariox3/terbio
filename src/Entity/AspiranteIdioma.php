<?php


namespace App\Entity;

use App\Repository\AspiranteIdiomaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AspiranteIdiomaRepository::class)]
class AspiranteIdioma
{

    #[ORM\Id]
    #[ORM\Column(name: "codigo_aspirante_idioma_pk", type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private $codigoAspiranteIdiomaPk;


    #[ORM\Column(name: "codigo_usuario_fk", type: "string", nullable: false)]
    private $codigoUsuarioFk;


    #[ORM\Column(name: "codigo_idioma_fk", type: "string", nullable: true)]
    private $codigoIdiomaFk;


    #[ORM\Column(name: "nivel", type: "string", length: 150, nullable: false)]
    #[Assert\Length(max: 150, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $nivel;


    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "usuarioAspiranteIdiomaRel")]
    #[ORM\JoinColumn(name: "codigo_usuario_fk", referencedColumnName: "codigo_usuario_pk")]
    private $usuarioRel;


    #[ORM\ManyToOne(targetEntity: Idioma::class, inversedBy: "idiomoAspiranteIdiomaRel")]
    #[ORM\JoinColumn(name: "codigo_idioma_fk", referencedColumnName: "codigo_idioma_pk")]
    private $idiomaRel;

    /**
     * @return mixed
     */
    public function getCodigoAspiranteIdiomaPk()
    {
        return $this->codigoAspiranteIdiomaPk;
    }

    /**
     * @param mixed $codigoAspiranteIdiomaPk
     */
    public function setCodigoAspiranteIdiomaPk($codigoAspiranteIdiomaPk): void
    {
        $this->codigoAspiranteIdiomaPk = $codigoAspiranteIdiomaPk;
    }

    /**
     * @return mixed
     */
    public function getCodigoUsuarioFk()
    {
        return $this->codigoUsuarioFk;
    }

    /**
     * @param mixed $codigoUsuarioFk
     */
    public function setCodigoUsuarioFk($codigoUsuarioFk): void
    {
        $this->codigoUsuarioFk = $codigoUsuarioFk;
    }

    /**
     * @return mixed
     */
    public function getCodigoIdiomaFk()
    {
        return $this->codigoIdiomaFk;
    }

    /**
     * @param mixed $codigoIdiomaFk
     */
    public function setCodigoIdiomaFk($codigoIdiomaFk): void
    {
        $this->codigoIdiomaFk = $codigoIdiomaFk;
    }

    /**
     * @return mixed
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * @param mixed $nivel
     */
    public function setNivel($nivel): void
    {
        $this->nivel = $nivel;
    }

    /**
     * @return mixed
     */
    public function getUsuarioRel()
    {
        return $this->usuarioRel;
    }

    /**
     * @param mixed $usuarioRel
     */
    public function setUsuarioRel($usuarioRel): void
    {
        $this->usuarioRel = $usuarioRel;
    }

    /**
     * @return mixed
     */
    public function getIdiomaRel()
    {
        return $this->idiomaRel;
    }

    /**
     * @param mixed $idiomaRel
     */
    public function setIdiomaRel($idiomaRel): void
    {
        $this->idiomaRel = $idiomaRel;
    }


}