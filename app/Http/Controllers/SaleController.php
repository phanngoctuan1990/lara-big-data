<?php

namespace App\Http\Controllers;

use App\Jobs\Sale\ImportSales;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function import(Request $request)
    {
        $uploadedFile = $request->file('file');
        $filename = 'sale_csv_' . uniqid() . '.' . $uploadedFile->getClientOriginalExtension();
        $uploadedFile->move(storage_path('tmp'), $filename);
        $filePath = storage_path('tmp/' . $filename);
        ImportSales::dispatch($filePath);
        return redirect()->back()->with('success', 'Sales uploaded successfully.');
    }
}
