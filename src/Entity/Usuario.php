<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "usuario")]
#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
    class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function getUserIdentifier(): string
    {
        return $this->getCodigoUsuarioPk();
    }
    #[ORM\Id]
    #[ORM\Column(type: "string", name: "codigo_usuario_pk")]
    private $codigoUsuarioPk;


    #[ORM\Column(name: "correo", type: "string", nullable: true)]
    private $correo;

    #[ORM\Column(name: "codigo_identificacion_fk", type: "string", length: 3, nullable: true)]
    private $codigoIdentificacionFk;


    #[ORM\Column(name: "numero_identificacion", type: "string", length: 20, nullable: false)]
    private $numeroIdentificacion;


    #[ORM\Column(name: "nombres", type: "string", nullable: true)]
    private $nombres;


    #[ORM\Column(name: "apellidos", type: "string", nullable: true)]
    private $apellidos;


    #[ORM\Column(name: "clave", type: "string", nullable: true)]
    private $clave;


    #[ORM\Column(name: "verificado", type: "boolean", nullable: true)]
    private $verificado;


    #[ORM\Column(name: "fecha_registro", type: "datetime", nullable: true)]
    private $fechaRegistro;


    #[ORM\Column(name: "token", type: "string", nullable: true)]
    private $token;

    #[ORM\Column(name: "codigo_empresa_fk", type: "integer", nullable: true, options: ["default" => null])]
    private $codigoEmpresaFk;


    #[ORM\Column(name: "codigo_rol_fk", type: "string", length: 20, nullable: true)]
    private $codigoRolFk;


    #[ORM\Column(name: "control", type: "boolean", nullable: true)]
    private $control;


    #[ORM\Column(name: "cliente", type: "boolean",  nullable: true)]
    private $cliente;


    #[ORM\Column(name: "empleado", type: "boolean",  nullable: true)]
    private $empleado;


    #[ORM\Column(name: "proveedor", type: "boolean",  nullable: true)]
    private $proveedor;


    #[ORM\Column(name: "empresa", type: "boolean",  nullable: true)]
    private $empresa;


    #[ORM\Column(name: "persona", type: "boolean",  nullable: true)]
    private $persona;


    #[ORM\Column(name: "codigo_tercero_erp_fk", type: "integer", nullable: true)]
    private $codigoTerceroErpFk;


    #[ORM\Column(name: "codigo_operacion_fk", type: "string", length: 20 ,nullable: true)]
    private $codigoOperacionFk;


    #[ORM\Column(name: "gestion_tranporte", type: "boolean", options: ["default" => false])]
    private $gestionTranporte = false;


    #[ORM\Column(name: "guia_nuevo", type: "boolean",  options: ["default" => true])]
    private $guiaNuevo = true;


    #[ORM\Column(name: "cambiar_valores_guia", type: "boolean", options: ["default" => false])]
    private $cambiarValoresGuia = false;


    #[ORM\Column(name: "forzar_cambio_clave", type: "boolean", options: ["default" => false])]
    private $forzarCambioClave = false;


    #[ORM\Column(name: "permite_cambiar_adquiriente", type: "boolean", options: ["default" => false])]
    private $permiteCambiarAdquiriente = false;


    #[ORM\Column(name: "estado_recogido", type: "boolean", options: ["default" => true])]
    private $estadoRecogido = true;


    #[ORM\Column(name: "estado_ingreso", type: "boolean", options: ["default" => true])]
    private $estadoIngreso = true;


    #[ORM\Column(name: "codigo_operacion_cliente_fk", type: "string", length: 20, nullable: true)]
    #[Assert\Length(max: 20, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $codigoOperacionClienteFk;

    #[ORM\Column(type: "string", length: 10, nullable: true)]
    #[Assert\Length(max: 10, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $codigoCiudadOrigenFk;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private $invertirOrigenDestino = false;

    #[ORM\Column(name: "bloquear_recaudo", type: "boolean", options: ["default" => false])]
    private $bloquearRecaudo = false;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private $bloquearAdquirienteCredito = false;

    #[ORM\Column(type: "string", length: 150, nullable: true)]
    #[Assert\Length(max: 150, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $direccionRemitente;

    #[ORM\Column(type: "string", length: 80, nullable: true)]
    #[Assert\Length(max: 80, maxMessage: "El campo no puede contener más de {{ limit }} caracteres")]
    private $telefonoRemitente;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private $bloquearOperacionCliente = false;

    #[ORM\ManyToOne(targetEntity: Empresa::class, inversedBy: "usuariosEmpresaRel")]
    #[ORM\JoinColumn(name: "codigo_empresa_fk", referencedColumnName: "codigo_empresa_pk")]
    protected $empresaRel;

    #[ORM\ManyToOne(targetEntity: Identificacion::class, inversedBy: "usuariosIdentificacionRel")]
    #[ORM\JoinColumn(name: "codigo_identificacion_fk", referencedColumnName: "codigo_identificacion_pk")]
    protected $identificacionRel;

    #[ORM\OneToMany(targetEntity: Estudio::class, mappedBy: "usuarioRel")]
    private $usuarioEstudiosRel;

    #[ORM\OneToMany(targetEntity: Aspirante::class, mappedBy: "usuarioRel")]
    private $usuarioAspiranteRel;

    #[ORM\OneToMany(targetEntity: Incapacidad::class, mappedBy: "usuarioRel")]
    private $usuarioIncapacidadesRel;

    /**
     * Se implementan métodos de la clase User del core de Symfony además de los metodos de la entidad própia.
     *
     */
    public function serialize()
    {
        return serialize(array(
            $this->codigoUsuarioPk,
            $this->clave
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->codigoUsuarioPk,
            $this->clave

            ) = unserialize($serialized);
    }

    public function getUsername(): string
    {
        return $this->getCodigoUsuarioPk();
    }

    public function getRoles(): array
    {

        return array($this->codigoRolFk);
    }

    public function getPassword(): ?string
    {
        return $this->getClave();
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function eraseCredentials()
    {

    }

    /**
     * @return mixed
     */
    public function getCodigoUsuarioPk()
    {
        return $this->codigoUsuarioPk;
    }

    /**
     * @param mixed $codigoUsuarioPk
     */
    public function setCodigoUsuarioPk($codigoUsuarioPk): void
    {
        $this->codigoUsuarioPk = $codigoUsuarioPk;
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
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * @param mixed $nombres
     */
    public function setNombres($nombres): void
    {
        $this->nombres = $nombres;
    }

    /**
     * @return mixed
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * @param mixed $apellidos
     */
    public function setApellidos($apellidos): void
    {
        $this->apellidos = $apellidos;
    }

    /**
     * @return mixed
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * @param mixed $clave
     */
    public function setClave($clave): void
    {
        $this->clave = $clave;
    }

    /**
     * @return mixed
     */
    public function getVerificado()
    {
        return $this->verificado;
    }

    /**
     * @param mixed $verificado
     */
    public function setVerificado($verificado): void
    {
        $this->verificado = $verificado;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getCodigoClienteFk()
    {
        return $this->codigoClienteFk;
    }

    /**
     * @param mixed $codigoClienteFk
     */
    public function setCodigoClienteFk($codigoClienteFk): void
    {
        $this->codigoClienteFk = $codigoClienteFk;
    }

    /**
     * @return mixed
     */
    public function getCodigoRolFk()
    {
        return $this->codigoRolFk;
    }

    /**
     * @param mixed $codigoRolFk
     */
    public function setCodigoRolFk($codigoRolFk): void
    {
        $this->codigoRolFk = $codigoRolFk;
    }

    /**
     * @return mixed
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * @param mixed $control
     */
    public function setControl($control): void
    {
        $this->control = $control;
    }

    /**
     * @return mixed
     */
    public function getClienteRel()
    {
        return $this->clienteRel;
    }

    /**
     * @param mixed $clienteRel
     */
    public function setClienteRel($clienteRel): void
    {
        $this->clienteRel = $clienteRel;
    }

    /**
     * @return mixed
     */
    public function getCodigoEmpresaFk()
    {
        return $this->codigoEmpresaFk;
    }

    /**
     * @param mixed $codigoEmpresaFk
     */
    public function setCodigoEmpresaFk($codigoEmpresaFk): void
    {
        $this->codigoEmpresaFk = $codigoEmpresaFk;
    }

    /**
     * @return mixed
     */
    public function getEmpresaRel()
    {
        return $this->empresaRel;
    }

    /**
     * @param mixed $empresaRel
     */
    public function setEmpresaRel($empresaRel): void
    {
        $this->empresaRel = $empresaRel;
    }

    /**
     * @return mixed
     */
    public function getCodigoIdentificacionFk()
    {
        return $this->codigoIdentificacionFk;
    }

    /**
     * @param mixed $codigoIdentificacionFk
     */
    public function setCodigoIdentificacionFk($codigoIdentificacionFk): void
    {
        $this->codigoIdentificacionFk = $codigoIdentificacionFk;
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
    public function getIdentificacionRel()
    {
        return $this->identificacionRel;
    }

    /**
     * @param mixed $identificacionRel
     */
    public function setIdentificacionRel($identificacionRel): void
    {
        $this->identificacionRel = $identificacionRel;
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
    public function getCliente()
    {
        return $this->cliente;
    }

    /**
     * @param mixed $cliente
     */
    public function setCliente($cliente): void
    {
        $this->cliente = $cliente;
    }

    /**
     * @return mixed
     */
    public function getEmpleado()
    {
        return $this->empleado;
    }

    /**
     * @param mixed $empleado
     */
    public function setEmpleado($empleado): void
    {
        $this->empleado = $empleado;
    }

    /**
     * @return mixed
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * @param mixed $proveedor
     */
    public function setProveedor($proveedor): void
    {
        $this->proveedor = $proveedor;
    }

    /**
     * @return mixed
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }

    /**
     * @param mixed $empresa
     */
    public function setEmpresa($empresa): void
    {
        $this->empresa = $empresa;
    }

    /**
     * @return mixed
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * @param mixed $persona
     */
    public function setPersona($persona): void
    {
        $this->persona = $persona;
    }

    /**
     * @return mixed
     */
    public function getCodigoTerceroErpFk()
    {
        return $this->codigoTerceroErpFk;
    }

    /**
     * @param mixed $codigoTerceroErpFk
     */
    public function setCodigoTerceroErpFk($codigoTerceroErpFk): void
    {
        $this->codigoTerceroErpFk = $codigoTerceroErpFk;
    }

    /**
     * @return mixed
     */
    public function getUsuarioEstudiosRel()
    {
        return $this->usuarioEstudiosRel;
    }

    /**
     * @param mixed $usuarioEstudiosRel
     */
    public function setUsuarioEstudiosRel($usuarioEstudiosRel): void
    {
        $this->usuarioEstudiosRel = $usuarioEstudiosRel;
    }

    /**
     * @return mixed
     */
    public function getUsuarioIdiomasRel()
    {
        return $this->usuarioIdiomasRel;
    }

    /**
     * @param mixed $usuarioIdiomasRel
     */
    public function setUsuarioIdiomasRel($usuarioIdiomasRel): void
    {
        $this->usuarioIdiomasRel = $usuarioIdiomasRel;
    }

    /**
     * @return mixed
     */
    public function getCodigoOperacionFk()
    {
        return $this->codigoOperacionFk;
    }

    /**
     * @param mixed $codigoOperacionFk
     */
    public function setCodigoOperacionFk($codigoOperacionFk): void
    {
        $this->codigoOperacionFk = $codigoOperacionFk;
    }

    /**
     * @return mixed
     */
    public function getUsuarioAspiranteIdiomaRel()
    {
        return $this->usuarioAspiranteIdiomaRel;
    }

    /**
     * @param mixed $usuarioAspiranteIdiomaRel
     */
    public function setUsuarioAspiranteIdiomaRel($usuarioAspiranteIdiomaRel): void
    {
        $this->usuarioAspiranteIdiomaRel = $usuarioAspiranteIdiomaRel;
    }

    /**
     * @return bool
     */
    public function isGuiaNuevo(): bool
    {
        return $this->guiaNuevo;
    }

    /**
     * @param bool $guiaNuevo
     */
    public function setGuiaNuevo(bool $guiaNuevo): void
    {
        $this->guiaNuevo = $guiaNuevo;
    }

    /**
     * @return bool
     */
    public function isGestionTranporte(): bool
    {
        return $this->gestionTranporte;
    }

    /**
     * @param bool $gestionTranporte
     */
    public function setGestionTranporte(bool $gestionTranporte): void
    {
        $this->gestionTranporte = $gestionTranporte;
    }

    /**
     * @return bool
     */
    public function isForzarCambioClave(): bool
    {
        return $this->forzarCambioClave;
    }

    /**
     * @param bool $forzarCambioClave
     */
    public function setForzarCambioClave(bool $forzarCambioClave): void
    {
        $this->forzarCambioClave = $forzarCambioClave;
    }

    /**
     * @return bool
     */
    public function isCambiarValoresGuia(): bool
    {
        return $this->cambiarValoresGuia;
    }

    /**
     * @param bool $cambiarValoresGuia
     */
    public function setCambiarValoresGuia(bool $cambiarValoresGuia): void
    {
        $this->cambiarValoresGuia = $cambiarValoresGuia;
    }

    /**
     * @return mixed
     */
    public function getUsuarioIncapacidadesRel()
    {
        return $this->usuarioIncapacidadesRel;
    }

    /**
     * @param mixed $usuarioIncapacidadesRel
     */
    public function setUsuarioIncapacidadesRel($usuarioIncapacidadesRel): void
    {
        $this->usuarioIncapacidadesRel = $usuarioIncapacidadesRel;
    }

    /**
     * @return mixed
     */
    public function getCodigoOperacionClienteFk()
    {
        return $this->codigoOperacionClienteFk;
    }

    /**
     * @param mixed $codigoOperacionClienteFk
     */
    public function setCodigoOperacionClienteFk($codigoOperacionClienteFk): void
    {
        $this->codigoOperacionClienteFk = $codigoOperacionClienteFk;
    }

    /**
     * @return bool
     */
    public function isPermiteCambiarAdquiriente(): bool
    {
        return $this->permiteCambiarAdquiriente;
    }

    /**
     * @param bool $permiteCambiarAdquiriente
     */
    public function setPermiteCambiarAdquiriente(bool $permiteCambiarAdquiriente): void
    {
        $this->permiteCambiarAdquiriente = $permiteCambiarAdquiriente;
    }

    /**
     * @return bool
     */
    public function isEstadoRecogido(): bool
    {
        return $this->estadoRecogido;
    }

    /**
     * @param bool $estadoRecogido
     */
    public function setEstadoRecogido(bool $estadoRecogido): void
    {
        $this->estadoRecogido = $estadoRecogido;
    }

    /**
     * @return bool
     */
    public function isEstadoIngreso(): bool
    {
        return $this->estadoIngreso;
    }

    /**
     * @param bool $estadoIngreso
     */
    public function setEstadoIngreso(bool $estadoIngreso): void
    {
        $this->estadoIngreso = $estadoIngreso;
    }

    /**
     * @return bool
     */
    public function isBloquearRecaudo(): bool
    {
        return $this->bloquearRecaudo;
    }

    /**
     * @param bool $bloquearRecaudo
     */
    public function setBloquearRecaudo(bool $bloquearRecaudo): void
    {
        $this->bloquearRecaudo = $bloquearRecaudo;
    }

    public function isBloquearAdquirienteCredito(): bool
    {
        return $this->bloquearAdquirienteCredito;
    }

    public function setBloquearAdquirienteCredito(bool $bloquearAdquirienteCredito): void
    {
        $this->bloquearAdquirienteCredito = $bloquearAdquirienteCredito;
    }

    /**
     * @return mixed
     */
    public function getCodigoCiudadOrigenFk()
    {
        return $this->codigoCiudadOrigenFk;
    }

    /**
     * @param mixed $codigoCiudadOrigenFk
     */
    public function setCodigoCiudadOrigenFk($codigoCiudadOrigenFk): void
    {
        $this->codigoCiudadOrigenFk = $codigoCiudadOrigenFk;
    }

    public function isInvertirOrigenDestino(): bool
    {
        return $this->invertirOrigenDestino;
    }

    public function setInvertirOrigenDestino(bool $invertirOrigenDestino): void
    {
        $this->invertirOrigenDestino = $invertirOrigenDestino;
    }

    /**
     * @return mixed
     */
    public function getUsuarioAspiranteRel()
    {
        return $this->usuarioAspiranteRel;
    }

    /**
     * @param mixed $usuarioAspiranteRel
     */
    public function setUsuarioAspiranteRel($usuarioAspiranteRel): void
    {
        $this->usuarioAspiranteRel = $usuarioAspiranteRel;
    }

    /**
     * @return mixed
     */
    public function getDireccionRemitente()
    {
        return $this->direccionRemitente;
    }

    /**
     * @param mixed $direccionRemitente
     */
    public function setDireccionRemitente($direccionRemitente): void
    {
        $this->direccionRemitente = $direccionRemitente;
    }

    /**
     * @return mixed
     */
    public function getTelefonoRemitente()
    {
        return $this->telefonoRemitente;
    }

    /**
     * @param mixed $telefonoRemitente
     */
    public function setTelefonoRemitente($telefonoRemitente): void
    {
        $this->telefonoRemitente = $telefonoRemitente;
    }

    public function isBloquearOperacionCliente(): bool
    {
        return $this->bloquearOperacionCliente;
    }

    public function setBloquearOperacionCliente(bool $bloquearOperacionCliente): void
    {
        $this->bloquearOperacionCliente = $bloquearOperacionCliente;
    }


}
