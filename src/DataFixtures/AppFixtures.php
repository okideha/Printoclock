<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Newsletter;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $users = [];
        $categories = [];
        $newsletters=[];
        $categoriesList = [
            "java",
            "javascript",
            "php",
            "python",
            "sql",
            "angular"
        ];

        for($i=0; $i<sizeOf($categoriesList) ; $i++){
            $category = new Category();
            $category->setName($categoriesList[$i]);
            $manager->persist($category);
            $categories[]=$category;
        }

        for($i=0; $i<5 ; $i++){
            $newsletter = new Newsletter();
            $newsletter->setName($faker->text(50));
            $newsletter->setContent($faker->text(1000));
            $newsletter->setImage('https://picsum.photos/seed/'.$newsletter->getName().'/200');
            $newsletter->setCategory($categories[$faker->numberBetween(0,5)]);            
            $manager->persist($newsletter);
            $newsletters[]=$newsletter;
        }

        for($i=0; $i<10 ; $i++){
            $user = new User();
            $user->setFirstname($faker->firstname());
            $user->setEmail($faker->email());
            $token = hash('sha256', uniqid());
            $user->setToken($token);
            $user->setRgpd(false);
            $user->addCategory($categories[$faker->numberBetween(0,5)]);
            $manager->persist($user);
            $users[]=$user;
        }

        $manager->flush();
        
    }
}
