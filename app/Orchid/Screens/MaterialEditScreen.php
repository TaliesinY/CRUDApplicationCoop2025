<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;

class MaterialEditScreen extends Screen
{
    public $course;
    public $index;

    public function query(Course $course, int $index): array
    {
        return [
            'course' => $course,
            'index' => $index,
            'material' => $course->materials[$index],
        ];
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('material.title')
                    ->title('Material Title')
                    ->placeholder('Enter material title'),
                Upload::make('material.attachment')
                    ->title('Upload Material')
                    ->acceptedFiles('.pdf,.doc,.docx,.ppt,.pptx'),
                Button::make('Update Material')
                    ->method('updateMaterial'),
            ]),
        ];
    }

    public function updateMaterial(Request $request)
    {
        $index = $this->index;
        $materialAttachment = $request->input('material.attachment');
        $attachment = Attachment::find($materialAttachment);

        $this->course->materials[$index] = [
            'title' => $request->input('material.title'),
            'attachment' => $attachment->url,
            'date' => now()->format('Y-m-d'),
        ];
        $this->course->save();

        return redirect()->route('platform.course.details', $this->course);
    }
}
