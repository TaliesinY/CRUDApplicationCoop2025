<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Menu;
use App\Models\Course;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;

class CoursesScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): array
    {
        return ['courses' => Course::all(),];
    }

    /**
     * The name of the screen displayed in the header.
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Courses';
    }

    /**
     * Displays a description on the user's screen
     * directly under the heading.
     */
    public function description(): ?string
    {
        return "Courses enrolled in";
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Link::make('Create Course')
                ->icon('bs.plus-circle')
                ->route('platform.course.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::table('courses', [
                TD::make('name', 'Course Name')
                    ->render(fn ($course) => Link::make($course->name)
                        ->route('platform.course.details', $course->id)),
            ]),
        ];
    }
}
