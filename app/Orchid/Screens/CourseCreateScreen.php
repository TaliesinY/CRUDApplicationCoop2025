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
        // Validate only the 'name' field as required
        $validatedData = $request->validate([
            'course.name' => 'required|string|max:255',  // Ensure 'name' is not null and is a string
        ]);

        // Fill the course model with validated data
        $this->course->fill([
            'name' => $validatedData['course']['name'],
            // Students, assignments, and materials will remain empty (null)
        ]);

        // Save the course
        $this->course->save();

        // Redirect to the 'platform.courses' route after saving
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
                    ->required(),  // Only the name is required
                Button::make('Save')
                    ->method('save')
            ])
        ];
    }
}
