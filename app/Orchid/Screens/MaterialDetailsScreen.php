<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Label;
use Orchid\Support\Facades\Layout;

class MaterialDetailsScreen extends Screen
{
    public $course;
    public $material;

    public function query(Course $course, int $index): array
    {
        $this->course = $course;
        $this->material = $course->materials[$index] ?? null;

        return [
            'course' => $course,
            'material' => $this->material,
        ];
    }

    public function name(): string
    {
        return 'Material Details: ' . ($this->material['title'] ?? 'Not Found');
    }

    public function description(): string
    {
        return 'Details of the material';
    }


    /**
     * The screen's layout elements.
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                Label::make('material.title')
                    ->title('Title'),
                Label::make('material.date')
                    ->title('Uploaded On'),
                Label::make('material.attachment')
                    ->title('Download Link')
                    ->value(function () {
                        if ($this->material['attachment']) {
                            return '<a href="' . $this->material['attachment'] . '" target="_blank">Download Material</a>';
                        }
                        return 'No attachment available';
                    }),
            ]),
        ];
    }
}
