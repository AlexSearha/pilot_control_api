<?php

namespace App\Service;

use App\Repository\InvoiceItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InvoiceItemService extends AbstractController
{
    public function __construct(
        private InvoiceItemRepository $invoiceItemRepo
    ) {}

    public function getAllClientInvoiceItems() : array
    {

    }
}
