<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AiService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('HUGGING_FACE_API_KEY');
    }

    public function askQuestion(string $prompt): string
    {
        try {
            $response = $this->client->post('https://api-inference.huggingface.co/models/gpt2', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'inputs' => $prompt,
                    'parameters' => [
                        'max_tokens' => 100,   // Limit the response length
                        'temperature' => 0.3,   // Control randomness (lower means more deterministic)
                    ]
                ],
            ]);


            $data = json_decode($response->getBody(), true);
            return $data[0]['generated_text'] ?? 'Sorry, I could not generate a response.';
        } catch (GuzzleException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
