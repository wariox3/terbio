<?php


namespace App\Repository;


use App\Entity\Incapacidad;
use App\Utilidades\Mensajes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class IncapacidadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Incapacidad::class);
    }

    public function lista($usuario)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->from(Incapacidad::class, 'i')
            ->select('i.codigoIncapacidadPk')
            ->addSelect('i.fechaRegistro')
            ->addSelect('i.fechaDesde')
            ->addSelect('i.fechaHasta')
            ->addSelect('i.numeroEps')
            ->addSelect('i.comentarios')
            ->addSelect('it.nombre as incapacidadTipoNombre')
            ->where("i.codigoUsuarioFk = '{$usuario}'")
            ->leftJoin('i.incapacidadTipoRel', 'it')
            ->orderBy('i.codigoIncapacidadPk', 'DESC');
        $arIncapacidades = $queryBuilder->getQuery()->getResult();
        return $arIncapacidades;
    }

    public function eliminar($arrSeleccionados)
    {
        $em = $this->getEntityManager();
        if ($arrSeleccionados) {
            foreach ($arrSeleccionados as $codigo) {
                $arRegistro = $em->getRepository(Incapacidad::class)->find($codigo);
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
}