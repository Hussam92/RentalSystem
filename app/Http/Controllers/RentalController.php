<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRentalRequest;
use App\Http\Requests\UpdateRentalRequest;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\Response;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Facades\Invoice;

class RentalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRentalRequest  $request
     * @return Response
     */
    public function store(StoreRentalRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rental  $rental
     * @return Response
     */
    public function show(Rental $rental)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rental  $rental
     * @return Response
     */
    public function edit(Rental $rental)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRentalRequest  $request
     * @param  \App\Models\Rental  $rental
     * @return Response
     */
    public function update(UpdateRentalRequest $request, Rental $rental)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rental  $rental
     * @return Response
     */
    public function destroy(Rental $rental)
    {
        //
    }

    public function invoice(Rental $rental): Response
    {
        $invoice = Invoice::make()->buyer(new Buyer([
            'name' => 'Gast',
        ]))->payUntilDays(0);

        $apartment = $rental->apartment;
        $title = sprintf('%s %s %s', $apartment->name, $apartment->street, $apartment->zip);

        collect($rental->begins_at->daysUntil($rental->ends_at)->toArray())->map(function (Carbon $date) use ($invoice, $rental, $title) {
            $invoiceItem = (new InvoiceItem())->title($title)
                ->pricePerUnit($rental->price_per_day / 1.19)
                ->units($date->format('d.m-Y'))
                ->taxByPercent(19);

            $invoice->addItem($invoiceItem);
        });

        return $invoice->stream();
    }
}
