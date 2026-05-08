<?php

require_once BASE_PATH . '/models/Tax.php';

class TaxService
{
    private Tax $taxModel;

    public function __construct()
    {
        $this->taxModel = new Tax();
    }

    public function generateTaxReport(int $ownerId, int $year): array
    {
        return $this->taxModel->generate($ownerId, $year);
    }

    public function getOwnerTaxHistory(int $ownerId): array
    {
        return $this->taxModel->getByOwner($ownerId);
    }
}