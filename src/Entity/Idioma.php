<?php


namespace App\Entity;

use App\Repository\IdiomaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: IdiomaRepository::class)]
#[DoctrineAssert\UniqueEntity(fields: ["codigoIdiomaPk"], message: "El código de idioma ingresado ya se encuentra registrado")]
class Idioma
{

    #[ORM\Id]
    #[ORM\Column(type: "string", name: "codigo_idioma_pk", length: 10, nullable: false, unique: true)]
    #[Assert\Length(max: 10, maxMessage: "El campo no puede contener más de 10 caracteres")]
    private $codigoIdiomaPk;


    #[ORM\Column(name: "nombre", type: "string", length: 150, nullable: false)]
    #[Assert\Length(max: 150, maxMessage: "El campo no puede contener más de 150 caracteres")]
    private $nombre;


    #[ORM\OneToMany(targetEntity: AspiranteIdioma::class, mappedBy: "idiomaRel")]
    private $idiomoAspiranteIdiomaRel;


    /**
     * @return mixed
     */
    public function getCodigoIdiomaPk()
    {
        return $this->codigoIdiomaPk;
    }

    /**
     * @param mixed $codigoIdiomaPk
     */
    public function setCodigoIdiomaPk($codigoIdiomaPk): void
    {
        $this->codigoIdiomaPk = $codigoIdiomaPk;
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
    public function getIdiomoAspiranteIdiomaRel()
    {
        return $this->idiomoAspiranteIdiomaRel;
    }

    /**
     * @param mixed $idiomoAspiranteIdiomaRel
     */
    public function setIdiomoAspiranteIdiomaRel($idiomoAspiranteIdiomaRel): void
    {
        $this->idiomoAspiranteIdiomaRel = $idiomoAspiranteIdiomaRel;
    }



}