<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;

class CourseCreateScreen extends Screen
{
    public $name = 'Create Course';
    public $description = 'Create a new course';

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
    public function save()
    {

        $this->course->fill([
            'name' => request('name'),
            'students' => json_decode(request('students'), true),
            'assignments' => json_decode(request('assignments'), true),
            'materials' => json_decode(request('materials'), true),
        ]);
        $this->course->save();
        return redirect()->route('platform.course.list'); // Redirect after saving
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
                Input::make('name')
                    ->title('Course Name')
                    ->placeholder('Enter course name')
                    ->required(),

                TextArea::make('students')
                    ->title('Students (JSON format)')
                    ->placeholder('Enter students list in JSON format e.g. ["Student 1", "Student 2"]'),

                TextArea::make('assignments')
                    ->title('Assignments (JSON format)')
                    ->placeholder('Enter assignments in JSON format e.g. ["Assignment 1", "Assignment 2"]'),

                TextArea::make('materials')
                    ->title('Materials (JSON format)')
                    ->placeholder('Enter materials in JSON format e.g. ["Material 1", "Material 2"]'),
            ]),

            Layout::actions([
                Button::make('Save')
                    ->method('save')
            ]),
        ];
    }
}
