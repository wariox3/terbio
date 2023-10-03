<?php


namespace App\Repository;


use App\Entity\Configuracion;
use App\Utilidades\Mensajes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ConfiguracionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Configuracion::class);
    }

    public function obtenerLogo(): array
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()->from(Configuracion::class, 'c')
            ->select('c.logo')
            ->where('c.codigoConfiguracionPk = 1');
        return $queryBuilder->getQuery()->getSingleResult();
    }

    public function eliminar($arrSeleccionados)
    {
        $em = $this->getEntityManager();
        if ($arrSeleccionados) {
            foreach ($arrSeleccionados as $codigo) {
                $arRegistro = $em->getRepository(Configuracion::class)->find($codigo);
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