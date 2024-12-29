<?php

declare (strict_types = 1);

namespace Hts\Mall\Console;

use DB;
use Hts\Mall\Models\Discount;
use Hts\Mall\Models\Product;
use Hts\Mall\Models\Variant;
use Illuminate\Console\Command;

class PurgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = '
        mall:purge
        {--force : Don\'t ask before deleting the order and customer related records}
    ';

    /**
     * The console command name.
     * @var string
     */
    protected $name = 'mall:purge';

    /**
     * The console command description.
     * @var string|null
     */
    protected $description = 'Purge all customer and order related data';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $question = 'This command removes all customer and order related data. Do you really want to continue?';

        if (! $this->option('force') && ! $this->output->confirm($question, false)) {
            return 0;
        }

        // Delete Orders
        $this->warn(' Deleting orders...');
        DB::table('hts_mall_order_products')->truncate();
        DB::table('hts_mall_orders')->truncate();
        $this->output->newLine();

        // Delete Carts
        $this->warn(' Deleting carts...');
        DB::table('hts_mall_cart_custom_field_value')->truncate();
        DB::table('hts_mall_cart_discount')->truncate();
        DB::table('hts_mall_cart_products')->truncate();
        DB::table('hts_mall_carts')->truncate();
        $this->output->newLine();

        // Delete Customers
        $this->warn(' Deleting customers...');
        DB::table('hts_mall_addresses')->truncate();
        DB::table('hts_mall_customers')->truncate();
        DB::table('users')->truncate();
        $this->output->newLine();

        // Delete Payment Logs
        $this->warn(' Deleting payment logs...');
        DB::table('hts_mall_payments_log')->truncate();
        $this->output->newLine();

        // Clean Up
        $this->warn(' Cleaning up...');
        Product::get()->each->update(['sales_count' => 0]);
        Variant::get()->each->update(['sales_count' => 0]);
        Discount::get()->each->update(['number_of_usages' => 0]);
        $this->callSilent('cache:clear', []);
        $this->output->newLine();

        // Finish
        $this->alert('Data has been purged!');
    }
}
