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

    /**
     * Load the course and specific material using the index.
     */
    public function query(Course $course, int $index): array
    {
        $this->course = $course;
        $this->index = $index;

        return [
            'course'   => $course,
            'index'    => $index,
            'material' => $course->materials[$index] ?? null,
        ];
    }


    public function name(): string
    {
        return 'Edit Material';
    }

    /**
     * The screen's layout elements.
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): array
    {
        return [
            Layout::rows([
                Input::make('material.title')
                    ->title('Material Title')
                    ->placeholder('Enter material title'),
                Upload::make('material.attachment')
                    ->title('Upload New Material (Optional)')
                    ->acceptedFiles('.pdf,.doc,.docx,.ppt,.pptx')
                    ->help('If you do not upload a new file, the existing attachment will remain unchanged.'),
                Button::make('Update Material')
                    ->method('updateMaterial'),
            ]),
        ];
    }

    /**
     * Handles updating the material.
     */
    public function updateMaterial(Request $request)
    {
        $materials = $this->course->materials;
        $material = $materials[$this->index];

        $newTitle = $request->input('material.title');

        // Check for a new file upload.
        $uploadedFileId = $request->input('material.attachment');
        if ($uploadedFileId) {
            $attachment = Attachment::find($uploadedFileId);
            $newAttachmentUrl = $attachment ? $attachment->url : $material['attachment'];
        } else {
            // No new file provided; keep the existing attachment.
            $newAttachmentUrl = $material['attachment'] ?? null;
        }

        // Update the material using a local variable.
        $materials[$this->index] = [
            'title'      => $newTitle,
            'attachment' => $newAttachmentUrl,
            'date'       => now()->format('Y-m-d'),
        ];

        $this->course->materials = $materials;
        $this->course->save();

        return redirect()->route('platform.course.details', $this->course);
    }
}
