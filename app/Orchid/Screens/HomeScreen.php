<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use App\Services\AiService;

class HomeScreen extends Screen
{
    public $question;
    public $response;

    /**
     * Fetch data to be displayed on the screen.
     */
    public function query(): array
    {
        return [
            'question' => $this->question,
            'response' => $this->response,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     */
    public function name(): string
    {
        return 'AI Assistant';
    }

    /**
     * The screen's layout elements.
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                TextArea::make('question')
                    ->title('Ask a Question')
                    ->placeholder('e.g., Help me with my homework...')
                    ->rows(5),
                Button::make('Ask')
                    ->method('askQuestion'),
            ]),
            Layout::rows([
                TextArea::make('response')
                    ->title('AI Response')
                    ->readonly()
                    ->rows(10)
                    ->value(session('response', '')),
            ]),
        ];
    }


    /**
     * Handle the form submission.
     */
    public function askQuestion(Request $request, AiService $aiService)
    {
        $question = $request->input('question');
        $response = $aiService->askQuestion($question);

        return redirect()->route('platform.main')->with([
            'question' => $question,
            'response' => $response,
        ]);
    }
}
