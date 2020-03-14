<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;

class LoadUser extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $user1 = new User();
    $user1->setUsername('Philippe');
    $user1->setFullname('Philippe');
    $user1->setEmail('docsphilippe@gmail.com');
    $user1->setPassword(password_hash('philippe', PASSWORD_BCRYPT));
    $manager->persist($user1);

    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }
}
