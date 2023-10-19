<?php


namespace App\Entity;

use App\Repository\AspiranteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AspiranteRepository::class)]
class Aspirante
{

    #[ORM\Id]
    #[ORM\Column(name: "codigo_aspirante_pk", type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private $codigoAspirantePk;

    #[ORM\Column(name: "fecha", type: "date", nullable: true)]
    private $fecha;

    #[ORM\Column(name: "numero_identificacion", type: "string", length: 20, nullable: false, unique: true)]
    #[Assert\NotBlank(message: "El campo no puede estar vacio")]
    #[Assert\Length(max: 20, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $numeroIdentificacion;

    #[ORM\Column(name: "libreta_militar", type: "string", length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $libretaMilitar;

    #[ORM\Column(name: "nombre_corto", type: "string", length: 120, nullable: true)]
    #[Assert\Length(max: 120, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $nombreCorto;

    #[ORM\Column(name: "nombre1", type: "string", length: 30, nullable: true)]
    #[Assert\Length(max: 30, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $nombre1;

    #[ORM\Column(name: "nombre2", type: "string", length: 30, nullable: true)]
    #[Assert\Length(max: 30, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $nombre2;

    #[ORM\Column(name: "apellido1", type: "string", length: 30, nullable: true)]
    #[Assert\Length(max: 30, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $apellido1;

    #[ORM\Column(name: "apellido2", type: "string", length: 30, nullable: true)]
    #[Assert\Length(max: 30, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $apellido2;

    #[ORM\Column(name: "telefono", type: "string", length: 15, nullable: true)]
    #[Assert\Length(max: 15, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $telefono;

    #[ORM\Column(name: "celular", type: "string", length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $celular;

    #[ORM\Column(name: "direccion", type: "string", length: 60, nullable: true)]
    #[Assert\Length(max: 60, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $direccion;

    #[ORM\Column(name: "barrio", type: "string", length: 100, nullable: true)]
    #[Assert\Length(max: 100, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $barrio;

    #[ORM\Column(name: "correo", type: "string", length: 80, nullable: true)]
    #[Assert\Length(max: 80, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $correo;

    #[ORM\Column(name: "fecha_nacimiento", type: "date",  nullable: true)]
    private $fechaNacimiento;

    #[ORM\Column(name: "codigo_ciudad_nacimiento_fk", type: "integer",  nullable: true)]
    private $codigoCiudadNacimientoFk;

    #[ORM\Column(name: "peso", type: "string", length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $peso;

    #[ORM\Column(name: "estatura", type: "string", length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $estatura;

    #[ORM\Column(name: "cargo_aspira", type: "string", length: 50, nullable: true)]
    #[Assert\Length(max: 50, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $cargoAspira;

    #[ORM\Column(name: "estado_autorizado", type: "boolean", options: ["default" => false])]
    private $estadoAutorizado = false;

    #[ORM\Column(name: "estado_aprobado", type: "boolean", options: ["default" => false])]
    private $estadoAprobado = false;

    #[ORM\Column(name: "estado_anulado", type: "boolean", options: ["default" => false])]
    private $estadoAnulado = false;

    #[ORM\Column(name: "estado_bloqueado", type: "boolean", options: ["default" => false])]
    private $estadoBloqueado = false;

    #[ORM\Column(name: "curso_vigilancia", type: "boolean", options: ["default" => false])]
    private $cursoVigilancia = false;

    #[ORM\Column(name: "moto", type: "boolean", options: ["default" => false])]
    private $moto = false;

    #[ORM\Column(name: "carro", type: "boolean", options: ["default" => false])]
    private $carro = false;

    #[ORM\Column(name: "posibilidad_viajar", type: "boolean", options: ["default" => false])]
    private $posibilidadViajar = false;

    #[ORM\Column(name: "licencia_moto", type: "boolean", options: ["default" => false])]
    private $licenciaMoto = false;

    #[ORM\Column(name: "licencia_carro", type: "boolean", options: ["default" => false])]
    private $licenciaCarro = false;

    #[ORM\Column(name: "discapacidad", type: "boolean", options: ["default" => false])]
    private $discapacidad = false;

    #[ORM\Column(name: "cabeza_hogar", type: "boolean", options: ["default" => false])]
    private $cabezaHogar = false;

    #[ORM\Column(name: "padre_familia", type: "boolean", options: ["default" => false])]
    private $padreFamilia = false;

    #[ORM\Column(name: "numero_hijos", type: "integer", nullable: true, options: ["default" => 0])]
    private $numeroHijos = false;

    #[ORM\Column(name: "ultima_empresa_labora", type: "string", length: 80, nullable: true)]
    #[Assert\Length(max: 80, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $ultimaEmpresaLabora;

    #[ORM\Column(name: "fecha_vence_curso_vigilancia", type: "date", nullable: true)]
    private $fechaVenceCursoVigilancia;

    #[ORM\Column(name: "comentarios", type: "string", length: 300, nullable: true)]
    private $comentarios;

    #[ORM\Column(name: "codigo_usuario_fk", type: "string", nullable: true)]
    private $codigoUsuarioFk;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: "usuarioAspiranteRel")]
    #[ORM\JoinColumn(name: "codigo_usuario_fk", referencedColumnName: "codigo_usuario_pk")]
    protected $usuarioRel;

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
    public function getCodigoAspirantePk()
    {
        return $this->codigoAspirantePk;
    }

    /**
     * @param mixed $codigoAspirantePk
     */
    public function setCodigoAspirantePk($codigoAspirantePk): void
    {
        $this->codigoAspirantePk = $codigoAspirantePk;
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
    public function getNumeroIdentificacion()
    {
        return $this->numeroIdentificacion;
    }

    /**
     * @param mixed $numeroIdentificacion
     */
    public function setNumeroIdentificacion($numeroIdentificacion): void
    {
        $this->numeroIdentificacion = $numeroIdentificacion;
    }

    /**
     * @return mixed
     */
    public function getLibretaMilitar()
    {
        return $this->libretaMilitar;
    }

    /**
     * @param mixed $libretaMilitar
     */
    public function setLibretaMilitar($libretaMilitar): void
    {
        $this->libretaMilitar = $libretaMilitar;
    }

    /**
     * @return mixed
     */
    public function getNombreCorto()
    {
        return $this->nombreCorto;
    }

    /**
     * @param mixed $nombreCorto
     */
    public function setNombreCorto($nombreCorto): void
    {
        $this->nombreCorto = $nombreCorto;
    }

    /**
     * @return mixed
     */
    public function getNombre1()
    {
        return $this->nombre1;
    }

    /**
     * @param mixed $nombre1
     */
    public function setNombre1($nombre1): void
    {
        $this->nombre1 = $nombre1;
    }

    /**
     * @return mixed
     */
    public function getNombre2()
    {
        return $this->nombre2;
    }

    /**
     * @param mixed $nombre2
     */
    public function setNombre2($nombre2): void
    {
        $this->nombre2 = $nombre2;
    }

    /**
     * @return mixed
     */
    public function getApellido1()
    {
        return $this->apellido1;
    }

    /**
     * @param mixed $apellido1
     */
    public function setApellido1($apellido1): void
    {
        $this->apellido1 = $apellido1;
    }

    /**
     * @return mixed
     */
    public function getApellido2()
    {
        return $this->apellido2;
    }

    /**
     * @param mixed $apellido2
     */
    public function setApellido2($apellido2): void
    {
        $this->apellido2 = $apellido2;
    }

    /**
     * @return mixed
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @param mixed $telefono
     */
    public function setTelefono($telefono): void
    {
        $this->telefono = $telefono;
    }

    /**
     * @return mixed
     */
    public function getCelular()
    {
        return $this->celular;
    }

    /**
     * @param mixed $celular
     */
    public function setCelular($celular): void
    {
        $this->celular = $celular;
    }

    /**
     * @return mixed
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param mixed $direccion
     */
    public function setDireccion($direccion): void
    {
        $this->direccion = $direccion;
    }

    /**
     * @return mixed
     */
    public function getBarrio()
    {
        return $this->barrio;
    }

    /**
     * @param mixed $barrio
     */
    public function setBarrio($barrio): void
    {
        $this->barrio = $barrio;
    }

    /**
     * @return mixed
     */
    public function getCorreo()
    {
        return $this->correo;
    }

    /**
     * @param mixed $correo
     */
    public function setCorreo($correo): void
    {
        $this->correo = $correo;
    }

    /**
     * @return mixed
     */
    public function getFechaNacimiento()
    {
        return $this->fechaNacimiento;
    }

    /**
     * @param mixed $fechaNacimiento
     */
    public function setFechaNacimiento($fechaNacimiento): void
    {
        $this->fechaNacimiento = $fechaNacimiento;
    }

    /**
     * @return mixed
     */
    public function getCodigoCiudadNacimientoFk()
    {
        return $this->codigoCiudadNacimientoFk;
    }

    /**
     * @param mixed $codigoCiudadNacimientoFk
     */
    public function setCodigoCiudadNacimientoFk($codigoCiudadNacimientoFk): void
    {
        $this->codigoCiudadNacimientoFk = $codigoCiudadNacimientoFk;
    }

    /**
     * @return mixed
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * @param mixed $peso
     */
    public function setPeso($peso): void
    {
        $this->peso = $peso;
    }

    /**
     * @return mixed
     */
    public function getEstatura()
    {
        return $this->estatura;
    }

    /**
     * @param mixed $estatura
     */
    public function setEstatura($estatura): void
    {
        $this->estatura = $estatura;
    }

    /**
     * @return mixed
     */
    public function getCargoAspira()
    {
        return $this->cargoAspira;
    }

    /**
     * @param mixed $cargoAspira
     */
    public function setCargoAspira($cargoAspira): void
    {
        $this->cargoAspira = $cargoAspira;
    }

    /**
     * @return mixed
     */
    public function getEstadoAutorizado()
    {
        return $this->estadoAutorizado;
    }

    /**
     * @param mixed $estadoAutorizado
     */
    public function setEstadoAutorizado($estadoAutorizado): void
    {
        $this->estadoAutorizado = $estadoAutorizado;
    }

    /**
     * @return mixed
     */
    public function getEstadoAprobado()
    {
        return $this->estadoAprobado;
    }

    /**
     * @param mixed $estadoAprobado
     */
    public function setEstadoAprobado($estadoAprobado): void
    {
        $this->estadoAprobado = $estadoAprobado;
    }

    /**
     * @return mixed
     */
    public function getEstadoAnulado()
    {
        return $this->estadoAnulado;
    }

    /**
     * @param mixed $estadoAnulado
     */
    public function setEstadoAnulado($estadoAnulado): void
    {
        $this->estadoAnulado = $estadoAnulado;
    }

    /**
     * @return mixed
     */
    public function getEstadoBloqueado()
    {
        return $this->estadoBloqueado;
    }

    /**
     * @param mixed $estadoBloqueado
     */
    public function setEstadoBloqueado($estadoBloqueado): void
    {
        $this->estadoBloqueado = $estadoBloqueado;
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
    public function getCursoVigilancia()
    {
        return $this->cursoVigilancia;
    }

    /**
     * @param mixed $cursoVigilancia
     */
    public function setCursoVigilancia($cursoVigilancia): void
    {
        $this->cursoVigilancia = $cursoVigilancia;
    }

    /**
     * @return mixed
     */
    public function getFechaVenceCursoVigilancia()
    {
        return $this->fechaVenceCursoVigilancia;
    }

    /**
     * @param mixed $fechaVenceCursoVigilancia
     */
    public function setFechaVenceCursoVigilancia($fechaVenceCursoVigilancia): void
    {
        $this->fechaVenceCursoVigilancia = $fechaVenceCursoVigilancia;
    }

    /**
     * @return mixed
     */
    public function getMoto()
    {
        return $this->moto;
    }

    /**
     * @param mixed $moto
     */
    public function setMoto($moto): void
    {
        $this->moto = $moto;
    }

    /**
     * @return mixed
     */
    public function getCarro()
    {
        return $this->carro;
    }

    /**
     * @param mixed $carro
     */
    public function setCarro($carro): void
    {
        $this->carro = $carro;
    }

    /**
     * @return mixed
     */
    public function getPosibilidadViajar()
    {
        return $this->posibilidadViajar;
    }

    /**
     * @param mixed $posibilidadViajar
     */
    public function setPosibilidadViajar($posibilidadViajar): void
    {
        $this->posibilidadViajar = $posibilidadViajar;
    }

    /**
     * @return mixed
     */
    public function getLicenciaMoto()
    {
        return $this->licenciaMoto;
    }

    /**
     * @param mixed $licenciaMoto
     */
    public function setLicenciaMoto( $licenciaMoto): void
    {
        $this->licenciaMoto = $licenciaMoto;
    }


    /**
     * @return mixed
     */
    public function getLicenciaCarro()
    {
        return $this->licenciaCarro;
    }

    /**
     * @param mixed $licenciaCarro
     */
    public function setLicenciaCarro($licenciaCarro): void
    {
        $this->licenciaCarro = $licenciaCarro;
    }

    /**
     * @return mixed
     */
    public function getDiscapacidad()
    {
        return $this->discapacidad;
    }

    /**
     * @param mixed $discapacidad
     */
    public function setDiscapacidad($discapacidad): void
    {
        $this->discapacidad = $discapacidad;
    }

    /**
     * @return mixed
     */
    public function getCabezaHogar()
    {
        return $this->cabezaHogar;
    }

    /**
     * @param mixed $cabezaHogar
     */
    public function setCabezaHogar($cabezaHogar): void
    {
        $this->cabezaHogar = $cabezaHogar;
    }

    /**
     * @return mixed
     */
    public function getPadreFamilia()
    {
        return $this->padreFamilia;
    }

    /**
     * @param mixed $padreFamilia
     */
    public function setPadreFamilia($padreFamilia): void
    {
        $this->padreFamilia = $padreFamilia;
    }

    /**
     * @return mixed
     */
    public function getNumeroHijos()
    {
        return $this->numeroHijos;
    }

    /**
     * @param mixed $numeroHijos
     */
    public function setNumeroHijos($numeroHijos): void
    {
        $this->numeroHijos = $numeroHijos;
    }

    /**
     * @return mixed
     */
    public function getUltimaEmpresaLabora()
    {
        return $this->ultimaEmpresaLabora;
    }

    /**
     * @param mixed $ultimaEmpresaLabora
     */
    public function setUltimaEmpresaLabora($ultimaEmpresaLabora): void
    {
        $this->ultimaEmpresaLabora = $ultimaEmpresaLabora;
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