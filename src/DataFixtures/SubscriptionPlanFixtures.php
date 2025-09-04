<?php

namespace App\DataFixtures;

use App\Entity\SubscriptionPlan;
use App\Repository\CurrencyRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubscriptionPlanFixtures extends Fixture
{

    public function __construct(
        private CurrencyRepository $currencyRepo
    )
    {}

    public function load(ObjectManager $manager): void
    {

        $subscriptionPlans = require __DIR__ . '/../../data/subscriptionPlansData.php';

        if (count($subscriptionPlans) === 0) {
            return;
        }

        $currency = $this->currencyRepo->findOneBy(['code' => 'EUR']);

        if (!$currency) {
            return;
        }

        foreach ($subscriptionPlans as $subscriptionPlan) {
            $newSubscriptionPlan = new SubscriptionPlan();

            $newSubscriptionPlan
                ->setCode($subscriptionPlan['code'])
                ->setName($subscriptionPlan['name'])
                ->setDescription($subscriptionPlan['description'])
                ->setPrice($subscriptionPlan['price'])
                ->setCurrency($currency)
                ->setMaxUser($subscriptionPlan['maxUsers'])
                ->setFeatures($subscriptionPlan['features']);

            $manager->persist($newSubscriptionPlan);
        }

        $manager->flush();
    }
}
