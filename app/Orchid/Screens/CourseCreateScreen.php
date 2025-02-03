<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;

class CourseCreateScreen extends Screen
{
    /**
     * The model instance.
     *
     * @var Course
     */
    public $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): array
    {
        return [
            'course' => $this->course
        ];
    }

    /**
     * Handle the form submission.
     */
    public function save(Request $request)
    {
        $validatedData = $request->validate([
            'course.name' => 'required|string|max:255',
        ]);

        // Fill the course model with validated data
        $this->course->fill([
            'name' => $validatedData['course']['name'],

        ]);

        $this->course->save();
        return redirect()->route('platform.courses');
    }

    /**
     * The form layout.
     *
     * @return array
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('course.name')
                    ->title('Course Name')
                    ->placeholder('Enter course name')
                    ->required(),
                Button::make('Save')
                    ->method('save')
            ])
        ];
    }
}
