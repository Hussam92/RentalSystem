<?php

namespace App\Filament\Actions;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Facades\Invoice;

class ExportBookingAction extends Action
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'download';
    }

    /**
     * @throws BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('Download'));

        $this->modalHeading(fn (): string => __('filament-support::actions/download.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalButton(__('filament-support::actions/download.single.modal.actions.download.label'));

        $this->successNotificationTitle(__('filament-support::actions/download.single.messages.download'));

        $this->color('secondary');

        $this->icon('heroicon-s-download');

        //$this->hidden(static function (Model $record): bool {
        //    if (! method_exists($record, 'trashed')) {
        //        return false;
        //    }
//
        //    return $record->trashed();
        //});

        $this->action(function () {
            $this->process(static function (Booking $record) {
                $invoice = Invoice::make()->buyer(new Buyer([
                    'name' => 'Gast',
                ]));

                $apartment = $record->apartment;
                $title = sprintf('%s %s %s', $apartment->name, $apartment->street, $apartment->zip);
                $invoiceItem = (new InvoiceItem())->title($title)->pricePerUnit((int) $record->price_per_day);
                $invoice->addItem($invoiceItem);

                $record->begins_at->daysUntil($record->ends_at)->map(function (Carbon $date) use ($invoice, $record, $title) {
                    $invoiceItem = (new InvoiceItem())->title($title)->pricePerUnit($record->price_per_day);
                    $invoice->addItem($invoiceItem);
                });

                $invoice->stream();
            });

            \Log::warning('Starting Download');

            $this->success();
        });
    }
}
