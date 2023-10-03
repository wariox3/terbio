<?php


namespace App\Repository;


use App\Entity\IncapacidadTipo;
use App\Utilidades\Mensajes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class IncapacidadTipoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IncapacidadTipo::class);
    }

    public function lista()
    {

        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->from(IncapacidadTipo::class, 'it')
            ->select('it.codigoIncapacidadTipoPk')
            ->addSelect('it.nombre')
            ->orderBy('it.codigoIncapacidadTipoPk', 'DESC');
        $arIncapacidades = $queryBuilder->getQuery()->getResult();
        return $arIncapacidades;
    }

    public function eliminar($arrSeleccionados)
    {
        $em = $this->getEntityManager();
        if ($arrSeleccionados) {
            foreach ($arrSeleccionados as $codigo) {
                $arRegistro = $em->getRepository(IncapacidadTipo::class)->find($codigo);
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