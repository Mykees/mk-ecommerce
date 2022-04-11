<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\CategoryRepository;
use App\DataFixtures\CategoryFixtures;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;


class CategoryControllerTest extends WebTestCase
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


    public function testCategoryListPage () {
        $this->databaseTool->loadFixtures([UserFixtures::class,CategoryFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);

        $this->client->request('GET','/admin/category/');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testCreateCategory () {
        $this->databaseTool->loadFixtures([UserFixtures::class,CategoryFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);

        $crawler = $this->client->request('GET','/admin/category/create');
        $form = $crawler->selectButton("Enregistrer")->form();
        $form['category[name]']->setValue('test category');
        $form['category[online]']->setValue(true);
        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/category/');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }

    public function testCreateCategoryWithoutName () {
        $this->databaseTool->loadFixtures([UserFixtures::class,CategoryFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);

        $crawler = $this->client->request('GET','/admin/category/create');
        $form = $crawler->selectButton("Enregistrer")->form();
        $form['category[online]']->setValue(true);
        $this->client->submit($form);


        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', "Ajout d'une nouvelle categorie");
    }

    public function testUpdateCategory () {
        $this->databaseTool->loadFixtures([UserFixtures::class,CategoryFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $category  = self::getContainer()->get(CategoryRepository::class)->findOneBy(['slug'=>'category0']);

        $this->client->loginUser($userAdmin);

        $crawler = $this->client->request('GET','/admin/category/'.$category->getId().'/edit');
        $form = $crawler->selectButton("Enregistrer")->form();
        $form['category[name]']->setValue('category 100');
        $form['category[online]']->setValue(true);
        $this->client->submit($form);

        $updatedCategory  = self::getContainer()->get(CategoryRepository::class)->findOneBy(['slug'=>'category-100']);

        $this->assertResponseRedirects('/admin/category/');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->assertEquals($category->getId(), $updatedCategory->getId());
    }


    public function testDeleteCategory () {
        $this->databaseTool->loadFixtures([UserFixtures::class,CategoryFixtures::class]);
        //On connect l'admin
        $userAdmin = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);
        $this->client->loginUser($userAdmin);

        $crawler = $this->client->request('GET','/admin/category/');
        $form = $crawler->selectButton("button-delete-1")->form();
        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/category/');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);


        $categories = self::getContainer()->get(CategoryRepository::class)->findAll();
        $countCategories = count($categories);

        $this->assertEquals(9, $countCategories);
    }
}