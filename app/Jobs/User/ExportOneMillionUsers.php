<?php

namespace App\Jobs\User;

use App\Models\TrackingExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use League\Csv\Writer;

class ExportOneMillionUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $fileName,
        private TrackingExport $trackingExport
    ) {
        $this->queue = 'process_chunk';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Define the chunk size
        $chunkSize = 5000;

        // Open a new file for writing
        $file = fopen(storage_path('app/' . $this->fileName), 'w');

        // Create a new CSV writer instance
        $csv = Writer::createFromStream($file);

        // Write the header row
        $csv->insertOne(['ID', 'Name', 'Email']);

        // Query the users table in chunks
        DB::table('users')->select('id', 'name', 'email')
            // ->where('id', '<=', 1000000)
            ->orderBy('id')
            ->chunk($chunkSize, function ($users) use ($csv) {
                // Iterate over the users and write each row to the CSV file
                foreach ($users as $user) {
                    $csv->insertOne([$user->id, $user->name, $user->email]);
                }
            });

        // Close the file
        fclose($file);
        $this->trackingExport->update([
            'status' => TrackingExport::EXPORTED_STATUS
        ]);
    }
}
