<?php

namespace App\Orchid\Screens;

use App\Models\Course;
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
        // Store the course in a class property for use in other methods.
        $this->course = $course;
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
        $newAnnouncement = [
            'text' => $request->input('announcement'),
            'date' => now()->toDateTimeString(),
        ];

        // Retrieve existing announcements and add the new one
        $announcements = $course->announcements ?? [];
        $announcements[] = $newAnnouncement;

        // Save the updated list of announcements
        $course->announcements = $announcements;
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
        return redirect()->route('platform.assignment.edit', [
            'course' => $this->course->id,
            'index' => $index,
        ]);
    }

    public function deleteAssignment(Request $request, Course $course)
    {
        $index = $request->input('index');
        $assignments = $course->assignments;
        array_splice($assignments, $index, 1);
        $course->assignments = $assignments;
        $course->save();

        return redirect()->route('platform.course.details', $course);
    }

    /**
     * Generate the stream content (announcements, assignments, and materials).
     */
    protected function getStreamContent(): array
    {
        $stream = [];

        // Add all announcements
        foreach ($this->course->announcements ?? [] as $announcement) {
            $stream[] = Label::make('')
                ->title('Announcement: ' . $announcement['text'])
                ->value('Posted on: ' . $announcement['date']);
        }

        // Add assignments and materials to stream
        foreach ($this->course->assignments ?? [] as $index => $assignment) {
            $stream[] = Link::make('Assignment: ' . $assignment['title'])
                ->route('platform.assignment.details', [
                    'course' => $this->course->id,
                    'index' => $index,
                ])
                ->title('Click to view details')
                ->icon('fa fa-file');
        }

        foreach ($this->course->materials ?? [] as $index => $material) {
            $stream[] = Link::make('Material: ' . $material['title'])
                ->route('platform.material.details', [
                    'course' => $this->course->id,
                    'index' => $index,
                ])
                ->title('Click to view details')
                ->icon('fa fa-file');
        }

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

    public function postAssignment(Request $request)
    {
        $assignmentTitle = $request->input('assignment.title');
        $assignmentDescription = $request->input('assignment.description');

        $assignments = $this->course->assignments ?? [];
        $assignments[] = [
            'title'       => $assignmentTitle,
            'description' => $assignmentDescription,
            'date'        => now()->format('Y-m-d'),
        ];
        $this->course->assignments = $assignments;
        $this->course->save();

        return redirect()->route('platform.course.details', $this->course);
    }

    public function uploadMaterial(Request $request)
    {
        $materialTitle = $request->input('material.title');
        $materialAttachment = $request->file('material.attachment');

        $materials = $this->course->materials ?? [];

        if ($materialAttachment) {
            $attachment = Attachment::create([
                'file' => $materialAttachment,
            ]);

            $materials[] = [
                'title'      => $materialTitle,
                'attachment' => $attachment->url,
                'date'       => now()->format('Y-m-d'),
            ];
        } else {
            $materials[] = [
                'title'      => $materialTitle,
                'attachment' => null,
                'date'       => now()->format('Y-m-d'),
            ];
        }

        $this->course->materials = $materials;
        $this->course->save();

        return redirect()->route('platform.course.details', $this->course);
    }

    /**
     * Updated getMaterialsContent method to include an "Edit Material" button.
     */
    protected function getMaterialsContent(): array
    {
        $fields = [];
        foreach ($this->course->materials ?? [] as $index => $material) {
            $fields[] = Label::make('')
                ->title('Material: ' . $material['title'])
                ->value('Uploaded on: ' . ($material['date'] ?? now()->format('Y-m-d')));
            $fields[] = Button::make('Edit Material')
                ->method('editMaterial')
                ->parameters(['index' => $index]);
            $fields[] = Button::make('Delete Material')
                ->method('deleteMaterial')
                ->parameters(['index' => $index]);
        }
        return $fields;
    }

    /**
     * Redirects to the material edit screen.
     */
    public function editMaterial(Request $request)
    {
        $index = $request->input('index');
        return redirect()->route('platform.material.edit', [
            'course' => $this->course->id,
            'index'  => $index,
        ]);
    }

    /**
     * Delete material from the course.
     */
    public function deleteMaterial(Request $request, Course $course)
    {
        $index = $request->input('index');
        $materials = $course->materials;
        array_splice($materials, $index, 1);
        $course->materials = $materials;
        $course->save();

        return redirect()->route('platform.course.details', $course);
    }
}
