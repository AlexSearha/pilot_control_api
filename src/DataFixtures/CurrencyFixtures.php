<?php

namespace App\DataFixtures;

use App\Entity\Currency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CurrencyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $currencies = require __DIR__ . '/../../data/currenciesData.php';

        if (count($currencies) === 0) {
            return;
        }

        foreach ($currencies as $currency) {
            $newCurrency = new Currency();

            $newCurrency
                ->setName($currency['name'])
                ->setCode($currency['code'])
                ->setSymbol($currency['symbol']);

            $manager->persist($newCurrency);
        }

        $manager->flush();
    }
}
