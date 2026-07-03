<?php
/**
 * Fix corrupted top_headline in general_settings table
 * The field had the entire SQL row data embedded in it
 * Correct value extracted from SQL dump: creativedesignbd_ecommerce3.sql
 * Created: 2026-04-15
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$correctHeadline = "Organic অনলাইন শপে আপনাকে স্বাগতম ||\r\nঅনলাইনে আস্থা ও বিশ্বস্ততার সাথে  সারা বাংলাদেশে হোম ডেলিভারী দিয়ে থাকি\r\nঅর্ডার করতে অগ্রিম টাকা দিতে হবে না\r\nএ্যাডভান্স বিকাশ পেমেন্টে ৫% ডিসকাউন্ট\r\n৩-৫ দিনে সারাদেশে হোম ডেলিভারী দেওয়া হয়\r\nক্যাশঅন ডেলিভারীর সুবিধা রয়েছে, তাই অর্ডার করুন নিশ্চিন্তে\r\nধন্যবাদ";

$updated = DB::table('general_settings')
    ->where('id', 2)
    ->update(['top_headline' => $correctHeadline]);

if ($updated) {
    echo "SUCCESS: top_headline updated for id=2\n";
    $row = DB::table('general_settings')->where('id', 2)->value('top_headline');
    echo "Stored value:\n" . $row . "\n";
} else {
    echo "No rows updated. Check if id=2 exists.\n";
    // Try by status=1
    $id = DB::table('general_settings')->where('status', 1)->value('id');
    echo "Active row id: " . $id . "\n";
}
