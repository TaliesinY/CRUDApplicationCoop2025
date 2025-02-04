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
        return [
            'course' => $course,
            'index' => $index,
            'assignment' => $course->assignments[$index],
        ];
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
        $index = $this->index;
        $this->course->assignments[$index] = [
            'title' => $request->input('assignment.title'),
            'description' => $request->input('assignment.description'),
            'date' => now()->format('Y-m-d'),
        ];
        $this->course->save();

        return redirect()->route('platform.course.details', $this->course);
    }
}
