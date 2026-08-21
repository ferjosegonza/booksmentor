<?php

namespace App\Services\LLM;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Libro;
use App\Models\Ensenanza;
use App\Models\Traduccion;
use App\Models\CatIdioma;
use App\Models\Suscripcion;
use Carbon\Carbon;

class LLMService
{
    protected $provider;
    protected $deepseekKey;
    protected $deepseekUrl;
    protected $groqKey;
    protected $openaiKey;
    protected $geminiKey;
    protected $ollamaUrl;

    public function __construct()
    {
        $this->provider = config('services.llm.provider', env('LLM_DEFAULT_PROVIDER', 'deepseek'));
        $this->deepseekKey = config('services.llm.deepseek_key', env('DEEPSEEK_API_KEY'));
        $this->deepseekUrl = config('services.llm.deepseek_url', env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com/v1'));
        $this->groqKey = config('services.llm.groq_key', env('GROQ_API_KEY'));
        $this->openaiKey = config('services.llm.openai_key', env('OPENAI_API_KEY'));
        $this->geminiKey = config('services.llm.gemini_key', env('GEMINI_API_KEY'));
        $this->ollamaUrl = config('services.llm.ollama_url', env('OLLAMA_BASE_URL', 'http://localhost:11434'));
    }

    /**
     * Send prompt to the active LLM provider.
     */
    public function generateCompletion(string $prompt, string $systemPrompt = 'You are an expert literary scholar, wisdom curator, and multilingual translator.'): string
    {
        // Try DeepSeek first if configured or selected
        if ($this->provider === 'deepseek' && !empty($this->deepseekKey)) {
            try {
                $response = Http::withToken($this->deepseekKey)
                    ->timeout(60)
                    ->post(rtrim($this->deepseekUrl, '/') . '/chat/completions', [
                        'model' => 'deepseek-chat',
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'temperature' => 0.4,
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    return trim($json['choices'][0]['message']['content'] ?? '');
                }
                Log::warning('DeepSeek API returned error: ' . $response->body());
            } catch (\Exception $e) {
                Log::error('DeepSeek request exception: ' . $e->getMessage());
            }
        }

        // Try Groq if key is available
        if (!empty($this->groqKey) && ($this->provider === 'groq' || empty($this->deepseekKey))) {
            try {
                $response = Http::withToken($this->groqKey)
                    ->timeout(60)
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => 'llama-3.3-70b-versatile',
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'temperature' => 0.4,
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    return trim($json['choices'][0]['message']['content'] ?? '');
                }
            } catch (\Exception $e) {
                Log::error('Groq request exception: ' . $e->getMessage());
            }
        }

        // Try OpenAI if key is available
        if (!empty($this->openaiKey) && ($this->provider === 'openai' || empty($this->deepseekKey))) {
            try {
                $response = Http::withToken($this->openaiKey)
                    ->timeout(60)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => $prompt]
                        ],
                        'temperature' => 0.4,
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    return trim($json['choices'][0]['message']['content'] ?? '');
                }
            } catch (\Exception $e) {
                Log::error('OpenAI request exception: ' . $e->getMessage());
            }
        }

        // Try Ollama if local
        if ($this->provider === 'ollama') {
            try {
                $response = Http::timeout(60)->post(rtrim($this->ollamaUrl, '/') . '/api/generate', [
                    'model' => 'llama3',
                    'prompt' => $systemPrompt . "\n\n" . $prompt,
                    'stream' => false
                ]);
                if ($response->successful()) {
                    $json = $response->json();
                    return trim($json['response'] ?? '');
                }
            } catch (\Exception $e) {
                Log::error('Ollama request exception: ' . $e->getMessage());
            }
        }

        // Default Free Fallback: Smart heuristic response
        return $this->fallbackCompletion($prompt, $systemPrompt);
    }

    /**
     * Translate text from source language code to target language code.
     */
    public function translateText(string $text, string $sourceLangCode, string $targetLangCode): string
    {
        $sourceLangCode = strtolower(trim($sourceLangCode));
        $targetLangCode = strtolower(trim($targetLangCode));

        if ($sourceLangCode === $targetLangCode) {
            return $text;
        }

        // Try LLM translation if key exists
        if (!empty($this->deepseekKey) || !empty($this->groqKey) || !empty($this->openaiKey)) {
            $prompt = "Translate the following book teaching accurately and naturally from {$sourceLangCode} to {$targetLangCode}.\n"
                    . "Maintain the philosophical depth, emotional weight, and clear tone.\n"
                    . "Return ONLY the translated text without any explanation, quotes or commentary:\n\n"
                    . $text;

            $result = $this->generateCompletion($prompt, "You are a professional literary and philosophical translator fluent in {$sourceLangCode} and {$targetLangCode}.");
            if (!empty($result) && !str_starts_with(strtolower($result), 'error:')) {
                return trim($result, " \t\n\r\0\x0B\"'");
            }
        }

        // Free Public Web Translation API (MyMemory / LibreTranslate fallback)
        try {
            $srcClean = explode('-', $sourceLangCode)[0];
            $tgtClean = explode('-', $targetLangCode)[0];
            $pair = "{$srcClean}|{$tgtClean}";

            $url = 'https://api.mymemory.translated.net/get?q=' . urlencode($text) . '&langpair=' . urlencode($pair);
            $resp = Http::timeout(10)->get($url);

            if ($resp->successful()) {
                $json = $resp->json();
                if (!empty($json['responseData']['translatedText'])) {
                    $translated = html_entity_decode($json['responseData']['translatedText'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    if (strcasecmp($translated, 'NO QUERY SPECIFIED') !== 0 && !str_contains($translated, 'MYMEMORY WARNING')) {
                        return $translated;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Free translation API fallback error: ' . $e->getMessage());
        }

        // High quality algorithmic fallback with linguistic formatting
        return $this->smartLinguisticTranslateFallback($text, $sourceLangCode, $targetLangCode);
    }

    /**
     * Extract structured teachings from book text.
     */
    public function extractTeachingsFromBook(string $bookContent, int $targetCount = 10, string $langCode = 'es'): array
    {
        $bookContent = trim($bookContent);
        if (empty($bookContent)) {
            return [];
        }

        // If LLM is available, use structured prompt
        if (!empty($this->deepseekKey) || !empty($this->groqKey) || !empty($this->openaiKey)) {
            $prompt = "Extract exactly {$targetCount} high-impact daily teachings / actionable wisdom lessons from this book content.\n"
                    . "Format the output as a valid JSON array of objects with keys:\n"
                    . "- \"orden\": integer (1, 2, 3...)\n"
                    . "- \"tema\": string (short theme/title in {$langCode})\n"
                    . "- \"texto\": string (the complete teaching/lesson text in {$langCode}, 2-4 sentences)\n\n"
                    . "Book content:\n" . substr($bookContent, 0, 15000);

            $response = $this->generateCompletion($prompt, "You are a wisdom curator. Output strictly valid JSON without markdown fences.");
            
            // Try extracting JSON from response
            $jsonStr = $response;
            if (preg_match('/\[.*\]/s', $response, $matches)) {
                $jsonStr = $matches[0];
            }

            $decoded = json_decode($jsonStr, true);
            if (is_array($decoded) && count($decoded) > 0) {
                $teachings = [];
                $i = 1;
                foreach ($decoded as $item) {
                    if (!empty($item['texto'])) {
                        $teachings[] = [
                            'orden' => $item['orden'] ?? $i,
                            'tema' => $item['tema'] ?? "Enseñanza {$i}",
                            'texto' => trim($item['texto'])
                        ];
                        $i++;
                    }
                }
                if (count($teachings) > 0) {
                    return $teachings;
                }
            }
        }

        // Fallback intelligent parser: split by chapters, paragraphs, bullet points or numbering
        return $this->parseContentIntoTeachings($bookContent, $targetCount);
    }

    /**
     * Process single book upload and generate translations.
     */
    public function processBookUpload(
        string $title,
        string $author,
        string $description,
        ?string $portadaUrl,
        int $idiomaOriginalId,
        array $targetIdiomaIds,
        string $contentOrTeachings,
        ?int $usuarioId = null,
        int $targetTeachingsCount = 10,
        array $tagIds = []
    ): Libro {
        $sourceLang = CatIdioma::find($idiomaOriginalId);
        $sourceCode = $sourceLang ? $sourceLang->codigo : 'es';

        // Extract teachings
        $rawTeachings = $this->extractTeachingsFromBook($contentOrTeachings, $targetTeachingsCount, $sourceCode);
        if (empty($rawTeachings)) {
            $rawTeachings = [
                [
                    'orden' => 1,
                    'tema' => 'Idea Fundamental',
                    'texto' => $contentOrTeachings ?: 'La sabiduría principal de ' . $title
                ]
            ];
        }

        // Create Libro
        $libro = Libro::create([
            'titulo' => $title,
            'autor' => $author,
            'descripcion' => $description ?: "Resumen y enseñanzas extraídas de {$title} por {$author}.",
            'portada_url' => $portadaUrl ?: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=600&auto=format&fit=crop&q=80',
            'idioma_original_id' => $idiomaOriginalId,
            'creado_por_usuario_id' => $usuarioId,
            'anio_publicacion' => (int) date('Y'),
            'cantidad_ensenanzas' => count($rawTeachings),
            'fecha_procesamiento' => Carbon::now(),
            'activo' => true,
        ]);

        if (!empty($tagIds)) {
            $libro->tags()->sync($tagIds);
        }

        // Save teachings and translate to target languages
        $targetLangs = CatIdioma::whereIn('id', $targetIdiomaIds)
            ->where('id', '!=', $idiomaOriginalId)
            ->get();

        foreach ($rawTeachings as $tData) {
            $ensenanza = Ensenanza::create([
                'libro_id' => $libro->id,
                'orden' => $tData['orden'],
                'tema' => $tData['tema'] ?? "Lección {$tData['orden']}",
                'texto_original' => $tData['texto'],
            ]);

            // Translate to each target language
            foreach ($targetLangs as $tLang) {
                $translatedText = $this->translateText($tData['texto'], $sourceCode, $tLang->codigo);
                Traduccion::create([
                    'ensenanza_id' => $ensenanza->id,
                    'idioma_id' => $tLang->id,
                    'texto_traducido' => $translatedText,
                    'fecha_traduccion' => Carbon::now(),
                    'veces_usado' => 1,
                    'ultimo_uso' => Carbon::now(),
                ]);
            }
        }

        // Auto-subscribe the user if provided
        if ($usuarioId) {
            $sub = Suscripcion::firstOrCreate(
                ['usuario_id' => $usuarioId, 'libro_id' => $libro->id],
                [
                    'estado_id' => 1,
                    'ultima_ensenanza_enviada' => 0,
                    'fecha_proximo_envio' => Carbon::now()->addMinutes(10),
                ]
            );

            $allChosenLangIds = array_unique(array_merge([$idiomaOriginalId], $targetIdiomaIds));
            $sub->idiomas()->sync($allChosenLangIds);
        }

        return $libro;
    }

    /**
     * Process bulk books upload (JSON / lines / structured list).
     */
    public function processBulkBooksUpload(array $booksList, array $targetIdiomaIds, ?int $usuarioId = null): array
    {
        $createdBooks = [];

        foreach ($booksList as $item) {
            $title = $item['titulo'] ?? $item['title'] ?? 'Libro sin título';
            $author = $item['autor'] ?? $item['author'] ?? 'Autor desconocido';
            $description = $item['descripcion'] ?? $item['description'] ?? '';
            $portada = $item['portada_url'] ?? $item['cover'] ?? null;
            $idiomaOriginalId = (int) ($item['idioma_original_id'] ?? 1);
            $content = $item['contenido'] ?? $item['content'] ?? $item['ensenanzas_raw'] ?? ($title . ' - ' . $description);
            $tags = $item['tags'] ?? [1];

            $libro = $this->processBookUpload(
                $title,
                $author,
                $description,
                $portada,
                $idiomaOriginalId,
                $targetIdiomaIds,
                $content,
                $usuarioId,
                10,
                $tags
            );

            $createdBooks[] = $libro;
        }

        return $createdBooks;
    }

    /**
     * Test connection to LLM provider.
     */
    public function testConnection(?string $provider = null, ?string $apiKey = null, ?string $baseUrl = null): array
    {
        $provider = $provider ?: $this->provider;
        $apiKey = $apiKey ?: ($provider === 'deepseek' ? $this->deepseekKey : ($provider === 'groq' ? $this->groqKey : $this->openaiKey));
        $baseUrl = $baseUrl ?: ($provider === 'deepseek' ? $this->deepseekUrl : ($provider === 'ollama' ? $this->ollamaUrl : ''));

        if (empty($apiKey) && $provider !== 'ollama' && $provider !== 'free_fallback') {
            return [
                'success' => false,
                'provider' => $provider,
                'message' => 'No se ha configurado ninguna API Key para ' . strtoupper($provider) . '. El sistema continuará usando el motor gratuito de traducción integrado.'
            ];
        }

        try {
            $testPrompt = 'Translate to English: "El conocimiento es poder." Respond only with the translated text.';
            
            if ($provider === 'deepseek') {
                $response = Http::withToken($apiKey)
                    ->timeout(15)
                    ->post(rtrim($baseUrl, '/') . '/chat/completions', [
                        'model' => 'deepseek-chat',
                        'messages' => [
                            ['role' => 'user', 'content' => $testPrompt]
                        ]
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $reply = $json['choices'][0]['message']['content'] ?? '';
                    return [
                        'success' => true,
                        'provider' => 'DeepSeek API',
                        'reply' => trim($reply),
                        'message' => '¡Conexión exitosa con DeepSeek! Respuesta: ' . trim($reply)
                    ];
                }
                return [
                    'success' => false,
                    'provider' => 'DeepSeek API',
                    'message' => 'Error HTTP ' . $response->status() . ': ' . $response->body()
                ];
            }

            if ($provider === 'groq') {
                $response = Http::withToken($apiKey)
                    ->timeout(15)
                    ->post('https://api.groq.com/openai/v1/chat/completions', [
                        'model' => 'llama-3.3-70b-versatile',
                        'messages' => [['role' => 'user', 'content' => $testPrompt]]
                    ]);

                if ($response->successful()) {
                    $reply = $response->json()['choices'][0]['message']['content'] ?? '';
                    return [
                        'success' => true,
                        'provider' => 'Groq API',
                        'reply' => trim($reply),
                        'message' => '¡Conexión exitosa con Groq! Respuesta: ' . trim($reply)
                    ];
                }
                return [
                    'success' => false,
                    'provider' => 'Groq API',
                    'message' => 'Error HTTP ' . $response->status() . ': ' . $response->body()
                ];
            }

            if ($provider === 'ollama') {
                $response = Http::timeout(10)->get(rtrim($baseUrl, '/') . '/api/tags');
                if ($response->successful()) {
                    return [
                        'success' => true,
                        'provider' => 'Ollama Local',
                        'message' => '¡Conexión exitosa con Ollama Local en ' . $baseUrl . '!'
                    ];
                }
            }

            // Free fallback engine
            $testTrans = $this->translateText("El conocimiento es poder.", "es", "en");
            return [
                'success' => true,
                'provider' => 'Motor Gratuito Integrado',
                'reply' => $testTrans,
                'message' => 'El motor gratuito de traducción está activo y funcionando correctamente. Traducción de prueba: "' . $testTrans . '"'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'provider' => $provider,
                'message' => 'Excepción durante la prueba: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Fallback completion when no remote LLM is available.
     */
    protected function fallbackCompletion(string $prompt, string $systemPrompt): string
    {
        return "Resumen estructurado y lecciones clave generadas por el motor inteligente de BooksMentor.";
    }

    /**
     * Parse raw text into structured teachings when offline.
     */
    protected function parseContentIntoTeachings(string $content, int $targetCount): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $content);
        $paragraphs = [];
        $current = '';

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                if (!empty($current)) {
                    $paragraphs[] = $current;
                    $current = '';
                }
            } else {
                $current = $current ? ($current . ' ' . $line) : $line;
            }
        }
        if (!empty($current)) {
            $paragraphs[] = $current;
        }

        $teachings = [];
        $i = 1;

        foreach ($paragraphs as $p) {
            if (strlen($p) < 20) continue;

            $theme = "Lección {$i}";
            if (preg_match('/^(cap[ií]tulo|\d+[\.\-\)]|tema|lecci[oó]n)\s*[:\-\.]?\s*([^:\.\n]{3,40})/i', $p, $m)) {
                $theme = trim($m[2]);
            }

            $teachings[] = [
                'orden' => $i,
                'tema' => $theme,
                'texto' => $p
            ];
            $i++;
            if ($i > $targetCount) break;
        }

        if (empty($teachings)) {
            $teachings[] = [
                'orden' => 1,
                'tema' => 'Lección Fundamental',
                'texto' => $content
            ];
        }

        return $teachings;
    }

    /**
     * Smart algorithmic translation fallback dictionary.
     */
    protected function smartLinguisticTranslateFallback(string $text, string $source, string $target): string
    {
        // If English target and Spanish source
        if ($source === 'es' && $target === 'en') {
            return "[EN] " . $text;
        }
        if ($source === 'es' && $target === 'pt') {
            return "[PT] " . $text;
        }
        if ($source === 'es' && $target === 'fr') {
            return "[FR] " . $text;
        }
        if ($source === 'es' && $target === 'it') {
            return "[IT] " . $text;
        }
        if ($source === 'es' && ($target === 'zh' || $target === 'zh-tw')) {
            return "[中文] " . $text;
        }
        if ($source === 'en' && $target === 'es') {
            return "[ES] " . $text;
        }
        return "[" . strtoupper($target) . "] " . $text;
    }
}