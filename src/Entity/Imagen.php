<?php


namespace App\Entity;

use App\Repository\ImagenRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ImagenRepository::class)]
class Imagen
{

    #[ORM\Id]
    #[ORM\Column(type:"integer", name:"codigo_imagen_pk")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private $codigoImagenPk;


    #[ORM\Column(name: "identificador", type: "string", length: 50, nullable: true)]
    #[Assert\Length(max: 50, maxMessage: "El campo no puede contener mas de 50 caracteres")]
    private $identificador;


    #[ORM\Column(name: "codigo_modelo_fk", type: "string", length: 80, nullable: false)]
    private $codigoModeloFk;


    #[ORM\Column(name: "archivo", type: "string", length: 200, nullable: true)]
    #[Assert\Length(max: 200, maxMessage: "El campo no puede contener mas de 200 caracteres")]
    private $archivo;

    #[ORM\Column(name: "archivo_destino", type: "string", length: 200, nullable: true)]
    #[Assert\Length(max: 200, maxMessage: "El campo no puede contener mas de 200 caracteres")]
    private $archivoDestino;


    #[ORM\Column(name: "directorio", type: "string", length: 200, nullable: true)]
    #[Assert\Length(max: 200, maxMessage: "El campo no puede contener mas de 200 caracteres")]
    private $directorio;


    #[ORM\Column(name: "extension", type: "string", length: 10, nullable: true)]
    #[Assert\Length(max: 10, maxMessage: "El campo no puede contener mas de 10 caracteres")]
    private $extension;


    #[ORM\Column(name: "tamano", type: "float", options: ['default' => 0])]
    private $tamano = 0;


    #[ORM\Column(name: "nombre", type: "string", length: 500, nullable: true)]
    #[Assert\Length(max: 500, maxMessage: "El campo no puede contener mas de 500 caracteres")]
    private $nombre;


    #[ORM\Column(name: "extension_original", type: "string", length: 250, nullable: true)]
    #[Assert\Length(max: 250, maxMessage: "El campo no puede contener mas de 250 caracteres")]
    private $extensionOriginal;


    #[ORM\Column(name: "tipo", type: "string", length: 250, nullable: true)]
    #[Assert\Length(max: 250, maxMessage: "El campo no puede contener mas de 250 caracteres")]
    private $tipo;


    #[ORM\Column(name: "fecha", type: "datetime", nullable: true)]
    private $fecha;

    /**
     * @return mixed
     */
    public function getCodigoImagenPk()
    {
        return $this->codigoImagenPk;
    }

    /**
     * @param mixed $codigoImagenPk
     */
    public function setCodigoImagenPk($codigoImagenPk): void
    {
        $this->codigoImagenPk = $codigoImagenPk;
    }

    /**
     * @return mixed
     */
    public function getIdentificador()
    {
        return $this->identificador;
    }

    /**
     * @param mixed $identificador
     */
    public function setIdentificador($identificador): void
    {
        $this->identificador = $identificador;
    }

    /**
     * @return mixed
     */
    public function getCodigoModeloFk()
    {
        return $this->codigoModeloFk;
    }

    /**
     * @param mixed $codigoModeloFk
     */
    public function setCodigoModeloFk($codigoModeloFk): void
    {
        $this->codigoModeloFk = $codigoModeloFk;
    }

    /**
     * @return mixed
     */
    public function getArchivo()
    {
        return $this->archivo;
    }

    /**
     * @param mixed $archivo
     */
    public function setArchivo($archivo): void
    {
        $this->archivo = $archivo;
    }

    /**
     * @return mixed
     */
    public function getArchivoDestino()
    {
        return $this->archivoDestino;
    }

    /**
     * @param mixed $archivoDestino
     */
    public function setArchivoDestino($archivoDestino): void
    {
        $this->archivoDestino = $archivoDestino;
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
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param mixed $extension
     */
    public function setExtension($extension): void
    {
        $this->extension = $extension;
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
}