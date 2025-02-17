<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use App\Models\Assignment;
use App\Models\Material;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Label;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\TD;
use Illuminate\Http\Request;
use Orchid\Attachment\Models\Attachment;

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
            Layout::tabs([
                'Stream' => Layout::rows([
                    TextArea::make('announcement')
                        ->title('Post an Announcement')
                        ->placeholder('Enter your announcement...')
                        ->rows(3),
                    Button::make('Post Announcement')
                        ->method('postAnnouncement'),
                    ...$this->getStreamContent(),
                ]),

                'Assignments' => Layout::rows([
                    Input::make('assignment.title')
                        ->title('Assignment Title')
                        ->placeholder('Enter assignment title'),
                    TextArea::make('assignment.description')
                        ->title('Assignment Description')
                        ->placeholder('Enter assignment description'),
                    Button::make('Post Assignment')
                        ->method('postAssignment'),
                    ...$this->getAssignmentsContent(),
                ]),

                'Materials' => Layout::rows([
                    Input::make('material.title')
                        ->title('Material Title')
                        ->placeholder('Enter material title'),
                    Upload::make('material.attachment')
                        ->title('Upload Material')
                        ->acceptedFiles('.pdf,.doc,.docx,.ppt,.pptx'),
                    Button::make('Upload Material')
                        ->method('uploadMaterial'),
                    ...$this->getMaterialsContent(),
                ]),

                'Students' => Layout::rows([
                    Input::make('student.name')
                        ->title('Student Name')
                        ->placeholder('Enter student name'),
                    Button::make('Add Student')
                        ->method('addStudent'),
                        ...$this->getStudentsContent(),
                ]),

            ]),
        ];
    }




    public function postAnnouncement(Request $request, Course $course)
    {
        $announcement = $request->input('announcement');

        // Append the announcement to the existing announcements
        $announcements = $course->announcement ?? [];
        $announcements[] = [
            'text' => $announcement,
            'date' => now()->format('Y-m-d'),
        ];

        // Save the new list of announcements
        $course->announcement = $announcements;
        $course->save();

        return redirect()->route('platform.course.details', $course);
    }



protected function getAssignmentsContent(): array
{
    $fields = [];
    foreach ($this->course->assignments ?? [] as $index => $assignment) {
        $fields[] = Label::make('')
            ->title('Assignment: ' . $assignment['title'])
            ->value('Posted on: ' . ($assignment['date'] ?? now()->format('Y-m-d')) . '<br>' . $assignment['description']);
        $fields[] = Button::make('Edit Assignment')
            ->method('editAssignment')
            ->parameters(['index' => $index]);
        $fields[] = Button::make('Delete Assignment')
            ->method('deleteAssignment')
            ->parameters(['index' => $index]);
    }
    return $fields;
}



public function editAssignment(Request $request)
{
    $index = $request->input('index');
    $assignment = $this->course->assignments[$index];

    return redirect()->route('platform.assignment.edit', [
        'course' => $this->course->id,
        'index' => $index,
    ]);
}

public function deleteAssignment(Request $request)
{
    $index = $request->input('index');
    $assignments = $this->course->assignments;
    array_splice($assignments, $index, 1);
    $this->course->assignments = $assignments;
    $this->course->save();

    return redirect()->route('platform.course.details', $this->course);
}

    /**
     * Generate the stream content (assignments and materials sorted by date).
     */
    protected function getStreamContent(): array
    {
        $stream = [];

        // Add announcements to the stream (if multiple announcements are stored)
        if ($this->course->announcement) {
            foreach ($this->course->announcement as $announcement) {
                $stream[] = Label::make('')
                    ->title('Announcement: ' . $announcement['text'])
                    ->value('Posted on: ' . $announcement['date']);
            }
        }

        // Add assignments to the stream with links
        foreach ($this->course->assignments ?? [] as $index => $assignment) {
            $stream[] = Link::make('Assignment: ' . $assignment['title'])
                ->route('platform.assignment.details', [
                    'course' => $this->course->id,
                    'assignment' => $index,  // Pass index or ID of the assignment
                ])
                ->title('Click to view details')
                ->icon('fa fa-file');
        }

        // Add materials to the stream with links
        foreach ($this->course->materials ?? [] as $index => $material) {
            if (isset($material['attachment']) && !empty($material['attachment'])) {
                $stream[] = Link::make('Material: ' . $material['title'])
                    ->route('platform.material.details', [
                        'course' => $this->course->id,
                        'material' => $index,  // Pass index or ID of the material
                    ])
                    ->title('Click to view details')
                    ->icon('fa fa-file');
            }
        }

        // Sort the stream by date (newest first)
        usort($stream, function ($a, $b) {
            return strtotime($b->get('value')) - strtotime($a->get('value'));
        });

        return $stream;
    }

    /**
     * Generate the students content.
     */
    protected function getStudentsContent(): array
    {
        $studentsContent = [];

        foreach ($this->course->students ?? [] as $student) {
            $studentsContent[] = Label::make('')
                ->title('Student: ' . $student['name']);
        }

        return $studentsContent;
    }

    public function addStudent(Request $request)
{
    $studentName = $request->input('student.name');

    if ($studentName) {
        $students = $this->course->students ?? [];
        $students[] = ['name' => $studentName];
        $this->course->students = $students;
        $this->course->save();
    }

    return redirect()->route('platform.course.details', $this->course);
}


    /**
     * Handle the form submission for posting an assignment.
     */
    public function postAssignment(Request $request)
    {
        $assignmentTitle = $request->input('assignment.title');
        $assignmentDescription = $request->input('assignment.description');

        // Add the new assignment to the course
        $assignments = $this->course->assignments ?? [];
        $assignments[] = [
            'title' => $assignmentTitle,
            'description' => $assignmentDescription,
            'date' => now()->format('Y-m-d'),
        ];
        $this->course->assignments = $assignments;
        $this->course->save();

        return redirect()->route('platform.course.details', $this->course);
    }

    /**
     * Handle the form submission for uploading a material.
     */
    public function uploadMaterial(Request $request)
    {
        $materialTitle = $request->input('material.title');
        $materialAttachment = $request->file('material.attachment');  // Handle file input

        $materials = $this->course->materials ?? [];

        // Handle file upload if available
        if ($materialAttachment) {
            $attachment = Attachment::create([
                'file' => $materialAttachment,
            ]);

            $materials[] = [
                'title' => $materialTitle,
                'attachment' => $attachment->url,  // Store the URL of the uploaded file
                'date' => now()->format('Y-m-d'),
            ];
        }

        $this->course->materials = $materials;
        $this->course->save();

        return redirect()->route('platform.course.details', $this->course);
    }



    protected function getMaterialsContent(): array
    {
        $fields = [];
        foreach ($this->course->materials ?? [] as $index => $material) {
            // Add a label for the material title and date
            $fields[] = Label::make('')
                ->title('Material: ' . $material['title'])
                ->value('Uploaded on: ' . ($material['date'] ?? now()->format('Y-m-d')));

            // Add the download link if the material has an attachment
            if (isset($material['attachment']) && !empty($material['attachment'])) {
                $fields[] = Link::make('Download Material')
                    ->href($material['attachment'])
                    ->title('Click to download')
                    ->icon('fa fa-download');
            }

            // Add a button to delete the material
            $fields[] = Button::make('Delete Material')
                ->method('deleteMaterial')
                ->parameters(['index' => $index]);
        }

        return $fields;
    }


}
