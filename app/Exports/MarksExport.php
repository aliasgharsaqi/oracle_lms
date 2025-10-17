<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MarksExport implements FromCollection, WithHeadings, WithMapping
{
    protected $students;
    protected $selectedSubjectId;
    protected $selectedSemesterId;

    public function __construct($students, $selectedSubjectId, $selectedSemesterId)
    {
        $this->students = $students;
        $this->selectedSubjectId = $selectedSubjectId;
        $this->selectedSemesterId = $selectedSemesterId;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->students;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Student Name',
            'Roll Number',
            'Total Marks',
            'Obtained Marks',
        ];
    }

    /**
     * @param Student $student
     * @return array
     */
    public function map($student): array
    {
        $mark = $student->marks
            ->where('subject_id', $this->selectedSubjectId)
            ->where('semester_id', $this->selectedSemesterId)
            ->first();

        return [
            $student->user->name,
            $student->roll_number ?? 'N/A', // Assuming students have a roll number
            $mark->total_marks ?? 100,
            $mark->obtained_marks ?? 'Not Graded',
        ];
    }
}
