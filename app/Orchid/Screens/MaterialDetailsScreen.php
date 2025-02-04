<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use App\Models\Material;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Label;
use Orchid\Screen\Layout;
use Illuminate\Http\Request;

class MaterialDetailsScreen extends Screen
{
    public $material;
    public $course;

    public function query(Course $course, Material $material): array
    {
        return [
            'course' => $course,
            'material' => $material,
        ];
    }

    public function name(): string
    {
        return 'Material: ' . $this->material->title;
    }

    public function description(): string
    {
        return 'Details of material: ' . $this->material->title;
    }

    public function layout(): array
    {
        return [
            Layout::rows([
                Label::make('title')
                    ->title('Material Title')
                    ->value($this->material->title),
                Label::make('attachment')
                    ->title('Material Attachment')
                    ->value($this->material->attachment ? $this->material->attachment : 'No file attached'),
                Label::make('uploaded_on')
                    ->title('Uploaded On')
                    ->value($this->material->created_at->format('Y-m-d')),
            ])
        ];
    }
}
