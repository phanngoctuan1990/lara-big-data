<?php

namespace App\Http\Controllers;

use App\Models\TrackingExport;
use App\Jobs\User\ExportOneMillionUsers;
use App\Jobs\User\ExportTwoMillionUsers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Bus;

use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    const EXPORT_LESS_THAN_ONE_MILIONS = 1;
    const EXPORT_LESS_THAN_TWO_MILIONS = 2;

    public function export()
    {
        $fileName = now()->timestamp . '_users.csv';
        $trackingExport = TrackingExport::create(['file_name' => $fileName]);
        if (request()->type == self::EXPORT_LESS_THAN_ONE_MILIONS) {
            ExportOneMillionUsers::dispatch($fileName, $trackingExport);
        }
        if (request()->type == self::EXPORT_LESS_THAN_TWO_MILIONS) {
            $batchChunk = Bus::batch([new ExportTwoMillionUsers(
                $fileName,
                $trackingExport
            )])
                ->onQueue('process_chunk')
                ->name('Chunk Users ' . $fileName)
                ->dispatch();

            $trackingExport->batch_process_chunk_id = $batchChunk->id;
            $trackingExport->save();
            sleep(1.5);
        }

        $trackingExport = TrackingExport::find($trackingExport->id);
        return response()->json([
            'message' => 'Handling export data',
            'tracking_id' => $trackingExport->id,
        ]);
    }

    public function trackingExport(TrackingExport $trackingExport)
    {
        $mesage = 'downloading file ' . $trackingExport->file_name;
        if ($trackingExport->batch_process_chunk_id) {
            $batchChunk = Bus::findBatch($trackingExport->batch_process_chunk_id);
            $batchWriteCsv = Bus::findBatch($trackingExport->batch_write_csv_id);
            $status = 0;
            if ($batchChunk->finishedAt && $batchWriteCsv->finishedAt) {
                $status = TrackingExport::EXPORTED_STATUS;
                $mesage = 'File downloaded successfully';
            }
            return response()->json([
                'message' => $mesage,
                'status' => $status,
                'tracking_id' => $trackingExport->id
            ]);
        }

        if ($trackingExport->status) {
            $mesage = 'File downloaded successfully';
        }
        return response()->json([
            'message' => $mesage,
            'status' => $trackingExport->status,
            'tracking_id' => $trackingExport->id
        ]);
    }

    public function download(TrackingExport $trackingExport)
    {
        ini_set('memory_limit', '512M');
        $file = Storage::disk('local')->get($trackingExport->file_name);

        $response = new StreamedResponse(function () use ($file) {
            echo $file;
        });

        $response->headers->set('Content-Type', Storage::disk('local')->mimeType($trackingExport->file_name));
        $response->headers->set('Content-Length', Storage::disk('local')->size($trackingExport->file_name));
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $trackingExport->file_name . '"');

        return $response;
    }
}
