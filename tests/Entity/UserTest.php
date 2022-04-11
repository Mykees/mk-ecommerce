<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\DataFixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserTest extends KernelTestCase {

    public $validator;

    /**
     * @var AbstractDatabaseTool
     */
    protected $databaseTool;


    public function setUp(): void
    {
        parent::setUp();
        $this->validator = self::getContainer()->get(ValidatorInterface::class);
        // $this->validator    = Validation::createValidatorBuilder()->enableAnnotationMapping(true)->addDefaultDoctrineAnnotationReader()->getValidator();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get('default');
    }


    private function getUserEntity () {
        return (new User())
            ->setEmail('test@test.fr')
            ->setPassword('00000000')
        ;
    }

    private function assertHasErrors ( User $user, int $number=0 ) {
        self::bootKernel();
        $error = $this->validator->validate($user);
        // dump($error);
        $this->assertCount($number, $error);
    }


    public function testValidUser () {

        $this->assertHasErrors($this->getUserEntity(), 0);

    }

    public function testInvalidUserEmailFormat () {
        $this->assertHasErrors($this->getUserEntity()->setEmail('test'), 1);
    }

    public function testInvalidUserBlankEmail () {
        $this->assertHasErrors($this->getUserEntity()->setEmail(''), 1);
    }


    public function testInvalidAlreadyExistUser () {
        $this->databaseTool->loadFixtures([UserFixtures::class]);
        $this->assertHasErrors($this->getUserEntity()->setEmail("user1@domain.com"), 1);
    }


    public function testInvalidBlankUserPassword () {
        $this->assertHasErrors($this->getUserEntity()->setPassword(""), 2);
    }

    public function testValidLengthUserPassword () {
        $this->assertHasErrors($this->getUserEntity()->setPassword("wwwwwwwwww"), 0);
    }

    public function testInvalidLengthUserPassword () {
        $this->assertHasErrors($this->getUserEntity()->setPassword("www"), 1);
    }

}