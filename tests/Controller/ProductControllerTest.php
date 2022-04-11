<?php

namespace App\Tests\Controller;

use App\DataFixtures\CategoryFixtures;
use App\DataFixtures\ProductFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class ProductControllerTest extends WebTestCase
{
    const ADMIN_USER_EMAIL = "user0@domain.com";
    public $client;

    /**
     * @var AbstractDatabaseTool
     */
    protected $databaseTool;

    public function setUp(): void {
        parent::setUp();
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get('default');
    }

    public function testProductListPage() {
        $this->databaseTool->loadFixtures([UserFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);

        $this->client->request('GET','/admin/product/');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateProduct(){
        $this->databaseTool->loadFixtures([UserFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);

        $crawler = $this->client->request('GET','/admin/product/create');

        $form = $crawler->selectButton("Enregistrer")->form();
        $form['product[name]']->setValue('test Name');
        $form['product[price]']->setValue(125);
        $form['product[excerpt]']->setValue('test');
        $form['product[description]']->setValue('test');
        $form['product[online]']->setValue(true);
        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/product/');
    }

    public function testCreateProductWithBadPriceFormat() {
        $this->databaseTool->loadFixtures([UserFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);


        $crawler = $this->client->request('GET','/admin/product/create');
        $form = $crawler->selectButton("Enregistrer")->form();
        $form['product[name]']->setValue('test lol');
        $form['product[price]']->setValue('badFormat');
        $form['product[excerpt]']->setValue('test');
        $form['product[description]']->setValue('test');
        $form['product[online]']->setValue(true);
        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        //On revient sur la page de creation d'un produit
        $this->assertSelectorTextContains('h2', "Ajout d'un nouveau produit");
    }

    public function testCreateProductWithEmptyName() {
        $this->databaseTool->loadFixtures([UserFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);


        $crawler = $this->client->request('GET','/admin/product/create');
        $form = $crawler->selectButton("Enregistrer")->form();
        $form['product[price]']->setValue(125);
        $form['product[excerpt]']->setValue('test');
        $form['product[description]']->setValue('test');
        $form['product[online]']->setValue(true);
        $this->client->submit($form);

        $this->assertResponseIsSuccessful();
        //On revient sur la page de creation d'un produit
        $this->assertSelectorTextContains('h2', "Ajout d'un nouveau produit");
    }


    public function testCreateProductAddCategory () {
        $this->databaseTool->loadFixtures([UserFixtures::class,CategoryFixtures::class,ProductFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);

        $category = self::getContainer()->get(CategoryRepository::class)->findOneBy(['slug'=>'category9']);

        $crawler = $this->client->request('GET','/admin/product/create');
        $form = $crawler->selectButton("Enregistrer")->form();
        $form['product[name]']->setValue('test add new product');
        $form['product[price]']->setValue(125);
        $form['product[excerpt]']->setValue('test');
        $form['product[description]']->setValue('test');
        $form['product[online]']->setValue(true);
        $form['product[category]']->setValue($category->getId());
        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/product/');

        $lastProduct = self::getContainer()->get(ProductRepository::class)->findOneBy(array(),array('id'=>'DESC'),1,0);
        $this->assertEquals('category9',$lastProduct->getCategory()->getSlug());
    }


    public function testCreateProductWithUploadImage() {
        $this->databaseTool->loadFixtures([UserFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);


        $uploadedFile = new UploadedFile(
            __DIR__.'/../Assets/tablette.jpeg',
            'tablette.jpeg'
        );

        $crawler = $this->client->request('GET','/admin/product/create');
        $form = $crawler->selectButton("Enregistrer")->form();
        $form['product[name]']->setValue('test product name');
        $form['product[price]']->setValue(125);
        $form['product[excerpt]']->setValue('test');
        $form['product[description]']->setValue('test');
        $form['product[online]']->setValue(true);
        $form['product[dlimage]']->upload($uploadedFile);
        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/product/');
    }

    public function testCreateProductWithUploadBadFormatFile() {
        $this->databaseTool->loadFixtures([UserFixtures::class]);
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);

        $uploadedFile = new UploadedFile(
            __DIR__.'/../Assets/doc.csv',
            'doc.csv'
        );

        $crawler = $this->client->request('GET','/admin/product/create');
        $form = $crawler->selectButton("Enregistrer")->form();
        $form['product[name]']->setValue('test product name');
        $form['product[price]']->setValue(125);
        $form['product[excerpt]']->setValue('test');
        $form['product[description]']->setValue('test');
        $form['product[online]']->setValue(true);
        $form['product[dlimage]']->upload($uploadedFile);

        $this->client->submit($form);

        $this->assertSelectorTextContains('h2', "Ajout d'un nouveau produit");

    }




    public function testUpdateProductName() {
        $this->databaseTool->loadFixtures([UserFixtures::class,ProductFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);

        $product = self::getContainer()->get(ProductRepository::class)->findOneBy(['slug'=>'product0']);

        $crawler = $this->client->request('GET','/admin/product/'.$product->getId().'/edit');
        $form = $crawler->selectButton("Enregistrer")->form();
        $form['product[name]']->setValue('test update product name');

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/product/');
        $updateProduct = self::getContainer()->get(ProductRepository::class)->findOneBy(['name'=>'test update product name']);

        $this->assertNotNull($updateProduct);
        $this->assertEquals("test update product name",$updateProduct->getName());
    }


    public function testUpdateWithBadPriceFormat() {
        $this->databaseTool->loadFixtures([UserFixtures::class,ProductFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);

        $product = self::getContainer()->get(ProductRepository::class)->findOneBy(['slug'=>'product0']);

        $crawler = $this->client->request('GET','/admin/product/'.$product->getId().'/edit');
        $form = $crawler->selectButton("Enregistrer")->form();
        $form['product[price]']->setValue('bad price format');

        $this->client->submit($form);

        $this->assertSelectorTextContains('h2', "Edition d'un produit");
    }


    public function testDeleteProduct() {
        $this->databaseTool->loadFixtures([UserFixtures::class,ProductFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);


        $crawler = $this->client->request('GET','/admin/product/');
        $form = $crawler->selectButton("button-delete-1")->form();
        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/product/');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $products = self::getContainer()->get(ProductRepository::class)->findAll();
        $countProducts = count($products);

        $this->assertEquals(9, $countProducts);
    }
}