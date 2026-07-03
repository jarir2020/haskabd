<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Performance indexes for frequently queried columns -- added 2026-04-15
return new class extends Migration
{
    public function up()
    {
        // Orders
        Schema::table('orders', function (Blueprint $table) {
            if (!$this->hasIndex('orders', 'idx_orders_status')) {
                $table->index('order_status', 'idx_orders_status');
            }
            if (!$this->hasIndex('orders', 'idx_orders_customer_id')) {
                $table->index('customer_id', 'idx_orders_customer_id');
            }
            if (!$this->hasIndex('orders', 'idx_orders_invoice_id')) {
                $table->index('invoice_id', 'idx_orders_invoice_id');
            }
        });

        // Products
        if (!Schema::hasColumn('products', 'flashsale')) {
            Schema::table('products', function (Blueprint $table) {
                $table->tinyInteger('flashsale')->nullable()->default(0)->after('topsale');
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (!$this->hasIndex('products', 'idx_products_status')) {
                $table->index('status', 'idx_products_status');
            }
            if (!$this->hasIndex('products', 'idx_products_category_id')) {
                $table->index('category_id', 'idx_products_category_id');
            }
            if (!$this->hasIndex('products', 'idx_products_slug')) {
                $table->index('slug', 'idx_products_slug');
            }
            if (!$this->hasIndex('products', 'idx_products_topsale_status')) {
                $table->index(['topsale', 'status'], 'idx_products_topsale_status');
            }
            if (!$this->hasIndex('products', 'idx_products_flashsale_status')) {
                $table->index(['flashsale', 'status'], 'idx_products_flashsale_status');
            }
        });

        // Order details
        Schema::table('order_details', function (Blueprint $table) {
            if (!$this->hasIndex('order_details', 'idx_order_details_order_id')) {
                $table->index('order_id', 'idx_order_details_order_id');
            }
            if (!$this->hasIndex('order_details', 'idx_order_details_product_id')) {
                $table->index('product_id', 'idx_order_details_product_id');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_status');
            $table->dropIndex('idx_orders_customer_id');
            $table->dropIndex('idx_orders_invoice_id');
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_status');
            $table->dropIndex('idx_products_category_id');
            $table->dropIndex('idx_products_slug');
            $table->dropIndex('idx_products_topsale_status');
            $table->dropIndex('idx_products_flashsale_status');
        });
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropIndex('idx_order_details_order_id');
            $table->dropIndex('idx_order_details_product_id');
        });
    }

    private function hasIndex(string $table, string $index): bool
    {
        $indexes = \DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$index}'");
        return !empty($indexes);
    }
};
