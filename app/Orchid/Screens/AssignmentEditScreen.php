<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;

class AssignmentEditScreen extends Screen
{
    public $course;
    public $index;

    public function query(Course $course, int $index): array
    {
        // Constructors
        $this->course = $course;
        $this->index = $index;

        return [
            'course'     => $course,
            'index'      => $index,
            'assignment' => $course->assignments[$index] ?? null,
        ];
    }

    public function name(): string
    {
        return 'Edit Assignment';
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('assignment.title')
                    ->title('Assignment Title')
                    ->placeholder('Enter assignment title'),
                TextArea::make('assignment.description')
                    ->title('Assignment Description')
                    ->placeholder('Enter assignment description'),
                Button::make('Update Assignment')
                    ->method('updateAssignment'),
            ]),
        ];
    }

    public function updateAssignment(Request $request)
    {
        $assignments = $this->course->assignments;

        // Update the assignment at the given index:
        $assignments[$this->index] = [
            'title'       => $request->input('assignment.title'),
            'description' => $request->input('assignment.description'),
            'date'        => now()->format('Y-m-d'),
        ];

        // Saving and redirecting to stream
        $this->course->assignments = $assignments;
        $this->course->save();
        return redirect()->route('platform.course.details', $this->course);
    }
}
