<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Label;
use Orchid\Support\Facades\Layout;

class AssignmentDetailsScreen extends Screen
{
    public $course;
    public $assignment;

    public function query(Course $course, int $index): array
    {
        $this->course = $course;
        $this->assignment = $course->assignments[$index] ?? null;

        return [
            'course' => $course,
            'assignment' => $this->assignment,
        ];
    }

    public function name(): string
    {
        return 'Assignment Details: ' . ($this->assignment['title'] ?? 'Not Found');
    }

    public function description(): string
    {
        return 'Details of the assignment';
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Label::make('assignment.title')
                    ->title('Title'),
                Label::make('assignment.description')
                    ->title('Description'),
                Label::make('assignment.date')
                    ->title('Posted On'),
            ]),
        ];
    }
}
