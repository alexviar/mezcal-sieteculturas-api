<?php

namespace App\Observers;

use App\Jobs\PostPurchase;
use App\Models\Purchase;

class PurchaseObserver
{
    public function created(Purchase $purchase)
    {
        if ($purchase->paid) {
            PostPurchase::dispatch($purchase)->afterCommit();
        }
    }

    public function updated(Purchase $purchase)
    {
        if ($purchase->wasChanged('paid') && $purchase->paid) {
            PostPurchase::dispatch($purchase)->afterCommit();
        }
    }
}
