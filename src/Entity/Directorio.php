<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DirectorioRepository")
 */
class Directorio
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $codigoDirectorioPk;

    /**
     * @ORM\Column(name="tipo", type="string", length=1, nullable=true)
     * @Assert\Length(
     *     max = 1,
     *     maxMessage="El campo no puede contener más de 1 caracteres"
     * )
     */
    private $tipo;

    /**
     * @ORM\Column(name="clase", type="string", length=50, nullable=true)
     * @Assert\Length(
     *     max = 50,
     *     maxMessage="El campo no puede contener más de 50 caracteres"
     * )
     */
    private $clase;

    /**
     * @Assert\Regex(
     *     pattern     = "/^[a-z]+$/i",
     *     htmlPattern = "^[a-zA-Z]+$"
     * )
     * @ORM\Column(name="nombre", type="string", length=50, nullable=true)
     * @Assert\Length(
     *     max = 50,
     *     maxMessage="El campo no puede contener más de 50 caracteres"
     * )
     */
    private $nombre;

    /**
     * @ORM\Column(name="codigo_masivo_tipo_fk", type="string", length=20, nullable=true)
     */
    private $codigoMasivoTipoFk;

    /**
     * @ORM\Column(name="codigo_directorio_padre_fk", type="integer", nullable=true)
     */
    private $codigoDirectorioPadreFk;

    /**
     * @ORM\Column(name="directorio", type="integer", options={"default" : 0})
     */
    private $directorio = 0;

    /**
     * @ORM\Column(name="numero_archivos", type="integer", options={"default" : 0})
     */
    private $numeroArchivos = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Directorio", inversedBy="directorioDirectoriosRel")
     * @ORM\JoinColumn(name="codigo_directorio_padre_fk",referencedColumnName="codigo_directorio_pk")
     */
    protected $directorioRel;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Archivo", mappedBy="directorioRel")
     */
    protected $archivoDirectoriosRel;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Directorio", mappedBy="directorioRel")
     */
    protected $directorioDirectoriosRel;

    /**
     * @return array
     */
    public function getInfoLog(): array
    {
        return $this->infoLog;
    }

    /**
     * @param array $infoLog
     */
    public function setInfoLog(array $infoLog): void
    {
        $this->infoLog = $infoLog;
    }

    /**
     * @return mixed
     */
    public function getCodigoDirectorioPk()
    {
        return $this->codigoDirectorioPk;
    }

    /**
     * @param mixed $codigoDirectorioPk
     */
    public function setCodigoDirectorioPk($codigoDirectorioPk): void
    {
        $this->codigoDirectorioPk = $codigoDirectorioPk;
    }

    /**
     * @return mixed
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @param mixed $tipo
     */
    public function setTipo($tipo): void
    {
        $this->tipo = $tipo;
    }

    /**
     * @return mixed
     */
    public function getClase()
    {
        return $this->clase;
    }

    /**
     * @param mixed $clase
     */
    public function setClase($clase): void
    {
        $this->clase = $clase;
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
    public function getCodigoMasivoTipoFk()
    {
        return $this->codigoMasivoTipoFk;
    }

    /**
     * @param mixed $codigoMasivoTipoFk
     */
    public function setCodigoMasivoTipoFk($codigoMasivoTipoFk): void
    {
        $this->codigoMasivoTipoFk = $codigoMasivoTipoFk;
    }

    /**
     * @return mixed
     */
    public function getDirectorio()
    {
        return $this->directorio;
    }

    /**
     * @param mixed $directorio
     */
    public function setDirectorio($directorio): void
    {
        $this->directorio = $directorio;
    }

    /**
     * @return mixed
     */
    public function getNumeroArchivos()
    {
        return $this->numeroArchivos;
    }

    /**
     * @param mixed $numeroArchivos
     */
    public function setNumeroArchivos($numeroArchivos): void
    {
        $this->numeroArchivos = $numeroArchivos;
    }

    /**
     * @return mixed
     */
    public function getCodigoDirectorioPadreFk()
    {
        return $this->codigoDirectorioPadreFk;
    }

    /**
     * @param mixed $codigoDirectorioPadreFk
     */
    public function setCodigoDirectorioPadreFk($codigoDirectorioPadreFk): void
    {
        $this->codigoDirectorioPadreFk = $codigoDirectorioPadreFk;
    }

    /**
     * @return mixed
     */
    public function getArchivoDirectoriosRel()
    {
        return $this->archivoDirectoriosRel;
    }

    /**
     * @param mixed $archivoDirectoriosRel
     */
    public function setArchivoDirectoriosRel($archivoDirectoriosRel): void
    {
        $this->archivoDirectoriosRel = $archivoDirectoriosRel;
    }

    /**
     * @return mixed
     */
    public function getDirectorioRel()
    {
        return $this->directorioRel;
    }

    /**
     * @param mixed $directorioRel
     */
    public function setDirectorioRel($directorioRel): void
    {
        $this->directorioRel = $directorioRel;
    }

    /**
     * @return mixed
     */
    public function getDirectorioDirectoriosRel()
    {
        return $this->directorioDirectoriosRel;
    }

    /**
     * @param mixed $directorioDirectoriosRel
     */
    public function setDirectorioDirectoriosRel($directorioDirectoriosRel): void
    {
        $this->directorioDirectoriosRel = $directorioDirectoriosRel;
    }

}