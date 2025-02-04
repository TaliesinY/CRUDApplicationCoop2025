<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Models\Course;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;
use Illuminate\Http\Request;

class CoursesScreen extends Screen
{
    public function query(): array
    {
        return ['courses' => Course::all(),];
    }


    public function name(): ?string
    {
        return 'Courses';
    }


    public function description(): ?string
    {
        return "Courses enrolled in";
    }


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
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::table('courses', [
                TD::make('name', 'Name')
                    ->render(
                        fn($course) =>
                        "<a href='" . route('platform.course.details', $course->id) . "'
                        class='text-primary font-bold'
                        style='text-decoration: none;'>"
                            . e($course->name) .
                            "</a>"
                    )->width('300px'),

                TD::make('actions', 'Actions')
                    ->render(function ($course) {
                        return Button::make('Edit Course')
                            ->method('editCourse')
                            ->parameters(['id' => $course->id])
                            ->icon('pencil')
                            ->class('btn btn-primary btn-sm')
                            ->render()
                            . ' ' .
                            Button::make('Delete Course')
                            ->method('deleteCourse')
                            ->parameters(['id' => $course->id])
                            ->icon('trash')
                            ->class('btn btn-danger btn-sm')
                            ->render();
                    }),
            ]),
        ];
    }


    /**
     * Edits current course.
     */
    public function editCourse(Request $request)
    {
        $courseId = $request->input('id');
        return redirect()->route('platform.course.edit', $courseId);
    }

    /**
     * Deletes a chosen course.
     */
    public function deleteCourse(Request $request)
    {
        $courseId = $request->input('id');
        Course::find($courseId)->delete();

        return redirect()->route('platform.courses');
    }
}
