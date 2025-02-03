<?php

namespace App\Orchid\Screens;

use App\Models\Course;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Label;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Layouts\Tabs;

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
                    // Display assignments and materials in a single stream
                    ...$this->getStreamContent(), // Spread the array of Label objects
                ]),

                'Assignments' => Layout::rows([
                    // Display only assignments in a separate tab
                    ...$this->getAssignmentsContent(), // Spread the array of Label objects
                ]),

                'Students' => Layout::rows([
                    // Display students in a separate tab
                    Label::make('students')->title('Students')->value(implode(', ', $this->course->students ?? [])),
                ]),
            ]),
        ];
    }

    /**
     * Generate the stream content (assignments and materials sorted by date).
     */
    protected function getStreamContent(): array
    {
        // Combine assignments and materials into a single array
        $assignments = $this->course->assignments ?? [];
        $materials = $this->course->materials ?? [];

        $stream = [];

        // Add assignments to the stream
        foreach ($assignments as $assignment) {
            $stream[] = [
                'type' => 'Assignment',
                'title' => $assignment['title'] ?? 'Untitled Assignment',
                'description' => $assignment['description'] ?? '',
                'date' => $assignment['date'] ?? now()->format('Y-m-d'),
            ];
        }

        // Add materials to the stream
        foreach ($materials as $material) {
            $stream[] = [
                'type' => 'Material',
                'title' => $material['title'] ?? 'Untitled Material',
                'description' => $material['description'] ?? '',
                'date' => $material['date'] ?? now()->format('Y-m-d'),
            ];
        }

        // Sort the stream by date (newest first)
        usort($stream, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Generate the stream content as an array of Label objects
        $streamContent = [];
        foreach ($stream as $item) {
            $streamContent[] = Label::make('')
                ->title($item['type'] . ': ' . $item['title'])
                ->value('Posted on: ' . $item['date'] . '<br>' . $item['description']);
        }

        return $streamContent;
    }

    /**
     * Generate the assignments content.
     */
    protected function getAssignmentsContent(): array
    {
        $assignments = $this->course->assignments ?? [];

        $assignmentsContent = [];

        // Add assignments to the assignments tab as an array of Label objects
        foreach ($assignments as $assignment) {
            $assignmentsContent[] = Label::make('')
                ->title('Assignment: ' . $assignment['title'])
                ->value('Posted on: ' . ($assignment['date'] ?? now()->format('Y-m-d')) . '<br>' . $assignment['description']);
        }

        return $assignmentsContent;
    }
}
