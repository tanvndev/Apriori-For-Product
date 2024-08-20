<?php

namespace App\Services\Interfaces;


interface AprioriServiceInterface
{
    public function getTransactions(): array;
    public function runApriori(): void;
    public function getRecommendations(array $products): array;
}
