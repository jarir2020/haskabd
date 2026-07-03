<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'sold')) {
                $table->integer('sold')->default(0)->after('stock');
            }
            if (!Schema::hasColumn('products', 'subcategory_id')) {
                $table->integer('subcategory_id')->nullable()->after('category_id');
            }
            if (!Schema::hasColumn('products', 'childcategory_id')) {
                $table->integer('childcategory_id')->nullable()->after('subcategory_id');
            }
            if (!Schema::hasColumn('products', 'pro_video')) {
                $table->string('pro_video')->nullable()->after('description');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'front_view')) {
                $table->tinyInteger('front_view')->default(0)->after('status');
            }
        });

        Schema::table('general_settings', function (Blueprint $table) {
            $columns = [
                'facebook_verification' => fn (Blueprint $table) => $table->string('facebook_verification', 255)->nullable(),
                'google_verification' => fn (Blueprint $table) => $table->string('google_verification', 255)->nullable(),
                'og_baner' => fn (Blueprint $table) => $table->string('og_baner', 255)->nullable(),
                'meta_description' => fn (Blueprint $table) => $table->text('meta_description')->nullable(),
                'meta_keyword' => fn (Blueprint $table) => $table->text('meta_keyword')->nullable(),
                'hot_deal_end_date' => fn (Blueprint $table) => $table->date('hot_deal_end_date')->nullable(),
                'flash_sale_end_date' => fn (Blueprint $table) => $table->date('flash_sale_end_date')->nullable(),
                'header_code' => fn (Blueprint $table) => $table->text('header_code')->nullable(),
                'top_headline' => fn (Blueprint $table) => $table->text('top_headline')->nullable(),
                'checkout_note' => fn (Blueprint $table) => $table->text('checkout_note')->nullable(),
                'order_policy' => fn (Blueprint $table) => $table->text('order_policy')->nullable(),
                'show_all_products' => fn (Blueprint $table) => $table->tinyInteger('show_all_products')->default(0),
                'show_category_wise_products' => fn (Blueprint $table) => $table->tinyInteger('show_category_wise_products')->default(0),
            ];

            foreach ($columns as $column => $definition) {
                if (!Schema::hasColumn('general_settings', $column)) {
                    $definition($table);
                }
            }
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            foreach (['sold', 'subcategory_id', 'childcategory_id', 'pro_video'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'front_view')) {
                $table->dropColumn('front_view');
            }
        });

        Schema::table('general_settings', function (Blueprint $table) {
            foreach ([
                'facebook_verification',
                'google_verification',
                'og_baner',
                'meta_description',
                'meta_keyword',
                'hot_deal_end_date',
                'flash_sale_end_date',
                'header_code',
                'top_headline',
                'checkout_note',
                'order_policy',
                'show_all_products',
                'show_category_wise_products',
            ] as $column) {
                if (Schema::hasColumn('general_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
