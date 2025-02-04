<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Label;
use Orchid\Support\Facades\Layout;
use Tabuna\Breadcrumbs\Breadcrumbs;

class AssignmentDetailsScreen extends Screen
{
    public $course;
    public $assignment;

    public function query(Course $course, $assignment): array
    {
        $this->course = $course;
        $this->assignment = $course->assignments[$assignment];

        // Set breadcrumbs
        Breadcrumbs::for('platform.course.details', function ($trail) use ($course) {
            $trail->push('Courses', route('platform.courses.index'));
            $trail->push($course->name, route('platform.course.details', $course->id));
        });
        Breadcrumbs::for('platform.assignment.details', function ($trail) use ($course, $assignment) {
            $trail->push('Assignments', route('platform.course.details', $course->id));
            $trail->push($this->assignment['title']);
        });

        return [
            'course' => $this->course,
            'assignment' => $this->assignment,
        ];
    }

    public function name(): string
    {
        return 'Assignment Details: ' . $this->assignment['title'];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Label::make('title')->title('Title')->value($this->assignment['title']),
                Label::make('description')->title('Description')->value($this->assignment['description']),
                Label::make('date')->title('Posted on')->value($this->assignment['date']),
            ])
        ];
    }
}
