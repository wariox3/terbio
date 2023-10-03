<?php

namespace App\Repository;

use App\Entity\FormatoImagen;
use App\Entity\Usuario;
use App\Utilidades\Mensajes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FormatoImagenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormatoImagen::class);
    }

    public function lista($codigoEmpresa, $codigoFormato)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder()->from(FormatoImagen::class, 'fi')
            ->select('fi.codigoFormatoImagenPk')
            ->addSelect('fi.imagen')
            ->addSelect('fi.posicionX')
            ->addSelect('fi.posicionY')
            ->addSelect('fi.ancho')
            ->addSelect('fi.alto')
            ->leftJoin('fi.formatoRel', 'f')
            ->where("f.codigoEmpresaFk = '{$codigoEmpresa}'")
            ->andWhere("fi.codigoFormatoFk = '{$codigoFormato}'");
        return $queryBuilder->getQuery()->getResult();
    }

    public function eliminar($arrSeleccionados)
    {
        $em = $this->getEntityManager();
        if ($arrSeleccionados) {
            foreach ($arrSeleccionados as $codigo) {
                $arRegistro = $em->getRepository(FormatoImagen::class)->find($codigo);
                if ($arRegistro) {
                    $em->remove($arRegistro);
                }
            }
            try {
                $em->flush();
            } catch (\Exception $e) {
                Mensajes::error('No se puede eliminar, el registro se encuentra en uso en el sistema');
            }
        }else{
            Mensajes::error("No existen registros para eliminar");
        }
    }
}
