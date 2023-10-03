<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FormatoImagenRepository")
 */
class FormatoImagen
{
    /**
     * @ORM\Id
     * @ORM\Column(name="codigo_formato_imagen_pk", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $codigoFormatoImagenPk;

    /**
     * @ORM\Column(name="codigo_formato_fk", type="integer", nullable=false)
     */
    private $codigoFormatoFk;

    /**
     * @ORM\Column(name="imagen", type="blob", nullable=true)
     */
    private $imagen;

    /**
     * @ORM\Column(name="posicion_x", type="integer", nullable=true)
     */
    private $posicionX = 0;

    /**
     * @ORM\Column(name="posicion_y", type="integer", nullable=true)
     */
    private $posicionY = 0;

    /**
     * @ORM\Column(name="ancho", type="integer", nullable=true)
     */
    private $ancho = 0;

    /**
     * @ORM\Column(name="alto", type="integer", nullable=true)
     */
    private $alto = 0;

    /**
     * @ORM\Column(type="string", length=30, name="extension", nullable=true)
     */
    private $extension;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Formato", inversedBy="formatosImagenFormatoRel")
     * @ORM\JoinColumn(name="codigo_formato_fk", referencedColumnName="codigo_formato_pk")
     */
    protected $formatoRel;

    /**
     * @return mixed
     */
    public function getCodigoFormatoImagenPk()
    {
        return $this->codigoFormatoImagenPk;
    }

    /**
     * @param mixed $codigoFormatoImagenPk
     */
    public function setCodigoFormatoImagenPk($codigoFormatoImagenPk): void
    {
        $this->codigoFormatoImagenPk = $codigoFormatoImagenPk;
    }

    /**
     * @return mixed
     */
    public function getCodigoFormatoFk()
    {
        return $this->codigoFormatoFk;
    }

    /**
     * @param mixed $codigoFormatoFk
     */
    public function setCodigoFormatoFk($codigoFormatoFk): void
    {
        $this->codigoFormatoFk = $codigoFormatoFk;
    }

    /**
     * @return mixed
     */
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * @param mixed $imagen
     */
    public function setImagen($imagen): void
    {
        $this->imagen = $imagen;
    }

    /**
     * @return mixed
     */
    public function getPosicionX()
    {
        return $this->posicionX;
    }

    /**
     * @param mixed $posicionX
     */
    public function setPosicionX($posicionX): void
    {
        $this->posicionX = $posicionX;
    }

    /**
     * @return mixed
     */
    public function getPosicionY()
    {
        return $this->posicionY;
    }

    /**
     * @param mixed $posicionY
     */
    public function setPosicionY($posicionY): void
    {
        $this->posicionY = $posicionY;
    }

    /**
     * @return mixed
     */
    public function getFormatoRel()
    {
        return $this->formatoRel;
    }

    /**
     * @param mixed $formatoRel
     */
    public function setFormatoRel($formatoRel): void
    {
        $this->formatoRel = $formatoRel;
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
    public function getAncho()
    {
        return $this->ancho;
    }

    /**
     * @param mixed $ancho
     */
    public function setAncho($ancho): void
    {
        $this->ancho = $ancho;
    }

    /**
     * @return mixed
     */
    public function getAlto()
    {
        return $this->alto;
    }

    /**
     * @param mixed $alto
     */
    public function setAlto($alto): void
    {
        $this->alto = $alto;
    }


}
