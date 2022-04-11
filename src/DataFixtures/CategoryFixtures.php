<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        for( $i=0; $i < 10; $i++ ) {

            $category = (new Category())
                ->setName("Category".$i)
                ->setSlug("category".$i)
                ->setCreatedAt(new \DateTime())
                ->setOnline(true)
            ;
            $manager->persist($category);
            $manager->flush();
            $this->setReference('category'.$i,$category);
        }

    }
}