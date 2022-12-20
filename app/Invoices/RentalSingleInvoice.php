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
        ]))
            ->payUntilDays(0)
            ->filename(self::getName($rental))
            ->name(self::getName($rental));

        $apartment = $rental->apartment;
        $title = sprintf('%s %s %s', $apartment->name, $apartment->street, $apartment->zip);

        $taxIncluded = config('invoices.tax_included');

        collect($rental->begins_at->daysUntil($rental->ends_at)->toArray())->sum(function (Carbon $date) use ($taxIncluded, &$invoice, $rental, $title) {
            $invoiceItem = (new InvoiceItem())->title($title)
                ->pricePerUnit($taxIncluded ? $rental->price_per_day / 1.19 : $rental->price_per_day)
                ->units($date->format('d.m.Y'))
                ->taxByPercent(19);

            $invoice->addItem($invoiceItem);
        });

        return $invoice->render();
    }

    public static function getName(Rental $rental): string
    {
        return sprintf('%s-%s_%s', $rental->begins_at->format('Y-m-d'), str_replace(' ', '_', $rental->apartment->street), $rental->apartment->name);
    }

    public static function getRoute(Rental $rental): string
    {
        return route('get.rentals.single.download', $rental->id);
    }
}
