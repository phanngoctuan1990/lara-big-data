<?php

namespace App\Jobs\Sale;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;

class ImportSales implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private $filePath)
    {
        $this->queue = 'import_sales';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $csv = Reader::createFromPath($this->filePath);
        $csv->setHeaderOffset(0);
        $sales = $csv->getRecords();

        foreach ($sales as $sale) {
            // Insert the record into the database
            info($sale);
        }
    }
}
