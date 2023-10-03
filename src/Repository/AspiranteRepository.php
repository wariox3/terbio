<?php


namespace App\Repository;


use App\Entity\Aspirante;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AspiranteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Aspirante::class);
    }

    public function lista()
    {

        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Aspirante::class, 'a')
            ->select('a.codigoAspirantePk')
            ->addSelect('a.numeroIdentificacion')
            ->addSelect('a.nombreCorto')
            ->addSelect('a.fechaNacimiento')
            ->addSelect('a.telefono')
            ->addSelect('a.celular')
            ->addSelect('a.correo')
            ->addSelect('a.direccion')
            ->addSelect("a.moto")
            ->addSelect("a.carro")
            ->addSelect("a.licenciaCarro")
            ->addSelect("a.licenciaMoto")
            ->addSelect("a.posibilidadViajar")
            ->addSelect("a.discapacidad")
            ->addSelect("a.cabezaHogar")
            ->addSelect("a.padreFamilia")
            ->addSelect("a.numeroHijos")
            ->addSelect("a.ultimaEmpresaLabora")
            ->addSelect('a.estadoAutorizado')
            ->addSelect('a.estadoAprobado')
            ->addSelect('a.estadoAnulado')
            ->addSelect('a.estadoBloqueado');

        $queryBuilder->addOrderBy('a.codigoAspirantePk', 'DESC');
        return $queryBuilder->getQuery()->getResult();
    }

    public function eliminar($arrSeleccionados)
    {
        $em = $this->getEntityManager();
        if ($arrSeleccionados) {
            foreach ($arrSeleccionados as $codigo) {
                $arRegistro = $this->getEntityManager()->getRepository(Aspirante::class)->find($codigo);
                $em->remove($arRegistro);
                $em->flush();
            }
        }
    }
}