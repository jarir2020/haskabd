<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiChatSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Toastr;

// AI Chat Settings admin controller -- updated 2026-04-15
class AiChatSettingController extends Controller
{
    public function index()
    {
        $setting   = AiChatSetting::first() ?? new AiChatSetting();
        $freeModels = $this->fetchFreeModels($setting->api_key ?? null);
        return view('backEnd.settings.ai_chat', compact('setting', 'freeModels'));
    }

    /**
     * Fetch available free models from OpenRouter, cached for 1 hour.
     */
    private function fetchFreeModels(?string $apiKey): array
    {
        $key = env('OPENROUTER_API_KEY');
        if ($apiKey) $key = $apiKey;

        return Cache::remember('openrouter_free_models', 3600, function() use ($key) {
            try {
                $res = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $key,
                ])->get('https://openrouter.ai/api/v1/models');

                $models = collect($res->json('data', []))
                    ->filter(fn($m) =>
                        isset($m['pricing']['prompt']) &&
                        (float)$m['pricing']['prompt'] === 0.0 &&
                        isset($m['id'])
                    )
                    ->sortByDesc(fn($m) => $m['context_length'] ?? 0)
                    ->map(fn($m) => [
                        'id'   => $m['id'],
                        'name' => ($m['name'] ?? $m['id']) . ' (free)',
                    ])
                    ->values()
                    ->toArray();

                $auto = [['id' => 'openrouter/free', 'name' => 'OpenRouter Free — best available free model (recommended)']];
                return array_merge($auto, $models ?: $this->fallbackModels());
            } catch (\Exception $e) {
                return $this->fallbackModels();
            }
        });
    }

    private function fallbackModels(): array
    {
        return [
            ['id' => 'openrouter/free',                       'name' => 'OpenRouter Free — best available free model (recommended)'],
            ['id' => 'mistralai/mistral-7b-instruct:free',    'name' => 'Mistral 7B Instruct (free)'],
            ['id' => 'meta-llama/llama-3.3-70b-instruct:free','name' => 'LLaMA 3.3 70B (free)'],
            ['id' => 'deepseek/deepseek-r1:free',             'name' => 'DeepSeek R1 (free)'],
            ['id' => 'google/gemma-3-1b-it:free',             'name' => 'Google Gemma 3 1B (free)'],
        ];
    }

    public function update(Request $request)
    {
        $request->validate([
            'model'      => 'required|string',
            'max_tokens' => 'required|integer|min:50|max:2000',
        ]);

        $setting = AiChatSetting::first();

        $data = [
            'provider'           => $request->provider ?? 'openrouter',
            'model'              => $request->model,
            'custom_instruction' => $request->custom_instruction,
            'max_tokens'         => $request->max_tokens,
            'status'             => $request->has('status') ? 1 : 0,
        ];

        // Only update api_key if a new one was provided
        if ($request->filled('api_key')) {
            $data['api_key'] = $request->api_key;
        }

        if ($setting) {
            $setting->update($data);
        } else {
            AiChatSetting::create($data);
        }

        Toastr::success('AI Chat settings saved.', 'Success');
        return redirect()->route('admin.ai_chat.index');
    }
}
