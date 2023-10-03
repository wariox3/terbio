<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IncapacidadRepository")
 */
class Incapacidad
{
    /**
     * @ORM\Id
     * @ORM\Column(name="codigo_incapacidad_pk", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $codigoIncapacidadPk;

    /**
     * @ORM\Column(name="codigo_usuario_fk", type="string", nullable=true)
     */
    private $codigoUsuarioFk;

    /**
     * @ORM\Column(name="codigo_incapacidad_tipo_fk", type="string", length=10, nullable=true)
     */
    private $codigoIncapacidadTipoFk;

    /**
     * @ORM\Column(name="numero_eps", type="string", length=50, nullable=true)
     * @Assert\Length(
     *     max = 50,
     *     maxMessage="El campo no puede contener mas de 50 caracteres"
     * )
     */
    private $numeroEps;

    /**
     * @ORM\Column(name="fecha_registro", type="datetime", nullable=true)
     */
    private $fechaRegistro;

    /**
     * @ORM\Column(name="fecha_desde", type="date")
     */
    private $fechaDesde;

    /**
     * @ORM\Column(name="fecha_hasta", type="date")
     */
    private $fechaHasta;

    /**
     * @ORM\Column(name="comentarios", type="string", length=2000, nullable=true)
     * @Assert\Length( max = 2000, maxMessage="El campo no puede contener mas de 2000 caracteres")
     */
    private $comentarios;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario", inversedBy="usuarioIncapacidadesRel")
     * @ORM\JoinColumn(name="codigo_usuario_fk", referencedColumnName="codigo_usuario_pk")
     */
    private $usuarioRel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IncapacidadTipo", inversedBy="incapacidadesIncapacidadTipoRel")
     * @ORM\JoinColumn(name="codigo_incapacidad_tipo_fk",referencedColumnName="codigo_incapacidad_tipo_pk")
     */
    protected $incapacidadTipoRel;

    /**
     * @return mixed
     */
    public function getCodigoIncapacidadPk()
    {
        return $this->codigoIncapacidadPk;
    }

    /**
     * @param mixed $codigoIncapacidadPk
     */
    public function setCodigoIncapacidadPk($codigoIncapacidadPk): void
    {
        $this->codigoIncapacidadPk = $codigoIncapacidadPk;
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
    public function getCodigoIncapacidadTipoFk()
    {
        return $this->codigoIncapacidadTipoFk;
    }

    /**
     * @param mixed $codigoIncapacidadTipoFk
     */
    public function setCodigoIncapacidadTipoFk($codigoIncapacidadTipoFk): void
    {
        $this->codigoIncapacidadTipoFk = $codigoIncapacidadTipoFk;
    }

    /**
     * @return mixed
     */
    public function getNumeroEps()
    {
        return $this->numeroEps;
    }

    /**
     * @param mixed $numeroEps
     */
    public function setNumeroEps($numeroEps): void
    {
        $this->numeroEps = $numeroEps;
    }

    /**
     * @return mixed
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * @param mixed $fechaRegistro
     */
    public function setFechaRegistro($fechaRegistro): void
    {
        $this->fechaRegistro = $fechaRegistro;
    }

    /**
     * @return mixed
     */
    public function getFechaDesde()
    {
        return $this->fechaDesde;
    }

    /**
     * @param mixed $fechaDesde
     */
    public function setFechaDesde($fechaDesde): void
    {
        $this->fechaDesde = $fechaDesde;
    }

    /**
     * @return mixed
     */
    public function getFechaHasta()
    {
        return $this->fechaHasta;
    }

    /**
     * @param mixed $fechaHasta
     */
    public function setFechaHasta($fechaHasta): void
    {
        $this->fechaHasta = $fechaHasta;
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
    public function getIncapacidadTipoRel()
    {
        return $this->incapacidadTipoRel;
    }

    /**
     * @param mixed $incapacidadTipoRel
     */
    public function setIncapacidadTipoRel($incapacidadTipoRel): void
    {
        $this->incapacidadTipoRel = $incapacidadTipoRel;
    }
}