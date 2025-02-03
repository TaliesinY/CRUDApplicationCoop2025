<?php

namespace App\Orchid\Screens;

use App\Models\Assignment;
use App\Models\Classroom;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;

class AssignmentCreateScreen extends Screen
{
    public function query(): array
    {
        return [
            'classrooms' => Classroom::where('teacher_id', auth()->id)->get(),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('assignment.title')
                    ->title('Title')
                    ->required(),
                TextArea::make('assignment.description')
                    ->title('Description'),
                Input::make('assignment.classroom_id')
                    ->type('select')
                    ->options($this->query()['classrooms']->pluck('name', 'id'))
                    ->title('Classroom')
                    ->required(),
                Button::make('Create Assignment')
                    ->method('createAssignment'),
            ]),
        ];
    }

    public function createAssignment(Request $request)
    {
        Assignment::create($request->input('assignment'));

        return redirect()->route('platform.main')->with('success', 'Assignment created successfully.');
    }
}
