<?php

namespace App\Jobs\User;

use App\Models\TrackingExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Bus;

class ExportTwoMillionUsers implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $fileName,
        private TrackingExport $trackingExport
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Define the chunk size
        $batchWriteCsv = Bus::batch([])
            ->onQueue('write_csv')
            ->name('Write CSV ' . $this->fileName)->dispatch();

        $this->trackingExport->batch_write_csv_id = $batchWriteCsv->id;
        $this->trackingExport->save();

        $chunkSize = 5000;
        // Query the users table in chunks
        DB::table('users')->select('id', 'name', 'email')
            ->orderBy('id')
            ->chunk($chunkSize, function ($users) use ($batchWriteCsv) {
                $users = Arr::map($users->toArray(), function ($user) {
                    return json_decode(json_encode($user), true);
                });
                // Iterate over the users and write each row to the CSV file
                $batchWriteCsv->add(new ProcessUsersChunk($users, $this->fileName));
            });
    }
}
