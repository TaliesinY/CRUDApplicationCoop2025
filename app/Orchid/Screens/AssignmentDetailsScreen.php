<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use App\Models\Assignment;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layout;
use Illuminate\Http\Request;

class AssignmentDetailsScreen extends Screen
{
    public $assignment;
    public $course;

    public function query(Course $course, Assignment $assignment): array
    {
        return [
            'course' => $course,
            'assignment' => $assignment,
        ];
    }

    public function name(): string
    {
        return 'Assignment: ' . $this->assignment->title;
    }

    public function description(): string
    {
        return 'Details of assignment: ' . $this->assignment->title;
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Label::make('title')
                    ->title('Assignment Title')
                    ->value($this->assignment->title),
                Label::make('description')
                    ->title('Assignment Description')
                    ->value($this->assignment->description),
                Label::make('posted_on')
                    ->title('Posted On')
                    ->value($this->assignment->created_at->format('Y-m-d')),
            ])
        ];
    }
}
