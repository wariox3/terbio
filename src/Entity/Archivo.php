<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArchivoRepository")
 */

class Archivo
{
    /**
     * @ORM\Id
     * @ORM\Column(name="codigo_archivo_pk", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $codigoArchivoPk;

    /**
     * @ORM\Column(name="codigo_archivo_tipo_fk", type="string", length=50, nullable=true)
     */
    private $codigoArchivoTipoFk;

    /**
     * @ORM\Column(name="codigo_directorio_fk", type="integer", nullable=true)
     */
    private $codigoDirectorioFk;

    /**
     * @ORM\Column(name="directorio", type="string", length=200, nullable=true)
     * @Assert\Length(
     *     max = 200,
     *     maxMessage="El campo no puede contener más de 200 caracteres"
     * )
     */
    private $directorio;

    /**
     * @ORM\Column(name="codigo", type="string", length=50, nullable=true)
     * @Assert\Length(
     *     max = 50,
     *     maxMessage="El campo no puede contener más de 50 caracteres"
     * )
     */
    private $codigo;

    /**
     * @ORM\Column(name="fecha_hasta", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\Column(name="nombre", type="string", length=500, nullable=true)
     * @Assert\Length(
     *     max = 500,
     *     maxMessage="El campo no puede contener más de 500 caracteres"
     * )
     */
    private $nombre;

    /**
     * @ORM\Column(name="extension_original", type="string", length=250, nullable=true)
     * @Assert\Length(
     *     max = 250,
     *     maxMessage="El campo no puede contener más de 250 caracteres"
     * )
     */
    private $extensionOriginal;

    /**
     * @ORM\Column(name="tipo", type="string", length=250, nullable=true)
     * @Assert\Length(
     *     max = 250,
     *     maxMessage="El campo no puede contener más de 250 caracteres"
     * )
     */
    private $tipo;

    /**
     * @ORM\Column(name="tamano", type="float", nullable=true)
     */
    private $tamano = 0;

    /**
     * @ORM\Column(name="descripcion", type="string", length=100, nullable=true)
     * @Assert\Length(
     *     max = 100,
     *     maxMessage="El campo no puede contener más de 100 caracteres"
     * )
     */
    private $descripcion;

    /**
     * @ORM\Column(name="comentarios", type="string", length=250, nullable=true)
     * @Assert\Length(
     *     max = 250,
     *     maxMessage="El campo no puede contener más de 250 caracteres"
     * )
     */
    private $comentarios;

    /**
     * @ORM\Column(name="usuario", type="string", length=25, nullable=true)
     * @Assert\Length(
     *     max = 25,
     *     maxMessage="El campo no puede contener más de 25 caracteres"
     * )
     */
    private $usuario;

    /**
     * @ORM\Column(name="soporte", type="string", length=50, nullable=true)
     * @Assert\Length(
     *     max = 50,
     *     maxMessage="El campo no puede contener más de 50 caracteres"
     * )
     */
    private $soporte;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Directorio", inversedBy="archivoDirectoriosRel")
     * @ORM\JoinColumn(name="codigo_directorio_fk",referencedColumnName="codigo_directorio_pk")
     */
    protected $directorioRel;

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
    public function getCodigoArchivoPk()
    {
        return $this->codigoArchivoPk;
    }

    /**
     * @param mixed $codigoArchivoPk
     */
    public function setCodigoArchivoPk($codigoArchivoPk): void
    {
        $this->codigoArchivoPk = $codigoArchivoPk;
    }

    /**
     * @return mixed
     */
    public function getCodigoArchivoTipoFk()
    {
        return $this->codigoArchivoTipoFk;
    }

    /**
     * @param mixed $codigoArchivoTipoFk
     */
    public function setCodigoArchivoTipoFk($codigoArchivoTipoFk): void
    {
        $this->codigoArchivoTipoFk = $codigoArchivoTipoFk;
    }

    /**
     * @return mixed
     */
    public function getCodigoDirectorioFk()
    {
        return $this->codigoDirectorioFk;
    }

    /**
     * @param mixed $codigoDirectorioFk
     */
    public function setCodigoDirectorioFk($codigoDirectorioFk): void
    {
        $this->codigoDirectorioFk = $codigoDirectorioFk;
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
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param mixed $codigo
     */
    public function setCodigo($codigo): void
    {
        $this->codigo = $codigo;
    }

    /**
     * @return mixed
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param mixed $fecha
     */
    public function setFecha($fecha): void
    {
        $this->fecha = $fecha;
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
    public function getExtensionOriginal()
    {
        return $this->extensionOriginal;
    }

    /**
     * @param mixed $extensionOriginal
     */
    public function setExtensionOriginal($extensionOriginal): void
    {
        $this->extensionOriginal = $extensionOriginal;
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
    public function getTamano()
    {
        return $this->tamano;
    }

    /**
     * @param mixed $tamano
     */
    public function setTamano($tamano): void
    {
        $this->tamano = $tamano;
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

    /**
     * @return mixed
     */
    public function getComentarios()
    {
        return $this->comentarios;
    }

    /**
     * @param mixed $comentarios
     */
    public function setComentarios($comentarios): void
    {
        $this->comentarios = $comentarios;
    }

    /**
     * @return mixed
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param mixed $usuario
     */
    public function setUsuario($usuario): void
    {
        $this->usuario = $usuario;
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
    public function getSoporte()
    {
        return $this->soporte;
    }

    /**
     * @param mixed $soporte
     */
    public function setSoporte($soporte): void
    {
        $this->soporte = $soporte;
    }

}