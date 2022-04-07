<?php

namespace App\Tests\Entity;

use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\ProductFixtures;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductTest extends KernelTestCase
{
    public $validator;

    /**
     * @var AbstractDatabaseTool
     */
    protected $databaseTool;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;


    private function getProductEntity (): Product
    {
        return (new Product())
            ->setDescription('Un description')
            ->setExcerpt('une description très courte')
            ->setName('Product')
            ->setSlug('product')
            ->setCreatedAt(new \DateTime('now'))
            ->setUpdatedAt(new \DateTime('now'))
            ->setPrice(200)
            ->setOnline(1)
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
//         $this->validator    = Validation::createValidatorBuilder()->enableAnnotationMapping(true)->addDefaultDoctrineAnnotationReader()->getValidator();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get('default');
    }

    private function assertHasErrors ( Product $product, int $number=0 ) {
        self::bootKernel();
        $error = $this->validator->validate($product);
        $this->assertCount($number, $error);
    }


    public function testProductWithBlankName() {
        $this->assertHasErrors($this->getProductEntity()->setName(''), 1);
    }


    public function testProductSlugWithBadFormat() {
        $this->assertHasErrors($this->getProductEntity()->setSlug('MAuvais-Slug'),1);
    }

    public function testProductSlugWithGoodFormat() {
        $this->assertHasErrors($this->getProductEntity()->setSlug('un-bon-slug-01'),0);
    }

    public function testProductSlugIsAutomaticallyGenerated() {
        $product = (new Product())
            ->setName('Nouveau Produit')
            ->setDescription('Un description')
            ->setExcerpt('une description très courte')
            ->setCreatedAt(new \DateTime('now'))
            ->setUpdatedAt(new \DateTime('now'))
            ->setPrice(200)
        ;
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->assertEquals("nouveau-produit",$product->getSlug());
    }


    public function testProductSlugIsUniq() {
        $this->databaseTool->loadFixtures([ProductFixtures::class]);
        $this->assertHasErrors($this->getProductEntity()->setSlug('product0'),1);
    }


    public function testProductWithNegativePrice() {
        $this->assertHasErrors($this->getProductEntity()->setPrice(-1),1);
    }

    public function testProductPriceEqualToZero() {
        $this->assertHasErrors($this->getProductEntity()->setPrice(0),0);
    }

    public function testProductCategory() {
        $this->databaseTool->loadFixtures([ProductFixtures::class,CategoryFixtures::class]);
        $category = self::getContainer()->get(CategoryRepository::class)->findOneBy(['slug'=>'category9']);
        $this->assertHasErrors($this->getProductEntity()->setCategory($category),0);
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}