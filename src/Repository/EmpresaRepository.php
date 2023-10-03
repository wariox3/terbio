<?php

namespace App\Repository;

use App\Entity\Empresa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @method Empresa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Empresa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Empresa[]    findAll()
 * @method Empresa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpresaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Empresa::class);
    }

    public function lista()
    {
        $session = new Session();
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Empresa::class, 'e')
            ->select('e.codigoEmpresaPk')
            ->addSelect('e.abreviatura')
            ->addSelect('e.nombre')
            ->addSelect('e.nit')
            ->addSelect('e.digitoVerificacion');

        if ($session->get('filtroEmpresaCodigo') != '') {
            $queryBuilder->andWhere("e.codigoEmpresaPk = '{$session->get('filtroEmpresaCodigo')}'");
        }

        if ($session->get('filtroEmpresaNumeroIdentificacion') != '') {
            $queryBuilder->andWhere("e.nit = '{$session->get('filtroEmpresaNumeroIdentificacion')}'");
        }

        if ($session->get('filtroEmpresaNombre') != '') {
            $queryBuilder->andWhere("e.nombre like '%{$session->get('filtroEmpresaNombre')}%'");
        }

        $queryBuilder->addOrderBy('e.codigoEmpresaPk', 'DESC');
        return $queryBuilder->getQuery()->getResult();
    }
}
