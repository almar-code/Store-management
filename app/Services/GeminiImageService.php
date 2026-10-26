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
}
