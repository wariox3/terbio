<?php

namespace App\Repository;

use App\Controller\FuncionesController;
use App\Entity\Usuario;
use App\Utilidades\Mensajes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository
{
    private $token;


    public function __construct(ManagerRegistry $registry, TokenStorageInterface $token)
    {
        parent::__construct($registry, Usuario::class);
        $this->token = $token;
    }

    public function validarNumeroIdentificacion($arRegistro)
    {
        if ($arRegistro) {
            if ($arRegistro->getCodigoUsuarioPk()) {
                $queryBuilder = $this->_em->createQueryBuilder()->from(Usuario::class, 'u')
                    ->select('u')
                    ->where("u.codigoUsuarioPk = '{$arRegistro->getCodigoUsuarioPk()}'");
                $arUsuario = $queryBuilder->getQuery()->getResult();
            } else {
                $queryBuilder = $this->_em->createQueryBuilder()->from(Usuario::class, 'u')
                    ->select('u')
                    ->where("u.numeroIdentificacion = '{$arRegistro->getNumeroIdentificacion()}'")
                    ->andWhere("u.codigoIdentificacionFk = '{$arRegistro->getIdentificacionRel()->getCodigoIdentificacionPk()}'");
                $arUsuario = $queryBuilder->getQuery()->getResult();
            }
            if (!$arUsuario) {
                return true;
            } else {
                Mensajes::info('Ya existe un usuario con este tipo de identificación y este número de identificación');
                return false;
            }
        } else {
            Mensajes::info('Es necesario un tipo de identificación y un número de identificación');
            return false;
        }
    }

    public function validarNumeroIdentificacionEmpresas($arRegistro)
    {
        $em = $this->getEntityManager();
        if ($arRegistro) {
            if ($arRegistro->getCodigoUsuarioPk()) {
                $queryBuilder = $em->createQueryBuilder()->from(Usuario::class, 'e')
                    ->select('e')
                    ->where("e.numeroIdentificacion = {$arRegistro->getNumeroIdentificacion()}")
                    ->andWhere("e.codigoUsuarioPk <> '{$arRegistro->getCodigoUsuarioPk()}'");
                $arUsuario = $queryBuilder->getQuery()->getResult();
            } else {
                $arUsuario = $this->_em->getRepository(Usuario::class)->findBy(['numeroIdentificacion' => $arRegistro->getNumeroIdentificacion()]);
            }
            if (!$arUsuario) {
                return true;
            } else {
                Mensajes::info('Ya existe un usuario con esta identificación');
                return false;
            }
        } else {
            Mensajes::info('Es necesario un número de identificación y tipo de identificación');
            return false;
        }

    }

    public function validarNombreUsuario($arRegistro)
    {
        $em = $this->getEntityManager();
        if ($arRegistro) {
            $arUsuario = $em->getRepository(Usuario::class)->find($arRegistro->getcodigoUsuarioPk());
            if (!$arUsuario) {
                return true;
            } else {
                Mensajes::error("El nombre de usuario ya existe");
                return false;
            }
        }
    }

    public function lista($raw)
    {
        $filtros = $raw['filtros'] ?? null;
        $rol = null;
        $numeroIdentificacion = null;
        $nombre = null;
        $codigoUsuario = null;
        if ($filtros) {
            $rol = $filtros['rol'] ?? null;
            $numeroIdentificacion = $filtros['numeroIdentificacion'] ?? null;
            $nombre = $filtros['nombre'] ?? null;
            $codigoUsuario = $filtros['codigoUsuario'] ?? null;
        }

        $usuario = $this->token->getToken()->getUser();
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Usuario::class, 'u')
            ->select('u.codigoUsuarioPk')
            ->addSelect('u.numeroIdentificacion')
            ->addSelect('u.nombres')
            ->addSelect('u.apellidos')
            ->addSelect('u.cliente')
            ->addSelect('u.empleado')
            ->addSelect('u.codigoTerceroErpFk')
            ->addSelect('u.proveedor')
            ->addSelect('u.codigoOperacionFk')
            ->addSelect('u.codigoIdentificacionFk')
            ->addSelect('u.correo')
            ->addSelect('u.fechaRegistro')
            ->addSelect('e.nombre as empresaNombre')
            ->leftJoin('u.empresaRel', 'e');

        if ($usuario->getCodigoRolFk() == "ROLE_USER") {
            $queryBuilder->andWhere("u.codigoEmpresaFk = {$usuario->getCodigoEmpresaFk()}");
        }

        if ($codigoUsuario) {
            $queryBuilder->andWhere("u.codigoUsuarioPk = '{$codigoUsuario}'");
        }
        if ($numeroIdentificacion) {
            $queryBuilder->andWhere("u.numeroIdentificacion = '{$numeroIdentificacion}'");
        }
        if ($nombre) {
            $queryBuilder->andWhere("u.nombres like '%{$nombre}%'");
        }

        if ($rol == "empleado") {
            $queryBuilder->andWhere("u.empleado = true");
        }

        if ($rol == "empresa") {
            $queryBuilder->andWhere("u.empresa = true");
        }

        if ($rol == "cliente") {
            $queryBuilder->andWhere("u.cliente = true");
        }

        if ($rol == "proveedor") {
            $queryBuilder->andWhere("u.proveedor = true");
        }
        return $queryBuilder->getQuery()->getResult();
    }

    public function listaUsuarioEmpresa($raw)
    {
        $filtros = $raw['filtros'] ?? null;
        $numeroIdentificacion = null;
        $nombre = null;
        $codigoUsuario = null;
        if ($filtros) {
            $numeroIdentificacion = $filtros['numeroIdentificacion'] ?? null;
            $nombre = $filtros['nombre'] ?? null;
            $codigoUsuario = $filtros['codigoUsuario'] ?? null;
        }

        $usuario = $this->token->getToken()->getUser();
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Usuario::class, 'u')
            ->select('u.codigoUsuarioPk')
            ->addSelect('u.numeroIdentificacion')
            ->addSelect('u.nombres')
            ->addSelect('u.cliente')
            ->addSelect('u.empleado')
            ->addSelect('u.proveedor')
            ->addSelect('u.empresa')
            ->addSelect('u.codigoIdentificacionFk')
            ->addSelect('e.nombre as empresaNombre')
            ->leftJoin('u.empresaRel', 'e')
            ->where('u.empresa = 1');

        if ($usuario->getCodigoRolFk() == "ROLE_USER") {
            $queryBuilder->andWhere("u.codigoEmpresaFk = {$usuario->getCodigoEmpresaFk()}");
        }

        if ($codigoUsuario) {
            $queryBuilder->andWhere("u.codigoUsuarioPk = '{$codigoUsuario}'");
        }
        if ($numeroIdentificacion) {
            $queryBuilder->andWhere("u.numeroIdentificacion = '{$numeroIdentificacion}'");
        }
        if ($nombre) {
            $queryBuilder->andWhere("u.nombres like '%{$nombre}%'");
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function eliminar($arrSeleccionados)
    {
        $em = $this->getEntityManager();
        if ($arrSeleccionados) {
            foreach ($arrSeleccionados as $codigo) {
                $arRegistro = $em->getRepository(Usuario::class)->find($codigo);
                if ($arRegistro) {
                    $em->remove($arRegistro);
                }
            }
            try {
                $em->flush();
            } catch (\Exception $e) {
                Mensajes::error('No se puede eliminar, el registro se encuentra en uso en el sistema');
            }
        } else {
            Mensajes::error("No existen registros para eliminar");
        }
    }

    public function actualizarDatosUsuario()
    {
        $em = $this->getEntityManager();
        $usuario = $this->token->getToken()->getUser();
        $arEmpresa = $this->token->getToken()->getUser()->getEmpresaRel();
        $url = "/api/recursohumano/empleado/lista/datosempleado";
        $contador = 0;
        $respuesta = FuncionesController::consumirApi($usuario->getEmpresaRel(), [], $url, true);
        if ($respuesta['error'] == false) {
            $arrEmpleados = $respuesta['empleados'];
            foreach ($arrEmpleados as $arrEmpleado) {
                $arUsuario = $em->getRepository(Usuario::class)->findOneBy(['codigoIdentificacionFk' => $arrEmpleado['codigoIdentificacionFk'], 'numeroIdentificacion' => $arrEmpleado['numeroIdentificacion']]);
                if ($arUsuario) {
                    $arUsuario->setCorreo($arrEmpleado['correo']);
                    $arUsuario->setEmpresaRel($arEmpresa);
                    $arUsuario->setEmpleado(1);
                    $arUsuario->setNombres($arrEmpleado['nombre1'] . ' ' . $arrEmpleado['nombre2']);
                    $arUsuario->setApellidos($arrEmpleado['apellido1'] . ' ' . $arrEmpleado['apellido2']);
                    $em->persist($arUsuario);
                    $contador++;
                }
            }
            Mensajes::success("El proceso se ejecutó con éxito. A " . $contador . " usuarios se les actualizaron los datos.");
            $em->flush();
        }
    }
}
