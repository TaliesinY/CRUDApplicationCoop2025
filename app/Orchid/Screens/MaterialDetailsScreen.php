<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Label;
use Orchid\Support\Facades\Layout;
use Tabuna\Breadcrumbs\Breadcrumbs;

class MaterialDetailsScreen extends Screen
{
    public $course;
    public $material;

    public function query(Course $course, $material): array
    {
        $this->course = $course;
        $this->material = $course->materials[$material];

        // Set breadcrumbs
        Breadcrumbs::for('platform.course.details', function ($trail) use ($course) {
            $trail->push('Courses', route('platform.courses.index'));
            $trail->push($course->name, route('platform.course.details', $course->id));
        });
        Breadcrumbs::for('platform.material.details', function ($trail) use ($course, $material) {
            $trail->push('Materials', route('platform.course.details', $course->id));
            $trail->push($this->material['title']);
        });

        return [
            'course' => $this->course,
            'material' => $this->material,
        ];
    }

    public function name(): string
    {
        return 'Material Details: ' . $this->material['title'];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Label::make('title')->title('Title')->value($this->material['title']),
                Label::make('date')->title('Uploaded on')->value($this->material['date']),
                Label::make('attachment')->title('Attachment')->value($this->material['attachment'] ? '<a href="' . $this->material['attachment'] . '" target="_blank">Download</a>' : 'No attachment'),
            ])
        ];
    }
}
