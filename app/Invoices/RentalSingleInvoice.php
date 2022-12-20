<?php

namespace App\Invoices;

use App\Models\Rental;
use Carbon\Carbon;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Facades\Invoice;

class RentalSingleInvoice
{
    public function __invoke(Rental $rental): \LaravelDaily\Invoices\Invoice
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

        return $invoice;
    }

}
