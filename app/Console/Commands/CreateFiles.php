<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateFiles extends Command
{
    protected $signature = 'command:cf';

    protected $description = 'Command description';

    public function handle()
    {
        // Artisan::call('make:model FinanceWalletModel');
        // Artisan::call('make:model FinanceWalletConsolidateMonthModel');
        // Artisan::call('make:model FinanceStatusModel');
        // Artisan::call('make:model FinanceTypeModel');
        // Artisan::call('make:model FinanceOriginTypeModel');
        // Artisan::call('make:model FinanceOriginModel');
        // Artisan::call('make:model FinanceGroupModel');
        // Artisan::call('make:model FinanceCategoryModel');
        // Artisan::call('make:model FinanceCategoryClosureModel');
        // Artisan::call('make:model FinanceItemModel');
        // Artisan::call('make:model FinanceInvoiceModel');
        // Artisan::call('make:model FinanceInvoiceItemModel');
        // Artisan::call('make:model FinanceListModel');
        // Artisan::call('make:model FinanceListItemModel');

        /* 
        # BASE
        // Artisan::call('make:request Finance/X/FinanceXStoreRequest');
        // Artisan::call('make:request Finance/X/FinanceXUpdateRequest');
        // Artisan::call('make:resource Finance/X/FinanceXResource');
        // Artisan::call('make:resource Finance/X/FinanceXCollection');

        Artisan::call('make:request Finance/Wallet/FinanceWalletStoreRequest');
        Artisan::call('make:request Finance/Wallet/FinanceWalletUpdateRequest');
        Artisan::call('make:resource Finance/Wallet/FinanceWalletResource');
        Artisan::call('make:resource Finance/Wallet/FinanceWalletCollection');

        Artisan::call('make:request Finance/WalletConsolidateMonth/FinanceWalletConsolidateMonthStoreRequest');
        Artisan::call('make:request Finance/WalletConsolidateMonth/FinanceWalletConsolidateMonthUpdateRequest');
        Artisan::call('make:resource Finance/WalletConsolidateMonth/FinanceWalletConsolidateMonthResource');
        Artisan::call('make:resource Finance/WalletConsolidateMonth/FinanceWalletConsolidateMonthCollection');

        // Artisan::call('make:request Finance/Status/FinanceStatusStoreRequest');
        // Artisan::call('make:request Finance/Status/FinanceStatusUpdateRequest');
        Artisan::call('make:resource Finance/Status/FinanceStatusResource');
        // Artisan::call('make:resource Finance/Status/FinanceStatusCollection');

        // Artisan::call('make:request Finance/Type/FinanceTypeStoreRequest');
        // Artisan::call('make:request Finance/Type/FinanceTypeUpdateRequest');
        Artisan::call('make:resource Finance/Type/FinanceTypeResource');
        // Artisan::call('make:resource Finance/Type/FinanceTypeCollection');

        // Artisan::call('make:request Finance/OriginType/FinanceOriginTypeStoreRequest');
        // Artisan::call('make:request Finance/OriginType/FinanceOriginTypeUpdateRequest');
        Artisan::call('make:resource Finance/OriginType/FinanceOriginTypeResource');
        Artisan::call('make:resource Finance/OriginType/FinanceOriginTypeCollection');

        Artisan::call('make:request Finance/Origin/FinanceOriginStoreRequest');
        Artisan::call('make:request Finance/Origin/FinanceOriginUpdateRequest');
        Artisan::call('make:resource Finance/Origin/FinanceOriginResource');
        Artisan::call('make:resource Finance/Origin/FinanceOriginCollection');

        Artisan::call('make:request Finance/Group/FinanceGroupStoreRequest');
        Artisan::call('make:request Finance/Group/FinanceGroupUpdateRequest');
        Artisan::call('make:resource Finance/Group/FinanceGroupResource');
        Artisan::call('make:resource Finance/Group/FinanceGroupCollection');

        Artisan::call('make:request Finance/Category/FinanceCategoryStoreRequest');
        Artisan::call('make:request Finance/Category/FinanceCategoryUpdateRequest');
        Artisan::call('make:resource Finance/Category/FinanceCategoryResource');
        Artisan::call('make:resource Finance/Category/FinanceCategoryCollection');

        // Artisan::call('make:request Finance/CategoryClosure/FinanceCategoryClosureStoreRequest');
        // Artisan::call('make:request Finance/CategoryClosure/FinanceCategoryClosureUpdateRequest');
        // Artisan::call('make:resource Finance/CategoryClosure/FinanceCategoryClosureResource');
        // Artisan::call('make:resource Finance/CategoryClosure/FinanceCategoryClosureCollection');

        Artisan::call('make:request Finance/Item/FinanceItemStoreRequest');
        Artisan::call('make:request Finance/Item/FinanceItemUpdateRequest');
        Artisan::call('make:resource Finance/Item/FinanceItemResource');
        Artisan::call('make:resource Finance/Item/FinanceItemCollection');

        Artisan::call('make:request Finance/Invoice/FinanceInvoiceStoreRequest');
        Artisan::call('make:request Finance/Invoice/FinanceInvoiceUpdateRequest');
        Artisan::call('make:resource Finance/Invoice/FinanceInvoiceResource');
        Artisan::call('make:resource Finance/Invoice/FinanceInvoiceCollection');

        Artisan::call('make:request Finance/InvoiceItem/FinanceInvoiceItemStoreRequest');
        Artisan::call('make:request Finance/InvoiceItem/FinanceInvoiceItemUpdateRequest');
        Artisan::call('make:resource Finance/InvoiceItem/FinanceInvoiceItemResource');
        Artisan::call('make:resource Finance/InvoiceItem/FinanceInvoiceItemCollection');

        Artisan::call('make:request Finance/List/FinanceListStoreRequest');
        Artisan::call('make:request Finance/List/FinanceListUpdateRequest');
        Artisan::call('make:resource Finance/List/FinanceListResource');
        Artisan::call('make:resource Finance/List/FinanceListCollection');

        Artisan::call('make:request Finance/ListItem/FinanceListItemStoreRequest');
        Artisan::call('make:request Finance/ListItem/FinanceListItemUpdateRequest');
        Artisan::call('make:resource Finance/ListItem/FinanceListItemResource');
        Artisan::call('make:resource Finance/ListItem/FinanceListItemCollection');

        Artisan::call('make:controller v1/FinanceTypeController');
        Artisan::call('make:resource Finance/Type/FinanceTypeResource');
        Artisan::call('make:resource Finance/Type/FinanceTypeCollection');

        Artisan::call('make:controller v1/FinanceStatusController');
        Artisan::call('make:resource Finance/Status/FinanceStatusResource');
        Artisan::call('make:resource Finance/Status/FinanceStatusCollection');

        Artisan::call('make:controller v1/FinanceOriginTypeController');
        Artisan::call('make:resource Finance/OriginType/FinanceOriginTypeResource');
        Artisan::call('make:resource Finance/OriginType/FinanceOriginTypeCollection');

        Artisan::call('make:controller v1/FinanceItemController');
        Artisan::call('make:request Finance/Item/FinanceItemStoreRequest');
        Artisan::call('make:request Finance/Item/FinanceItemUpdateRequest');
        Artisan::call('make:resource Finance/Item/FinanceItemResource');
        Artisan::call('make:resource Finance/Item/FinanceItemCollection');
        */



        return Command::SUCCESS;
    }
}
