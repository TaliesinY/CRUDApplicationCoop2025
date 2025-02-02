<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Label;
use Orchid\Support\Facades\Layout;

class CourseDetailsScreen extends Screen
{
    public $course;

    public function query(Course $course): array
    {
        return [
            'course' => $course,
        ];
    }

    public function name(): string
    {
        return 'Course Details: ' . $this->course->name;
    }

    public function description(): string
    {
        return 'Details of ' . $this->course->name;
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Label::make('course.name')->title('Course Name'),
                Label::make('course.students')->title('Students')->render(fn ($course) => implode(', ', $course->students ?? [])),
                Label::make('course.assignments')->title('Assignments')->render(fn ($course) => implode(', ', $course->assignments ?? [])),
                Label::make('course.materials')->title('Materials')->render(fn ($course) => implode(', ', $course->materials ?? [])),
            ]),
        ];
    }
}
