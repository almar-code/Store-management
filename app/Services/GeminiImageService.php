<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class GeminiImageService
{
    private function apiKey(): string
    {
        $key = config('services.gemini.key');

        if (!$key) {
            throw new RuntimeException(
                'GEMINI_API_KEY is missing from .env'
            );
        }

        return $key;
    }

    private function imageData(string $path): array
    {
        if (!is_file($path)) {
            throw new RuntimeException(
                'Image file not found: ' . $path
            );
        }

        $mime = mime_content_type($path);

        if (!in_array($mime, ['image/jpeg', 'image/png'])) {
            throw new RuntimeException(
                'Only JPEG and PNG images are supported.'
            );
        }

        return [
            'mime_type' => $mime,
            'data' => base64_encode(file_get_contents($path)),
        ];
    }

    public function embedImage(string $path): array
    {
        $image = $this->imageData($path);
        $model = config(
            'services.gemini.embedding_model',
            'gemini-embedding-2'
        );

        $response = Http::timeout(90)
            ->withHeaders([
                'x-goog-api-key' => $this->apiKey(),
            ])
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:embedContent",
                [
                    'content' => [
                        'parts' => [[
                            'inline_data' => $image,
                        ]],
                    ],
                    'output_dimensionality' => 768,
                ]
            );

        if ($response->failed()) {
    $message = $response->json('error.message');

    throw new RuntimeException(
        'Gemini HTTP ' . $response->status() .
        ': ' . ($message ?: 'Unknown API error')
    );
}

        $vector = $response->json('embedding.values');

        if (!is_array($vector) || count($vector) !== 768) {
            throw new RuntimeException(
                'Gemini returned an unexpected embedding.'
            );
        }

        return $vector;
    }

    public function describeImage(string $path): array
    {
        $image = $this->imageData($path);
        $model = config(
            'services.gemini.vision_model',
            'gemini-2.5-flash'
        );

        $prompt = <<<'PROMPT'

حلّل قطعة الملابس الرئيسية التي يرتديها الشخص، وركّز على العباية نفسها
لا على الخلفية أو الكرسي أو الهاتف أو الحقيبة أو الملابس الأخرى.

أعد النتيجة بصيغة JSON صالحة فقط، باستخدام الحقول التالية:
{
  "item_type": "نوع قطعة الملابس",
  "main_color": "وصف اللون الأساسي باللغة الإنجليزية",
  "main_color_hex": "#RRGGBB",
  "pattern": "النقوش أو النمط",
  "embroidery": "التطريز",
  "sleeves": "وصف الأكمام",
  "cut": "قصة الملابس",
  "fabric_texture": "ملمس القماش",
  "visible_details": ["تفاصيل مرئية تخص قطعة الملابس"],
  "confidence_notes": "اذكر أي شك في تحديد اللون أو قطعة الملابس"
}

قواعد اللون:
- main_color_hex يجب أن يكون لون HEX من ست خانات يعبّر عن اللون
  المرئي للعباية نفسها، مثل #87CEEB للأزرق السماوي.
- لا تستخدم لون الخلفية أو لون الإضاءة باعتباره لون العباية.
- لا تختر #0000FF لمجرد أن اسم اللون هو blue.
- إذا تعذر تحديد لون العباية بثقة، اجعل main_color_hex بقيمة null.
- لا تخمّن تفاصيل غير واضحة في الصورة.
- أعد JSON فقط دون Markdown أو شرح خارجه.
.
PROMPT;

        $response = Http::timeout(90)
            ->withHeaders([
                'x-goog-api-key' => $this->apiKey(),
            ])
            ->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent",
                [
                    'contents' => [[
                        'parts' => [
                            ['text' => $prompt],
                            [
                                'inline_data' => $image,
                            ],
                        ],
                    ]],
                    'generationConfig' => [
                        'responseMimeType' => 'application/json',
                    ],
                ]
            );

        if ($response->failed()) {
    $message = $response->json('error.message');

    throw new RuntimeException(
        'Gemini HTTP ' . $response->status() .
        ': ' . ($message ?: 'Unknown API error')
    );
}

        $text = $response->json(
            'candidates.0.content.parts.0.text'
        );

        $description = is_string($text)
            ? json_decode($text, true)
            : null;

        if (!is_array($description)) {
            throw new RuntimeException(
                'Gemini did not return valid JSON.'
            );
        }

        return $description;
    }

    /**
     * تحليل صورة العباية لصفحة إضافة المنتج:
     * يعيد وصفاً تسويقياً جاهزاً + اسم اللون من القائمة + كود HEX.
     * عند ضغط الخوادم (503/429) يعيد المحاولة ثم ينتقل لنموذج احتياطي.
     *
     * @param  string[]  $colorNames  أسماء الألوان المسموحة (من ProductController::COLORS)
     */
    public function describeAbayaForProduct(string $path, array $colorNames = []): array
    {
        $image = $this->imageData($path);
        $colorsList = implode('، ', $colorNames);

        $prompt = <<<PROMPT
أنت خبير أزياء تكتب أوصاف منتجات لمتجر عبايات نسائية.
حلّل العباية الظاهرة في الصورة فقط، وتجاهل الخلفية والشخص والإكسسوارات.

أعد JSON صالحاً فقط بهذه الحقول:
{
  "is_abaya": true,
  "description": "وصف المنتج",
  "color_name": "اسم اللون",
  "color_hex": "#RRGGBB"
}

قواعد الوصف (description):
- بالعربية الفصحى، بأسلوب تسويقي راقٍ ومختصر، من جملتين إلى أربع جمل (40 إلى 80 كلمة تقريباً).
- اذكر فقط ما يظهر بوضوح: القصة (مفتوحة، مغلقة، كلوش، فراشة، بشت...)، الأكمام، التطريز أو النقوش أو الأحجار، مظهر القماش، واللون.
- لا تذكر المقاسات أو السعر أو العلامة التجارية أو الشخص أو كلمة "الصورة".
- لا تخترع تفاصيل غير ظاهرة.

قواعد اللون:
- color_name: اختر أقرب لون للعباية نفسها من هذه القائمة فقط وانسخه حرفياً كما هو: {$colorsList}
- color_hex: لون HEX من ست خانات يمثل اللون الفعلي المرئي لقماش العباية، وليس لون الخلفية أو الإضاءة.
- إذا تعذر تحديد اللون بثقة اجعل color_name و color_hex بقيمة null.

إذا لم تكن الصورة لعباية أو قطعة ملابس نسائية طويلة، اجعل is_abaya بقيمة false والباقي null.
أعد JSON فقط دون Markdown أو أي شرح خارجه.
PROMPT;

        $payload = [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    ['inline_data' => $image],
                ],
            ]],
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'temperature' => 0.4,
            ],
        ];

        $text = $this->generateWithFallback($payload);

        $result = json_decode($text, true);

        if (!is_array($result)) {
            throw new RuntimeException(
                'Gemini did not return valid JSON.'
            );
        }

        return $result;
    }

    /**
     * تجربة نماذج وصف المنتج بالترتيب حتى ينجح أحدها.
     * الضغط (429/5xx) أو انتهاء المهلة أو عدم توفر النموذج => الانتقال للنموذج التالي.
     * يعيد نص الرد الأول الناجح.
     */
    private function generateWithFallback(array $payload): string
    {
        $models = array_values(array_unique(array_filter(array_map(
            'trim',
            explode(',', (string) config(
                'services.gemini.product_models',
                'gemini-2.5-flash,gemini-3.5-flash-lite,gemini-2.5-flash-lite'
            ))
        ))));

        $retryable = [429, 500, 503, 504];
        $lastError = 'Unknown API error';

        // حد زمني كلي للطلب حتى لا ينتظر المستخدم طويلاً
        $deadline = microtime(true) + 50;

        foreach ($models as $model) {
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                $remaining = (int) floor($deadline - microtime(true));

                if ($remaining < 5) {
                    throw new RuntimeException('Gemini timed out: ' . $lastError);
                }

                try {
                    $response = Http::timeout(min(20, $remaining))
                        ->connectTimeout(10)
                        ->withHeaders([
                            'x-goog-api-key' => $this->apiKey(),
                        ])
                        ->post(
                            "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent",
                            $payload
                        );
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    // انتهاء المهلة أو انقطاع: لا نكرر على نفس النموذج البطيء
                    $lastError = 'Gemini timed out (' . $model . ')';
                    break;
                }

                if ($response->successful()) {
                    $text = $response->json('candidates.0.content.parts.0.text');

                    if (is_string($text) && trim($text) !== '') {
                        return $text;
                    }

                    $lastError = "Empty response from {$model}";
                    break;
                }

                $status = $response->status();
                $lastError = 'Gemini HTTP ' . $status . ' (' . $model . '): '
                    . ($response->json('error.message') ?: 'Unknown API error');

                // 404 = النموذج غير متاح لهذا المفتاح
                if ($status === 404) {
                    break;
                }

                if (!in_array($status, $retryable, true)) {
                    throw new RuntimeException($lastError);
                }

                if ($attempt === 1) {
                    usleep(1500 * 1000);
                }
            }
        }

        throw new RuntimeException($lastError);
    }
}