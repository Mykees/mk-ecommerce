<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase {

    const ADMIN_USER_EMAIL = "user0@domain.com";

    public function testDisplayLoginPage () {
        $client = static::createClient();

        $client->request('GET','/login');
        $this->assertSelectorTextContains('h1','Connexion');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }


    public function testAnonymousIsRedirectToLogin () {
        $client = static::createClient();

        $client->request('GET','/admin');
        $this->assertResponseRedirects("/login");
    }



    public function testLoginWithBadCredentials () {
        $client  = static::createClient();
        $crawler = $client->request('GET','/login');
        
        $form = $crawler->selectButton('Se connecter')->form([
            '_username'=>'john@doe.fr',
            '_password'=>'mauvaispassword'
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }


    public function testSuccessLogin () {

        $client  = static::createClient();
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get('default');
        $databaseTool->loadFixtures([UserFixtures::class]);

        $crawler = $client->request('GET','/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['_username']->setValue('user0@domain.com');
        $form['_password']->setValue('wwwwwwww');
        $client->submit($form);


        $this->assertResponseRedirects('/admin');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }



    public function testRegisterUser () {
        $client  = static::createClient();

        $crawler = $client->request('GET',"/register");
        $form = $crawler->selectButton("S'inscrire")->form();
        $form['registration_form[email]']->setValue('newUser@domain.com');
        $form['registration_form[password][first]']->setValue('wwwwwwww');
        $form['registration_form[password][second]']->setValue('wwwwwwww');
        $client->submit($form);

        $user = self::getContainer()->get(UserRepository::class)->findBy([],['id'=>'DESC'],1,0);
        $users = self::getContainer()->get(UserRepository::class)->count([]);
        
        $this->assertEquals('newUser@domain.com',$user[0]->getEmail());
        $this->assertEquals(11, $users);
    }


    public function testLogoutUser() {
        $client = static::createClient();
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get('default');
        $databaseTool->loadFixtures([UserFixtures::class]);

        $user = self::getContainer()->get(UserRepository::class)->findOneBy(['email'=>self::ADMIN_USER_EMAIL]);

        $client->loginUser($user);

        $crawler = $client->request('GET','/admin');
        $linkCrawler = $crawler->selectLink('Sign out')->link();
        $client->click($linkCrawler);


        $this->assertResponseRedirects('http://localhost/');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
    
}