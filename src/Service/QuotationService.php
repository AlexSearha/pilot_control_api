<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\Quotation;
use App\Repository\QuotationRepository;
use App\Security\Voter\QuotationVoter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class QuotationService extends AbstractController
{
    private $errorToStringify = [];

    public function __construct(
        private QuotationRepository $quotationRepo,
        private CompanyService $companyService,
        private ProjectService $projectService,
        private CompanyClientService $companyClientService,
        private UserService $userService,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em,
        private QuotationItemService $quotationItemService
    ) {}

    public function getAllQuotations()
    {
        return $this->quotationRepo->findBy([], ['title' => 'ASC']);
    }

    public function getClientQuotations(?Company $company) : array
    {
        return $this->quotationRepo->findBy(['company' => $company->getId()], ['createAt' => 'ASC']);
    }

    public function findQuotationByUuid(string $quotationUuuid) : Quotation
    {
        $quotation =  $this->quotationRepo->findOneBy(['uuid' => $quotationUuuid]);

        $this->isQuotationExist($quotation);

        return $quotation;
    }

    public function getOneClientQuotation(?Company $company, ?Quotation $quotation) : object
    {
        $this->companyService->isCompanyExist($company);
        $this->isQuotationExist($quotation);

        return $quotation;
    }

    public function createClientQuotation(?Company $company, array $payload): Quotation
    {
        $this->companyService->isCompanyExist($company);

        if (count($payload) === 0 ) {
            throw new \Exception('Aucune donnée à traiter', Response::HTTP_BAD_REQUEST);
        }

        if (!isset($payload['quotationItems'])) {
            throw new \Exception('Aucun article de renseigné', Response::HTTP_BAD_REQUEST);
        }

        $newQuotation = new Quotation();
        $newQuotation->setCompany($company);

        foreach ($payload['quotationItems'] as $quotationItemPayload) {
            /** @var array $quotationItemPayload */
            $newQuotationItem = $this->quotationItemService->createQuotationItem($newQuotation, $quotationItemPayload);
            $newQuotation->addQuotationItem($newQuotationItem);
        }

        if (isset($payload['title'])) {
            $newQuotation->setTitle($payload['title']);
        }
        if (isset($payload['description'])) {
            $newQuotation->setDescription($payload['description']);
        }
        if (isset($payload['status'])) {
            $newQuotation->setStatus($payload['status']);
        }
        if (isset($payload['totalAmount'])) {
            $newQuotation->setTotalAmount($payload['totalAmount']);
        }
        if (isset($payload['validUntil'])) {
            $newQuotation->setValidUntil($payload['validUntil']);
        }
        if (isset($payload['discount'])) {
            $newQuotation->setDiscount($payload['discount']);
        }
        if (isset($payload['tax'])) {
            $newQuotation->setTax($payload['tax']);
        }
        if (isset($payload['comments'])) {
            $newQuotation->setComments($payload['comments']);
        }
        if (isset($payload['project'])) {
            $project = $this->projectService->getOneClientProject($payload['project']);
            $newQuotation->setProject($project);
        }
        if (isset($payload['currency'])) {
            $currency = null; // TODO: A renseigner des que Currency est OK
            $newQuotation->setCurrency($currency);
        }
        if (isset($payload['companyClient'])) {
            $companyClient = $this->companyClientService->getCompanyClient($company->getUuid(), $payload['companyClient']);
            $newQuotation->setCompanyClient($companyClient);
        }
        if (isset($payload['user'])) {
            $user = $this->userService->getOneUser($payload['user']);
            $newQuotation->setCreatedBy($user);
        }

        $errors = $this->validator->validate($newQuotation);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->persist($newQuotation);
            $this->em->flush();
            $this->em->refresh($newQuotation);

            return $newQuotation;

        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);

        }
    }

    public function updateClientQuotation(?Company $company, ?Quotation $quotation, array $payload) : Quotation
    {
        $this->companyService->isCompanyExist($company);
        $this->isQuotationExist($quotation);

        if (count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_NOT_FOUND);
        }

        if (isset($payload['quotationItemsAdd'])) {
            foreach ($payload['quotationItemsAdd'] as $quotationItemPayload) {
                /** @var array $quotationItemPayload */
                $newQuotationItem = $this->quotationItemService->createQuotationItem($quotation, $quotationItemPayload);
                $quotation->addQuotationItem($newQuotationItem);
            }
        }

         if (isset($payload['quotationItemsRemove'])) {
            foreach ($payload['quotationItemsRemove'] as $quotationItemUuid) {

                $quotationItem = $this->quotationItemService->findClientQuotationItemByUuid($quotationItemUuid);
                $quotation->removeQuotationItem($quotationItem);
                $this->quotationItemService->deleteQuotationItem($quotationItem);
            }
        }

        if (isset($payload['title'])) {
            $quotation->setTitle($payload['title']);
        }
        if (isset($payload['description'])) {
            $quotation->setDescription($payload['description']);
        }
        if (isset($payload['status'])) {
            $quotation->setStatus($payload['status']);
        }
        if (isset($payload['totalAmount'])) {
            $quotation->setTotalAmount($payload['totalAmount']);
        }
        if (isset($payload['validUntil'])) {
            $quotation->setValidUntil($payload['validUntil']);
        }
        if (isset($payload['discount'])) {
            $quotation->setDiscount($payload['discount']);
        }
        if (isset($payload['tax'])) {
            $quotation->setTax($payload['tax']);
        }
        if (isset($payload['comments'])) {
            $quotation->setComments($payload['comments']);
        }
        if (isset($payload['project'])) {
            $project = $this->projectService->getOneClientProject($payload['project']);
            $quotation->setComments($project);
        }
        if (isset($payload['currency'])) {
            $currency = null; // TODO: A renseigner des que Currency est OK
            $quotation->setCurrency($currency);
        }
        if (isset($payload['companyClient'])) {
            $companyClient = $this->companyClientService->getCompanyClient($company->getUuid(), $payload['companyClient']);
            $quotation->setCompanyClient($companyClient);
        }
        if (isset($payload['user'])) {
            $user = $this->userService->getOneUser($payload['user']);
            $quotation->setCreatedBy($user);
        }

        $errors = $this->validator->validate($quotation);

        if (count($errors) > 0) {

            foreach ($errors as $error) {
                $this->errorToStringify[] = $error->getMessage();
            }

            throw new \Exception(implode(',', $this->errorToStringify), Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->em->flush();
            $this->em->refresh($quotation);

            return $quotation;

        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);

        }

    }

    public function deleteClientQuotation(?Company $company, ?Quotation $quotation) : void
    {
        $this->companyService->isCompanyExist($company);
        $this->isQuotationExist($quotation);

        $quotation->setDeletedAt(new DateTimeImmutable());

         try {
            $this->em->flush();

        } catch (\Exception $e) {

            throw new \Exception('Une erreur est survenue', Response::HTTP_BAD_REQUEST);

        }

    }

    public function deleteClientQuotations(?Company $company, array $payload): void
    {
        $this->companyService->isCompanyExist($company);

        if (!isset($payload['quotations']) || count($payload) === 0) {
            throw new \Exception("Aucune donnée à traiter", Response::HTTP_NOT_FOUND);
        }

        foreach ($payload['quotations'] as $quotation) {
            $findClientQuotation = $this->quotationRepo->findOneBy(['uuid' => $quotation]);
            $this->denyAccessUnlessGranted(QuotationVoter::DELETE, $findClientQuotation);

            $this->deleteClientQuotation($company, $findClientQuotation);
        }
    }

    public function isQuotationExist(?Quotation $quotation) : Quotation
    {
        if (!$quotation) {
            throw new \Exception("Article inconnu", Response::HTTP_NOT_FOUND);
        }

        return $quotation;
    }
}
