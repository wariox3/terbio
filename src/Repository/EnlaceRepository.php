<?php

namespace App\Repository;

use App\Entity\Enlace;
use App\Utilidades\Mensajes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Enlace|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enlace|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enlace[]    findAll()
 * @method Enlace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enlace::class);
    }

    public function lista($codigoEmpresa)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->from(Enlace::class, 'e')
            ->select('e.codigoEnlacePk')
            ->addSelect('e.nombre')
            ->addSelect('e.enlace')
        ->where("e.codigoEmpresaFk = {$codigoEmpresa}");
        $arEnlaces = $queryBuilder->getQuery()->getResult();
        return $arEnlaces;
    }

    public function eliminar($arrSeleccionados)
    {
        $em = $this->getEntityManager();
        if ($arrSeleccionados) {
            foreach ($arrSeleccionados as $codigo) {
                $arRegistro = $em->getRepository(Enlace::class)->find($codigo);
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
