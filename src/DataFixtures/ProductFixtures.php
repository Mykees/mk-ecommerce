<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{


    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class
        ];
    }

    public function load(ObjectManager $manager)
    {
        for( $i=0; $i < 10; $i++ ) {
            $faker = Factory::create();

            $product = (new Product())
                ->setDescription($faker->text(200))
                ->setExcerpt($faker->sentence(15))
                ->setName('Product'.$i)
                ->setSlug('product'.$i)
                ->setCreatedAt(new \DateTime('now'))
                ->setUpdatedAt(new \DateTime('now'))
                ->setPrice($faker->randomNumber(2))
                ->setImage($faker->imageUrl(640, 480))
                ->setCategory($this->getReference('category'.$i))
            ;
            $manager->persist($product);
        }

        $manager->flush();
    }
}