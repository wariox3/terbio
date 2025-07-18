<?php

namespace App\Entity;

use App\Repository\EmpresaRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: EmpresaRepository::class)]
class Empresa
{

    #[ORM\Id]
    #[ORM\Column(name: "codigo_empresa_pk", type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private $codigoEmpresaPk;

    #[ORM\Column(name: "nombre", type: "string", length: 60,nullable: true)]
    private $nombre;

    #[ORM\Column(name: "direccion", type: "string", length: 60,nullable: true)]
    private $direccion;

    #[ORM\Column(name: "telefono", type: "string", length: 60,nullable: true)]
    private $telefono;

    #[ORM\Column(name: "nit", type: "string", length: 20,nullable: true)]
    private $nit;

    #[ORM\Column(name: "digito_verificacion", type: "string", length: 1, nullable: true)]
    private $digitoVerificacion;

    #[ORM\Column(name: "abreviatura", type: "string", length: 60, nullable: true)]
    private $abreviatura;

    #[ORM\Column(name: "logo", type: "blob", nullable: true)]
    private $logo;

    #[ORM\Column(name: "extension", type: "string", length: 5,nullable: true)]
    private $extension;

    #[ORM\Column(name: "url_servicio", type: "string", length: 500, nullable: true)]
    private $urlServicio;

    #[ORM\Column(name: "token", type: "string", length: 300,nullable: true)]
    private $token;

    #[ORM\Column(name: "usuario_servicio", type: "string", length: 300, nullable: true)]
    private $usuarioServicio;

    #[ORM\Column(name: "clave_servicio", type: "string", length: 300, nullable: true)]
    private $claveServicio;

    #[ORM\Column(name: "pago", type: "boolean", nullable: false, options: ["default" => true])]
    private $pago = true;

    #[ORM\Column(name: "certificado_laboral", nullable: false, type: "boolean", options: ["default" => true])]
    private $certificadoLaboral = true;

    #[ORM\Column(name: "certificado_retiro", nullable: false, type: "boolean", options: ["default" => true])]
    private $certificadoRetiro = true;

    #[ORM\Column(name: "programacion", type: "boolean", options: ["default" => true])]
    private $programacion = true;

    #[ORM\Column(name: "menu_general", type: "boolean", options: ["default" => true])]
    private $menuGeneral = true;

    #[ORM\Column(name: "menu_crm", type: "boolean", options: ["default" => true])]
    private $menuCrm = true;

    #[ORM\Column(name: "menu_operacion", type: "boolean", options: ["default" => true])]
    private $menuOperacion = true;

    #[ORM\Column(name: "menu_programacion", type: "boolean", options: ["default" => true])]
    private $menuProgramacion = true;

    #[ORM\Column(name: "menu_venta", type: "boolean", options: ["default" => true])]
    private $menuVenta = true;

    #[ORM\Column(name: "menu_cartera", type: "boolean", options: ["default" => true])]
    private $menuCartera = true;

    #[ORM\Column(name: "menu_recurso_humano", type: "boolean", options: ["default" => true])]
    private $menuRecursoHumano = true;

    #[ORM\Column(name: "menu_transporte", type: "boolean", options: ["default" => true])]
    private $menuTransporte = true;

    #[ORM\Column(name: "menu_informacion", type: "boolean", options: ["default" => true])]
    private $menuInformacion = true;

    #[ORM\Column(name: "ciudad", type: "string", length: 60, nullable: true)]
    private $ciudad;

    #[ORM\Column(name: "codigo_item", type: "integer", length: 60, nullable: true)]
    private $codigoItem;

    #[ORM\Column(name: "registro_fijo", type: "boolean", options: ['default' => false])]
    private $registroFijo = false;

    #[ORM\Column(name: "logo_menu", type: "boolean", options: ['default' => false])]
    private $logoMenu = false;

    #[ORM\Column(name: "menu_solicitud", type: "boolean", options: ['default' => true])]
    private $menuSolicitud = true;

    #[ORM\Column(name: "menu_certificado", type: "boolean", options: ['default' => true])]
    private $menuCertificado = true;

    #[ORM\Column(name: "menu_certificado_otro", type: "boolean", options: ['default' => true])]
    private $menuCertificadoOtro = true;

    #[ORM\Column(name: "menu_capacitacion", type: "boolean", options: ['default' => true])]
    private $menuCapacitacion = true;

    #[ORM\Column(name: "menu_cambio_empresa", type: "boolean", options: ['default' => true])]
    private $menuCambioEmpresa = true;

    #[ORM\Column(name: "menu_incapacidad", type: "boolean", options: ['default' => true])]
    private $menuIncapacidad = true;

    #[ORM\Column(name: "menu_informacion_personal", type: "boolean", options: ['default' => true])]
    private $menuInformacionPersonal = true;

    #[ORM\Column(name: "firma", type: "boolean", options: ['default' => false])]
    private $firma = false;

    #[ORM\Column(name: "acceso", type: "boolean", options: ['default' => false])]
    private $acceso = false;

    #[ORM\Column(name: "mostrar_submenu", type: "boolean", options: ['default' => true])]
    private $mostrarSubmenu = true;

    #[ORM\Column(name: "mostrar_informacion_empleado", type: "boolean", options: ['default' => false])]
    private $mostrarInformacionEmpleado = false;

    #[ORM\Column(name: "validar_contrato_activo", type: "boolean", options: ['default' => false])]
    private $validarContratoActivo = false;

    #[ORM\Column(name: "menu_empleado_programacion", type: "boolean", options: ['default' => true])]
    private $menuEmpleadoProgramacion = true;

    #[ORM\Column(name: "menu_empleado_seguridad_social", type: "boolean", options: ['default' => true])]
    private $menuEmpleadoSeguridadSocial= true;

    #[ORM\Column(name: "forzar_cambio_clave_registro", type: "boolean", options: ['default' => false])]
    private $forzarCambioClaveRegistro = false;

    #[ORM\OneToMany(targetEntity: Usuario::class, mappedBy: "empresaRel")]
    protected $usuariosEmpresaRel;

    #[ORM\OneToMany(targetEntity: Enlace::class, mappedBy: "empresaRel")]
    protected $enlacesEmpresaRel;

    #[ORM\OneToMany(targetEntity: Texto::class, mappedBy: "empresaRel")]
    protected $textosEmpresaRel;

    /**
     * @return mixed
     */
    public function getCodigoEmpresaPk()
    {
        return $this->codigoEmpresaPk;
    }

    /**
     * @param mixed $codigoEmpresaPk
     */
    public function setCodigoEmpresaPk($codigoEmpresaPk): void
    {
        $this->codigoEmpresaPk = $codigoEmpresaPk;
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
    public function getNit()
    {
        return $this->nit;
    }

    /**
     * @param mixed $nit
     */
    public function setNit($nit): void
    {
        $this->nit = $nit;
    }

    /**
     * @return mixed
     */
    public function getDigitoVerificacion()
    {
        return $this->digitoVerificacion;
    }

    /**
     * @param mixed $digitoVerificacion
     */
    public function setDigitoVerificacion($digitoVerificacion): void
    {
        $this->digitoVerificacion = $digitoVerificacion;
    }

    /**
     * @return mixed
     */
    public function getAbreviatura()
    {
        return $this->abreviatura;
    }

    /**
     * @param mixed $abreviatura
     */
    public function setAbreviatura($abreviatura): void
    {
        $this->abreviatura = $abreviatura;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo): void
    {
        $this->logo = $logo;
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
    public function getUrlServicio()
    {
        return $this->urlServicio;
    }

    /**
     * @param mixed $urlServicio
     */
    public function setUrlServicio($urlServicio): void
    {
        $this->urlServicio = $urlServicio;
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
    public function getUsuarioServicio()
    {
        return $this->usuarioServicio;
    }

    /**
     * @param mixed $usuarioServicio
     */
    public function setUsuarioServicio($usuarioServicio): void
    {
        $this->usuarioServicio = $usuarioServicio;
    }

    /**
     * @return mixed
     */
    public function getClaveServicio()
    {
        return $this->claveServicio;
    }

    /**
     * @param mixed $claveServicio
     */
    public function setClaveServicio($claveServicio): void
    {
        $this->claveServicio = $claveServicio;
    }

    public function isPago(): bool
    {
        return $this->pago;
    }

    public function setPago(bool $pago): void
    {
        $this->pago = $pago;
    }

    public function isCertificadoLaboral(): bool
    {
        return $this->certificadoLaboral;
    }

    public function setCertificadoLaboral(bool $certificadoLaboral): void
    {
        $this->certificadoLaboral = $certificadoLaboral;
    }

    public function isCertificadoRetiro(): bool
    {
        return $this->certificadoRetiro;
    }

    public function setCertificadoRetiro(bool $certificadoRetiro): void
    {
        $this->certificadoRetiro = $certificadoRetiro;
    }

    public function isProgramacion(): bool
    {
        return $this->programacion;
    }

    public function setProgramacion(bool $programacion): void
    {
        $this->programacion = $programacion;
    }

    public function isMenuGeneral(): bool
    {
        return $this->menuGeneral;
    }

    public function setMenuGeneral(bool $menuGeneral): void
    {
        $this->menuGeneral = $menuGeneral;
    }

    public function isMenuCrm(): bool
    {
        return $this->menuCrm;
    }

    public function setMenuCrm(bool $menuCrm): void
    {
        $this->menuCrm = $menuCrm;
    }

    public function isMenuOperacion(): bool
    {
        return $this->menuOperacion;
    }

    public function setMenuOperacion(bool $menuOperacion): void
    {
        $this->menuOperacion = $menuOperacion;
    }

    public function isMenuProgramacion(): bool
    {
        return $this->menuProgramacion;
    }

    public function setMenuProgramacion(bool $menuProgramacion): void
    {
        $this->menuProgramacion = $menuProgramacion;
    }

    public function isMenuVenta(): bool
    {
        return $this->menuVenta;
    }

    public function setMenuVenta(bool $menuVenta): void
    {
        $this->menuVenta = $menuVenta;
    }

    public function isMenuCartera(): bool
    {
        return $this->menuCartera;
    }

    public function setMenuCartera(bool $menuCartera): void
    {
        $this->menuCartera = $menuCartera;
    }

    public function isMenuRecursoHumano(): bool
    {
        return $this->menuRecursoHumano;
    }

    public function setMenuRecursoHumano(bool $menuRecursoHumano): void
    {
        $this->menuRecursoHumano = $menuRecursoHumano;
    }

    public function isMenuTransporte(): bool
    {
        return $this->menuTransporte;
    }

    public function setMenuTransporte(bool $menuTransporte): void
    {
        $this->menuTransporte = $menuTransporte;
    }

    public function isMenuInformacion(): bool
    {
        return $this->menuInformacion;
    }

    public function setMenuInformacion(bool $menuInformacion): void
    {
        $this->menuInformacion = $menuInformacion;
    }

    /**
     * @return mixed
     */
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * @param mixed $ciudad
     */
    public function setCiudad($ciudad): void
    {
        $this->ciudad = $ciudad;
    }

    /**
     * @return mixed
     */
    public function getCodigoItem()
    {
        return $this->codigoItem;
    }

    /**
     * @param mixed $codigoItem
     */
    public function setCodigoItem($codigoItem): void
    {
        $this->codigoItem = $codigoItem;
    }

    public function isRegistroFijo(): bool
    {
        return $this->registroFijo;
    }

    public function setRegistroFijo(bool $registroFijo): void
    {
        $this->registroFijo = $registroFijo;
    }

    public function isLogoMenu(): bool
    {
        return $this->logoMenu;
    }

    public function setLogoMenu(bool $logoMenu): void
    {
        $this->logoMenu = $logoMenu;
    }

    public function isMenuSolicitud(): bool
    {
        return $this->menuSolicitud;
    }

    public function setMenuSolicitud(bool $menuSolicitud): void
    {
        $this->menuSolicitud = $menuSolicitud;
    }

    public function isMenuCertificado(): bool
    {
        return $this->menuCertificado;
    }

    public function setMenuCertificado(bool $menuCertificado): void
    {
        $this->menuCertificado = $menuCertificado;
    }

    public function isMenuCertificadoOtro(): bool
    {
        return $this->menuCertificadoOtro;
    }

    public function setMenuCertificadoOtro(bool $menuCertificadoOtro): void
    {
        $this->menuCertificadoOtro = $menuCertificadoOtro;
    }

    public function isMenuCapacitacion(): bool
    {
        return $this->menuCapacitacion;
    }

    public function setMenuCapacitacion(bool $menuCapacitacion): void
    {
        $this->menuCapacitacion = $menuCapacitacion;
    }

    public function isMenuCambioEmpresa(): bool
    {
        return $this->menuCambioEmpresa;
    }

    public function setMenuCambioEmpresa(bool $menuCambioEmpresa): void
    {
        $this->menuCambioEmpresa = $menuCambioEmpresa;
    }

    public function isMenuIncapacidad(): bool
    {
        return $this->menuIncapacidad;
    }

    public function setMenuIncapacidad(bool $menuIncapacidad): void
    {
        $this->menuIncapacidad = $menuIncapacidad;
    }

    public function isMenuInformacionPersonal(): bool
    {
        return $this->menuInformacionPersonal;
    }

    public function setMenuInformacionPersonal(bool $menuInformacionPersonal): void
    {
        $this->menuInformacionPersonal = $menuInformacionPersonal;
    }

    public function isFirma(): bool
    {
        return $this->firma;
    }

    public function setFirma(bool $firma): void
    {
        $this->firma = $firma;
    }

    public function isAcceso(): bool
    {
        return $this->acceso;
    }

    public function setAcceso(bool $acceso): void
    {
        $this->acceso = $acceso;
    }

    public function isMostrarSubmenu(): bool
    {
        return $this->mostrarSubmenu;
    }

    public function setMostrarSubmenu(bool $mostrarSubmenu): void
    {
        $this->mostrarSubmenu = $mostrarSubmenu;
    }

    public function isMostrarInformacionEmpleado(): bool
    {
        return $this->mostrarInformacionEmpleado;
    }

    public function setMostrarInformacionEmpleado(bool $mostrarInformacionEmpleado): void
    {
        $this->mostrarInformacionEmpleado = $mostrarInformacionEmpleado;
    }

    public function isValidarContratoActivo(): bool
    {
        return $this->validarContratoActivo;
    }

    public function setValidarContratoActivo(bool $validarContratoActivo): void
    {
        $this->validarContratoActivo = $validarContratoActivo;
    }

    public function isMenuEmpleadoProgramacion(): bool
    {
        return $this->menuEmpleadoProgramacion;
    }

    public function setMenuEmpleadoProgramacion(bool $menuEmpleadoProgramacion): void
    {
        $this->menuEmpleadoProgramacion = $menuEmpleadoProgramacion;
    }

    public function isMenuEmpleadoSeguridadSocial(): bool
    {
        return $this->menuEmpleadoSeguridadSocial;
    }

    public function setMenuEmpleadoSeguridadSocial(bool $menuEmpleadoSeguridadSocial): void
    {
        $this->menuEmpleadoSeguridadSocial = $menuEmpleadoSeguridadSocial;
    }

    public function isForzarCambioClaveRegistro(): bool
    {
        return $this->forzarCambioClaveRegistro;
    }

    public function setForzarCambioClaveRegistro(bool $forzarCambioClaveRegistro): void
    {
        $this->forzarCambioClaveRegistro = $forzarCambioClaveRegistro;
    }

    /**
     * @return mixed
     */
    public function getUsuariosEmpresaRel()
    {
        return $this->usuariosEmpresaRel;
    }

    /**
     * @param mixed $usuariosEmpresaRel
     */
    public function setUsuariosEmpresaRel($usuariosEmpresaRel): void
    {
        $this->usuariosEmpresaRel = $usuariosEmpresaRel;
    }

    /**
     * @return mixed
     */
    public function getEnlacesEmpresaRel()
    {
        return $this->enlacesEmpresaRel;
    }

    /**
     * @param mixed $enlacesEmpresaRel
     */
    public function setEnlacesEmpresaRel($enlacesEmpresaRel): void
    {
        $this->enlacesEmpresaRel = $enlacesEmpresaRel;
    }

    /**
     * @return mixed
     */
    public function getTextosEmpresaRel()
    {
        return $this->textosEmpresaRel;
    }

    /**
     * @param mixed $textosEmpresaRel
     */
    public function setTextosEmpresaRel($textosEmpresaRel): void
    {
        $this->textosEmpresaRel = $textosEmpresaRel;
    }



}
