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
        $this->apiKey = env('HUGGING_FACE_API_KEY'); // Load the API key from the .env file
    }

    /**
     * Send a prompt to the Hugging Face Inference API and get a response.
     *
     * @param string $prompt The user's question or input.
     * @return string The AI's response.
     */
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
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return $data[0]['generated_text'] ?? 'Sorry, I could not generate a response.';
        } catch (GuzzleException $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
