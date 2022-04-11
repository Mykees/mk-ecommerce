<?php

namespace App\Tests\Entity;

use App\DataFixtures\CategoryFixtures;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryTest extends KernelTestCase
{

    public $validator;

    /**
     * @var AbstractDatabaseTool
     */
    protected $databaseTool;
    /**
     * @var EntityManager
     */
    protected $entityManager;


    private function getCategoryEntity (): Category {
        return (new Category())
            ->setName('Category 20')
            ->setSlug('category-20')
            ->setOnline(true)
        ;
    }

    public function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->validator = self::getContainer()->get(ValidatorInterface::class);
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get('default');
    }

    private function assertHasErrors ( Category $category, int $number=0 ) {
        self::bootKernel();
        $error = $this->validator->validate($category);
        $this->assertCount($number, $error);
    }

    public function testCategoryWithBlankName() {
        $this->assertHasErrors($this->getCategoryEntity()->setName(''), 1);
    }

    public function testCategoryWithBadSlugFormat() {
        $this->assertHasErrors($this->getCategoryEntity()->setSlug('Slug%%BadèFOrmat'), 1);
    }


    public function testCategorySlugWithGoodFormat() {
        $this->assertHasErrors($this->getCategoryEntity()->setSlug('cate-with-good-slug'), 0);
    }
}