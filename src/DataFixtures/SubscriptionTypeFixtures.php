<?php

namespace App\DataFixtures;

use App\Entity\SubscriptionType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubscriptionTypeFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {

        $subscriptionTypes = require __DIR__ . '/../../data/subscriptionTypesData.php';
         if (count($subscriptionTypes) === 0) {
            return;
        }

        foreach ($subscriptionTypes as $subscriptionType) {
            $newSubscriptionType = new SubscriptionType();

            $newSubscriptionType
                            ->setName($subscriptionType['name'])
                            ->setCode($subscriptionType['code'])
                            ->setDescription($subscriptionType['description']);

            $manager->persist($newSubscriptionType);
        }

        $manager->flush();
    }
}
