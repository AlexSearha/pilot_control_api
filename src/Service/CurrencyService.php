<?php

namespace App\Service;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CurrencyService extends AbstractController
{
    private $errorsToStrigify = [];

    public function __construct(
        private CurrencyRepository $currencyRepo,
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ) {
    }

    public function getAllCurrencies()
    {
        return $this->currencyRepo->findBy([], ["name" => "ASC"]);
    }

    public function getOneCurrency(?Currency $currency): object
    {
        $this->isCurrencyExist($currency);

        return $currency;
    }

    public function createCurrency(array $payload): Currency
    {
        if (count($payload) === 0) {
            throw new \Exception("Aucune données à traiter", Response::HTTP_NOT_FOUND);
        }

        $newCurrency = new Currency();

        if (isset($payload['name'])) {
            $newCurrency->setName($payload['name']);
        }
        if (isset($payload['description'])) {
            $newCurrency->setDescription($payload['description']);
        }

        $errors = $this->validator->validate($newCurrency);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorsToStrigify[] = $error->getMessage();
            }

            throw new \Exception(implode(",", $this->errorsToStrigify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newCurrency);
            $this->em->flush();
            $this->em->refresh($newCurrency);

            return $newCurrency;

        } catch (\Exception $e) {
            throw new \Exception("Une erreur est survenue", Response::HTTP_BAD_REQUEST);
        }
    }

    public function updateCurrency(?Currency $currency, array $payload): Currency
    {
        $this->isCurrencyExist($currency);

        if (count($payload) === 0) {
            throw new \Exception("Aucune données à traiter", Response::HTTP_NOT_FOUND);
        }

        if (isset($payload['name'])) {
            $currency->setName($payload['name']);
        }
        if (isset($payload['description'])) {
            $currency->setDescription($payload['description']);
        }

        $errors = $this->validator->validate($currency);

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorsToStrigify[] = $error->getMessage();
            }

            throw new \Exception(implode(",", $this->errorsToStrigify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->flush();
            $this->em->refresh($currency);

            return $currency;

        } catch (\Exception $e) {
            throw new \Exception("Une erreur est survenue", Response::HTTP_BAD_REQUEST);
        }

    }

    public function deleteCurrency(?Currency $currency): void
    {

        $this->isCurrencyExist($currency);

        $currency->setDeletedAt(new DateTimeImmutable());

        try {
            $this->em->flush();

        } catch (\Exception $e) {
            throw new \Exception("Une erreur est survenue", Response::HTTP_BAD_REQUEST);
        }
    }

    public function isCurrencyExist(?Currency $currency): void
    {
        if (!$currency) {
            throw new \Exception("La devise est inconnue", Response::HTTP_NOT_FOUND);
        }
    }
}
