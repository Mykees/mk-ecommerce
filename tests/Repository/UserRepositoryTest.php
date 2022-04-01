<?php

namespace App\Tests\Repository;

use App\DataFixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\UserRepository as RepositoryUserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserRepository extends KernelTestCase {

    /**
     * @var AbstractDatabaseTool
     */
    protected $databaseTool;


    public function setUp(): void
    {
        parent::setUp();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get('default');
    }

    public function testCount () {
        self::bootKernel();

        $this->databaseTool->loadFixtures([UserFixtures::class]);

        $users = self::getContainer()->get(RepositoryUserRepository::class)->count([]);
        $this->assertEquals(10, $users);
    }
}