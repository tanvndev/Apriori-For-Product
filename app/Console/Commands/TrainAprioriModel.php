<?php

namespace App\Console\Commands;

use App\Services\Interfaces\AprioriServiceInterface;
use Illuminate\Console\Command;

class TrainAprioriModel extends Command
{
    protected $signature = 'apriori:train';
    protected $description = 'Train the Apriori model';

    protected $aprioriService;

    public function __construct(AprioriServiceInterface $aprioriService)
    {
        parent::__construct();
        $this->aprioriService = $aprioriService;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->aprioriService->runApriori();
        $this->info('Apriori model trained successfully.');
    }
}
