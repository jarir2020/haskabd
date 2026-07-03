<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AiChatSetting;
use App\Models\GeneralSetting;
use App\Models\Category;
use App\Models\Product;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// AI Chat frontend controller -- updated 2026-04-15 (RAG for large catalogs)
class AiChatController extends Controller
{
    // Max products to inject per message
    const MAX_PRODUCTS = 15;

    public function send(Request $request)
    {
        $request->validate(['message' => 'required|string|max:500']);

        $setting = AiChatSetting::first();

        if (!$setting || !$setting->status) {
            return response()->json(['reply' => 'Chat is currently unavailable.']);
        }

        $apiKey = $setting->api_key ?: env('OPENROUTER_API_KEY');
        $model  = $setting->model ?: 'openrouter/free';

        if (!$apiKey) {
            return response()->json(['reply' => 'Chat is not configured yet.']);
        }

        // Static site info — cached 10 min
        [$siteName, $categories, $phone, $email, $address, $totalProducts] = Cache::remember('ai_chat_static_context', 600, function () {
            $site       = optional(GeneralSetting::where('status', 1)->first());
            $categories = Category::where('status', 1)->pluck('name')->implode(', ');
            $contact    = Contact::where('status', 1)->first();
            $total      = Product::where('status', 1)->count();
            return [
                $site->name ?? 'our shop',
                $categories,
                $contact?->phone ?? '',
                $contact?->email ?? '',
                $contact?->address ?? '',
                $total,
            ];
        });

        // RAG: find products relevant to this specific message
        $relevantProducts = $this->searchRelevantProducts($request->message);

        $productSection = $relevantProducts->isNotEmpty()
            ? "Relevant products for this query:\n" . $relevantProducts->map(function ($p) {
                $price   = $p->new_price ?? $p->old_price ?? '?';
                $inStock = ($p->stock > 0) ? 'in stock' : 'out of stock';
                return "- {$p->name} | Category: {$p->category_name} | Price: {$price} BDT | {$inStock}";
            })->implode("\n")
            : "No specific products matched this query. Answer based on general shop knowledge.";

        $siteContext = "You are a helpful customer support assistant for \"{$siteName}\", an online organic products shop.
The shop has {$totalProducts} products total across these categories: {$categories}.
Contact: phone {$phone}, email {$email}, address: {$address}.

{$productSection}

Only answer questions related to this shop: products, prices, stock, categories, orders, shipping, returns, and contact info.
If asked anything unrelated to this shop, politely say you can only help with questions about {$siteName}.
Keep answers concise and friendly.";

        if ($setting->custom_instruction) {
            $siteContext .= "\n" . $setting->custom_instruction;
        }

        // Conversation history
        $history   = session('ai_chat_history', []);
        $history[] = ['role' => 'user', 'content' => $request->message];
        $trimmed   = array_slice($history, -10);

        $messages = array_merge(
            [['role' => 'system', 'content' => $siteContext]],
            $trimmed
        );

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer'  => url('/'),
                'X-Title'       => $siteName,
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model'      => $model,
                'messages'   => $messages,
                'max_tokens' => (int) $setting->max_tokens,
            ]);

            $reply = $response->json('choices.0.message.content')
                ?? 'Sorry, I could not get a response. Please try again.';

            $history[] = ['role' => 'assistant', 'content' => $reply];
            session(['ai_chat_history' => array_slice($history, -20)]);

            return response()->json(['reply' => $reply]);

        } catch (\Exception $e) {
            Log::error('AI Chat error: ' . $e->getMessage());
            return response()->json(['reply' => 'Sorry, something went wrong. Please try again.']);
        }
    }

    /**
     * Find products relevant to the user's message using keyword search.
     * Only hits the DB when needed — no context overflow regardless of catalog size.
     */
    private function searchRelevantProducts(string $message)
    {
        // Strip common stop words and extract meaningful keywords
        $stopWords = ['what', 'is', 'are', 'the', 'do', 'you', 'have', 'any', 'tell', 'me',
                      'about', 'a', 'an', 'i', 'want', 'need', 'looking', 'for', 'show',
                      'price', 'how', 'much', 'does', 'cost', 'available', 'stock', 'buy'];

        $words = preg_split('/\s+/', strtolower($message));
        $keywords = array_filter($words, fn($w) =>
            strlen($w) > 2 && !in_array($w, $stopWords)
        );

        if (empty($keywords)) {
            // No useful keywords — return top featured products as fallback
            return Product::where('status', 1)
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->select('products.name', 'products.new_price', 'products.old_price',
                         'products.stock', DB::raw('categories.name as category_name'))
                ->where('products.feature_product', 1)
                ->limit(self::MAX_PRODUCTS)
                ->get();
        }

        // Build LIKE conditions for each keyword against name and description
        $query = Product::where('products.status', 1)
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.name', 'products.new_price', 'products.old_price',
                     'products.stock', DB::raw('categories.name as category_name'));

        $query->where(function ($q) use ($keywords) {
            foreach ($keywords as $kw) {
                $kw = '%' . $kw . '%';
                $q->orWhere('products.name', 'LIKE', $kw)
                  ->orWhere('categories.name', 'LIKE', $kw);
            }
        });

        $results = $query->limit(self::MAX_PRODUCTS)->get();

        // If keyword search found nothing, broaden to all products in matching categories
        if ($results->isEmpty()) {
            $results = Product::where('products.status', 1)
                ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->select('products.name', 'products.new_price', 'products.old_price',
                         'products.stock', DB::raw('categories.name as category_name'))
                ->limit(self::MAX_PRODUCTS)
                ->get();
        }

        return $results;
    }

    public function clearHistory()
    {
        session()->forget('ai_chat_history');
        return response()->json(['ok' => true]);
    }
}
