<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;

class AdminPageControllerTest extends WebTestCase {

    const ADMIN_USER_EMAIL = "user0@domain.com";
    const SIMPLE_USER_EMAIL = "user1@domain.com";


    public function testAuthenticatedUserRoleAdminAccess () {

        $client = static::createClient();
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get('default');
        $databaseTool->loadFixtures([UserFixtures::class]);
    
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);

        $client->loginUser($userAdmin);

        $client->request('GET','/admin');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testAuthenticatedSimpleUserRoleAdminAccess () {

        $client = static::createClient();
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get('default');
        $databaseTool->loadFixtures([UserFixtures::class]);
    
        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::SIMPLE_USER_EMAIL]);

        $client->loginUser($user);

        $client->request('GET','/admin');

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }


}