<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;

class CourseEditScreen extends Screen
{
    public $course;

    public function query(Course $course): array
    {
        return [
            'course' => $course,
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('course.name')
                    ->title('Course Name')
                    ->placeholder('Enter course name'),
                Button::make('Update Course')
                    ->method('updateCourse'),
            ]),
        ];
    }

    /**
     * Edits current course.
     */
    public function updateCourse(Request $request)
    {
        $this->course->name = $request->input('course.name');
        $this->course->save();

        return redirect()->route('platform.courses');
    }
}
