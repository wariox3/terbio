<?php

namespace App\Repository;

use App\Entity\Formato;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FormatoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formato::class);
    }

    public function lista($codigoEmpresa)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->from(Formato::class, 'f')
            ->select('f.codigoFormatoPk')
            ->addSelect('f.nombre')
            ->addSelect('ft.nombre AS tipo')
            ->addSelect('f.titulo')
            ->addSelect('f.contenido')
            ->addSelect('f.fecha')
            ->leftJoin('f.formatoTipoRel', 'ft')
            ->where("f.codigoEmpresaFk = '{$codigoEmpresa}'");
        return $queryBuilder->getQuery()->getResult();
    }
}
