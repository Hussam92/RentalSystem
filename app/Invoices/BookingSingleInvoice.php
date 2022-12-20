<?php

namespace App\Invoices;

use App\Models\Booking;
use Carbon\Carbon;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Facades\Invoice;

class BookingSingleInvoice
{
    public function __invoke(Booking $booking): \LaravelDaily\Invoices\Invoice
    {
        $invoice = Invoice::make()->buyer(new Buyer([
            'name' => 'Gast',
        ]))
            ->payUntilDays(0)
            ->filename(self::getName($booking))
            ->name(self::getName($booking));

        $apartment = $booking->apartment;
        $title = sprintf('%s %s %s', $apartment->name, $apartment->street, $apartment->zip);

        $taxIncluded = config('invoices.tax_included');

        collect($booking->begins_at->daysUntil($booking->ends_at)->toArray())->sum(function (Carbon $date) use ($taxIncluded, &$invoice, $booking, $title) {
            $invoiceItem = (new InvoiceItem())->title($title)
                ->pricePerUnit($taxIncluded ? $booking->price_per_day / 1.19 : $booking->price_per_day)
                ->units($date->format('d.m.Y'))
                ->taxByPercent(19);

            $invoice->addItem($invoiceItem);
        });

        return $invoice->render();
    }

    public static function getName(Booking $booking): string
    {
        return sprintf('%s-%s_%s', $booking->begins_at->format('Y-m-d'), str_replace(' ', '_', $booking->apartment->street), $booking->apartment->name);
    }

    public static function getRoute(Booking $booking): string
    {
        return route('get.bookings.single.download', $booking->id);
    }
}
