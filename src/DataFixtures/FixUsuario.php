<?php

namespace App\DataFixtures;

use App\Entity\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class FixUsuario extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $arUsuario = $manager->getRepository(Usuario::class)->findOneBy(array('codigoUsuarioPk' => 'semantica'));
        if(!$arUsuario) {
            $arUsuario = new Usuario();
            $arUsuario->setCodigoUsuarioPk('semantica');
            $arUsuario->setNombres("semantica");
            $arUsuario->setClave('smt48903');
            $arUsuario->setCorreo('investigacion@semantica.com.co');
            $arUsuario->setCodigoRolFk('ROLE_ADMIN');
            $manager->persist($arUsuario);
        }
        $manager->flush();
    }
}
