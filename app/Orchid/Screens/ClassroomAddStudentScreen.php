<?php

namespace App\Orchid\Screens;

use App\Models\Classroom;
use App\Models\User;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;

class ClassroomAddStudentScreen extends Screen
{
    public function query(): array
    {
        return [
            'classrooms' => Classroom::where('teacher_id', auth()->id)->get(),
            'students' => User::whereHas('roles', function ($query) {
                $query->where('slug', 'student');
            })->get(),
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Select::make('classroom_id')
                    ->options($this->query()['classrooms']->pluck('name', 'id'))
                    ->title('Classroom')
                    ->required(),
                Select::make('student_id')
                    ->options($this->query()['students']->pluck('name', 'id'))
                    ->title('Student')
                    ->required(),
                Button::make('Add Student')
                    ->method('addStudent'),
            ]),
        ];
    }

    public function addStudent(Request $request)
    {
        $classroom = Classroom::find($request->input('classroom_id'));
        $classroom->students()->attach($request->input('student_id'));

        return redirect()->route('platform.main')->with('success', 'Student added to classroom successfully.');
    }
}
