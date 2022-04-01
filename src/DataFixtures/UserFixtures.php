<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture {

    public function load ( ObjectManager $manager ) {
        for( $i=0; $i < 10; $i++ ) {
            $user = (new User())
                ->setEmail("user$i@domain.com")
                ->setPassword('$2y$10$ut2QgK4c/os9MGgWxoNioe.0qPQMS8Rkv3elgOHH.mA1HW/JW4PMC') //brcrypt hasher of : wwwwwwww
                ->setRoles($i === 0 ? ['ROLE_ADMIN'] : ['ROLE_USER'])
            ;
            $manager->persist($user);
        }
        $manager->flush();
    }
}