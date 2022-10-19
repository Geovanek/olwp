<?php

namespace App\Http\Controllers;

use App\Http\Requests\BeerRequest;
use App\Jobs\ExportJob;
use App\Jobs\SendExportMailJob;
use App\Jobs\StoreExportDataJob;
use App\Services\PunkapiService;
use Carbon\Carbon;

class BeerController extends Controller
{
    public function __construct(public BeerRequest $request, public PunkapiService $service)
    {}

    public function index()
    {
        return $this->service->getBeers(... $this->request->validated());
    }

    public function export()
    {
        $filename = "beers-export--" . Carbon::now()->format('d-m-Y H:i:s') . ".xlsx";

        ExportJob::withChain([
            new SendExportMailJob($filename),
            new StoreExportDataJob(auth()->user(), $filename),
        ])->dispatch($this->request->validated(), $filename);
        
        return 'Relatório criado';
    }
}
