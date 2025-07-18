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
    public function getPago()
    {
        return $this->pago;
    }

    /**
     * @param mixed $pago
     */
    public function setPago($pago): void
    {
        $this->pago = $pago;
    }

    /**
     * @return mixed
     */
    public function getCertificadoLaboral()
    {
        return $this->certificadoLaboral;
    }

    /**
     * @param mixed $certificadoLaboral
     */
    public function setCertificadoLaboral($certificadoLaboral): void
    {
        $this->certificadoLaboral = $certificadoLaboral;
    }

    /**
     * @return mixed
     */
    public function getCertificadoRetiro()
    {
        return $this->certificadoRetiro;
    }

    /**
     * @param mixed $certificadoRetiro
     */
    public function setCertificadoRetiro($certificadoRetiro): void
    {
        $this->certificadoRetiro = $certificadoRetiro;
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

    /**
     * @return bool
     */
    public function getProgramacion(): bool
    {
        return $this->programacion;
    }

    /**
     * @param bool $programacion
     */
    public function setProgramacion(bool $programacion): void
    {
        $this->programacion = $programacion;
    }

    /**
     * @return bool
     */
    public function isMenuGeneral(): bool
    {
        return $this->menuGeneral;
    }

    /**
     * @param bool $menuGeneral
     */
    public function setMenuGeneral(bool $menuGeneral): void
    {
        $this->menuGeneral = $menuGeneral;
    }

    /**
     * @return bool
     */
    public function isMenuCrm(): bool
    {
        return $this->menuCrm;
    }

    /**
     * @param bool $menuCrm
     */
    public function setMenuCrm(bool $menuCrm): void
    {
        $this->menuCrm = $menuCrm;
    }

    /**
     * @return bool
     */
    public function isMenuOperacion(): bool
    {
        return $this->menuOperacion;
    }

    /**
     * @param bool $menuOperacion
     */
    public function setMenuOperacion(bool $menuOperacion): void
    {
        $this->menuOperacion = $menuOperacion;
    }

    /**
     * @return bool
     */
    public function isMenuProgramacion(): bool
    {
        return $this->menuProgramacion;
    }

    /**
     * @param bool $menuProgramacion
     */
    public function setMenuProgramacion(bool $menuProgramacion): void
    {
        $this->menuProgramacion = $menuProgramacion;
    }

    /**
     * @return bool
     */
    public function isMenuVenta(): bool
    {
        return $this->menuVenta;
    }

    /**
     * @param bool $menuVenta
     */
    public function setMenuVenta(bool $menuVenta): void
    {
        $this->menuVenta = $menuVenta;
    }

    /**
     * @return bool
     */
    public function isMenuCartera(): bool
    {
        return $this->menuCartera;
    }

    /**
     * @param bool $menuCartera
     */
    public function setMenuCartera(bool $menuCartera): void
    {
        $this->menuCartera = $menuCartera;
    }

    /**
     * @return bool
     */
    public function isMenuRecursoHumano(): bool
    {
        return $this->menuRecursoHumano;
    }

    /**
     * @param bool $menuRecursoHumano
     */
    public function setMenuRecursoHumano(bool $menuRecursoHumano): void
    {
        $this->menuRecursoHumano = $menuRecursoHumano;
    }

    /**
     * @return bool
     */
    public function isMenuTransporte(): bool
    {
        return $this->menuTransporte;
    }

    /**
     * @param bool $menuTransporte
     */
    public function setMenuTransporte(bool $menuTransporte): void
    {
        $this->menuTransporte = $menuTransporte;
    }

    /**
     * @return bool
     */
    public function isRegistroFijo(): bool
    {
        return $this->registroFijo;
    }

    /**
     * @param bool $registroFijo
     */
    public function setRegistroFijo(bool $registroFijo): void
    {
        $this->registroFijo = $registroFijo;
    }

    /**
     * @return bool
     */
    public function isMenuInformacion(): bool
    {
        return $this->menuInformacion;
    }

    /**
     * @param bool $menuInformacion
     */
    public function setMenuInformacion(bool $menuInformacion): void
    {
        $this->menuInformacion = $menuInformacion;
    }

    /**
     * @return bool
     */
    public function isLogoMenu(): bool
    {
        return $this->logoMenu;
    }

    /**
     * @param bool $logoMenu
     */
    public function setLogoMenu(bool $logoMenu): void
    {
        $this->logoMenu = $logoMenu;
    }

    /**
     * @return bool
     */
    public function isMenuSolicitud(): bool
    {
        return $this->menuSolicitud;
    }

    /**
     * @param bool $menuSolicitud
     */
    public function setMenuSolicitud(bool $menuSolicitud): void
    {
        $this->menuSolicitud = $menuSolicitud;
    }

    /**
     * @return bool
     */
    public function isMenuCertificado(): bool
    {
        return $this->menuCertificado;
    }

    /**
     * @param bool $menuCertificado
     */
    public function setMenuCertificado(bool $menuCertificado): void
    {
        $this->menuCertificado = $menuCertificado;
    }

    /**
     * @return bool
     */
    public function isMenuCapacitacion(): bool
    {
        return $this->menuCapacitacion;
    }

    /**
     * @param bool $menuCapacitacion
     */
    public function setMenuCapacitacion(bool $menuCapacitacion): void
    {
        $this->menuCapacitacion = $menuCapacitacion;
    }

    /**
     * @return bool
     */
    public function isMenuCambioEmpresa(): bool
    {
        return $this->menuCambioEmpresa;
    }

    /**
     * @param bool $menuCambioEmpresa
     */
    public function setMenuCambioEmpresa(bool $menuCambioEmpresa): void
    {
        $this->menuCambioEmpresa = $menuCambioEmpresa;
    }

    /**
     * @return bool
     */
    public function isMenuCertificadoOtro(): bool
    {
        return $this->menuCertificadoOtro;
    }

    /**
     * @param bool $menuCertificadoOtro
     */
    public function setMenuCertificadoOtro(bool $menuCertificadoOtro): void
    {
        $this->menuCertificadoOtro = $menuCertificadoOtro;
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
     * @return bool
     */
    public function isMenuIncapacidad(): bool
    {
        return $this->menuIncapacidad;
    }

    /**
     * @param bool $menuIncapacidad
     */
    public function setMenuIncapacidad(bool $menuIncapacidad): void
    {
        $this->menuIncapacidad = $menuIncapacidad;
    }

    /**
     * @return bool
     */
    public function isMenuInformacionPersonal(): bool
    {
        return $this->menuInformacionPersonal;
    }

    /**
     * @param bool $menuInformacionPersonal
     */
    public function setMenuInformacionPersonal(bool $menuInformacionPersonal): void
    {
        $this->menuInformacionPersonal = $menuInformacionPersonal;
    }

    /**
     * @return bool
     */
    public function isFirma(): bool
    {
        return $this->firma;
    }

    /**
     * @param bool $firma
     */
    public function setFirma(bool $firma): void
    {
        $this->firma = $firma;
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

    /**
     * @return bool
     */
    public function isAcceso(): bool
    {
        return $this->acceso;
    }

    /**
     * @param bool $acceso
     */
    public function setAcceso(bool $acceso): void
    {
        $this->acceso = $acceso;
    }

    /**
     * @return bool
     */
    public function isMostrarSubmenu(): bool
    {
        return $this->mostrarSubmenu;
    }

    /**
     * @param bool $mostrarSubmenu
     */
    public function setMostrarSubmenu(bool $mostrarSubmenu): void
    {
        $this->mostrarSubmenu = $mostrarSubmenu;
    }

    /**
     * @return bool
     */
    public function isMostrarInformacionEmpleado(): bool
    {
        return $this->mostrarInformacionEmpleado;
    }

    /**
     * @param bool $mostrarInformacionEmpleado
     */
    public function setMostrarInformacionEmpleado(bool $mostrarInformacionEmpleado): void
    {
        $this->mostrarInformacionEmpleado = $mostrarInformacionEmpleado;
    }

    /**
     * @return bool
     */
    public function isValidarContratoActivo(): bool
    {
        return $this->validarContratoActivo;
    }

    /**
     * @param bool $validarContratoActivo
     */
    public function setValidarContratoActivo(bool $validarContratoActivo): void
    {
        $this->validarContratoActivo = $validarContratoActivo;
    }

    /**
     * @return bool
     */
    public function isMenuEmpleadoProgramacion(): bool
    {
        return $this->menuEmpleadoProgramacion;
    }

    /**
     * @param bool $menuEmpleadoProgramacion
     */
    public function setMenuEmpleadoProgramacion(bool $menuEmpleadoProgramacion): void
    {
        $this->menuEmpleadoProgramacion = $menuEmpleadoProgramacion;
    }

    public function isForzarCambioClaveRegistro(): bool
    {
        return $this->forzarCambioClaveRegistro;
    }

    public function setForzarCambioClaveRegistro(bool $forzarCambioClaveRegistro): void
    {
        $this->forzarCambioClaveRegistro = $forzarCambioClaveRegistro;
    }


}
