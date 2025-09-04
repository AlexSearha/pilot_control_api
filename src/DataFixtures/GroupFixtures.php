<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {}

     public function getDependencies(): array
    {
        return [
            CurrencyFixtures::class,
            SubscriptionTypeFixtures::class,
            SubscriptionPlanFixtures::class,
        ];
    }
}
