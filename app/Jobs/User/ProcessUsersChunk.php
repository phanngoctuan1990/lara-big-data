<?php

namespace App\Jobs\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Writer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Bus\Batchable;

class ProcessUsersChunk implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private array $users, private string $filename)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // If the file doesn't exist yet, add a header row
        if (!Storage::disk('local')->exists($this->filename)) {
            $csv = Writer::createFromString('');
            $csv->insertOne(['ID', 'NAME', 'Email']); // Replace with your own column names
            Storage::disk('local')->put($this->filename, $csv->getContent());
        }

        // Append the data to the CSV file
        $csv = Writer::createFromPath(storage_path('app/' . $this->filename), 'a+');
        $csv->insertAll($this->users);
    }
}
