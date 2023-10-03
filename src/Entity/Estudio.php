<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EstudioRepository")
 */
class Estudio
{
    /**
     * @ORM\Id
     * @ORM\Column(name="codigo_estudio_pk", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $codigoEstudioPk;

    /**
     * @ORM\Column(name="codigo_usuario_fk", type="string", nullable=true)
     */
    private $codigoUsuarioFk;

    /**
     * @ORM\Column(name="estudio_tipo", type="string", length=50, nullable=true)
     */
    private $estudioTipo;

    /**
     * @ORM\Column(name="institucion", type="string", length=150, nullable=true)
     * @Assert\Length( max = 150, maxMessage = "El campo no puede contener más de {{ limit }} caracteres")
     */
    private $institucion;

    /**
     * @ORM\Column(name="titulo", type="string", length=120, nullable=true)
     * @Assert\Length( max = 120, maxMessage = "El campo no puede contener más de {{ limit }} caracteres")
     */
    private $titulo;

    /**
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @ORM\Column(name="fecha_inicio", type="date", nullable=true)
     */
    private $fechaInicio;

    /**
     * @ORM\Column(name="fecha_terminacion", type="date", nullable=true)
     */
    private $fechaTerminacion;

    /**
     * @ORM\Column(name="graduado", type="boolean",options={"default" : false}, nullable=true)
     */
    private $graduado = false;

    /**
     * @ORM\Column(name="duracion_estudio", type="string", length=60, nullable=true)
     * @Assert\Length( max = 60, maxMessage = "El campo no puede contener más de {{ limit }} caracteres")
     */
    private $duracionEstudio;

    /**
     * @ORM\Column(name="estado_estudio", type="string", length=20, nullable=true)
     */
    private $estadoEstudio;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario", inversedBy="usuarioEstudiosRel")
     * @ORM\JoinColumn(name="codigo_usuario_fk", referencedColumnName="codigo_usuario_pk")
     */
    protected $usuarioRel;

    /**
     * @return mixed
     */
    public function getCodigoEstudioPk()
    {
        return $this->codigoEstudioPk;
    }

    /**
     * @param mixed $codigoEstudioPk
     */
    public function setCodigoEstudioPk($codigoEstudioPk): void
    {
        $this->codigoEstudioPk = $codigoEstudioPk;
    }

    /**
     * @return mixed
     */
    public function getCodigoUsuarioFk()
    {
        return $this->codigoUsuarioFk;
    }

    /**
     * @return mixed
     */
    public function getEstudioTipo()
    {
        return $this->estudioTipo;
    }

    /**
     * @param mixed $estudioTipo
     */
    public function setEstudioTipo($estudioTipo): void
    {
        $this->estudioTipo = $estudioTipo;
    }



    /**
     * @param mixed $codigoEstudioTipo
     */
    public function setCodigoEstudioTipo($codigoEstudioTipo): void
    {
        $this->codigoEstudioTipo = $codigoEstudioTipo;
    }



    /**
     * @return mixed
     */
    public function getInstitucion()
    {
        return $this->institucion;
    }

    /**
     * @param mixed $institucion
     */
    public function setInstitucion($institucion): void
    {
        $this->institucion = $institucion;
    }

    /**
     * @return mixed
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @param mixed $titulo
     */
    public function setTitulo($titulo): void
    {
        $this->titulo = $titulo;
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
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * @param mixed $fechaInicio
     */
    public function setFechaInicio($fechaInicio): void
    {
        $this->fechaInicio = $fechaInicio;
    }

    /**
     * @return mixed
     */
    public function getFechaTerminacion()
    {
        return $this->fechaTerminacion;
    }

    /**
     * @param mixed $fechaTerminacion
     */
    public function setFechaTerminacion($fechaTerminacion): void
    {
        $this->fechaTerminacion = $fechaTerminacion;
    }

    /**
     * @return bool
     */
    public function isGraduado(): bool
    {
        return $this->graduado;
    }

    /**
     * @param bool $graduado
     */
    public function setGraduado(bool $graduado): void
    {
        $this->graduado = $graduado;
    }

    /**
     * @return mixed
     */
    public function getDuracionEstudio()
    {
        return $this->duracionEstudio;
    }

    /**
     * @param mixed $duracionEstudio
     */
    public function setDuracionEstudio($duracionEstudio): void
    {
        $this->duracionEstudio = $duracionEstudio;
    }

    /**
     * @return mixed
     */
    public function getEstadoEstudio()
    {
        return $this->estadoEstudio;
    }

    /**
     * @param mixed $estadoEstudio
     */
    public function setEstadoEstudio($estadoEstudio): void
    {
        $this->estadoEstudio = $estadoEstudio;
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


}