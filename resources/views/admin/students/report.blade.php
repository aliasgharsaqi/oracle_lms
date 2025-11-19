@extends('layouts.admin')

@section('title', 'Student Attendance Report')
@section('page-title', 'Attendance Report')

@push('styles')
{{-- Ye CSS status (P, A, L) ko colors dega --}}
<style>
    .att-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        font-weight: 700;
        border-radius: 50%;
        font-size: 12px;
        line-height: 1;
    }
    /* Tailwind classes are used for backgrounds directly in the HTML.
      These classes are for the text color inside the circles.
    */
    .att-p { background-color: #dcfce7; color: #166534; } /* Present (bg-green-50) */
    .att-a { background-color: #fee2e2; color: #991b1b; } /* Absent (bg-red-50) */
    .att-l { background-color: #fef9c3; color: #854d0e; } /* Late (bg-yellow-50) */
    .att-sl { background-color: #f3e8ff; color: #6b21a8; } /* Short Leave (bg-purple-50) */
    .att-lv { background-color: #e0f2fe; color: #0369a1; } /* Leave (bg-blue-50) */
    .att-w { background-color: #f3f4f6; color: #4b5563; } /* Weekend (bg-gray-100) */
    .att-na { background-color: #f3f4f6; color: #9ca3af; } /* Not Marked (bg-gray-100) */

    /* Small legend badges */
    .att-legend {
        width: 20px;
        height: 20px;
        font-size: 10px;
    }

    /* Mobile grid ke liye chote circles */
    .att-status-sm {
        width: 24px;
        height: 24px;
        font-size: 10px;
    }
</style>
@endpush

@section('content')

{{-- Filter Form --}}
<div class="bg-white shadow-lg rounded-lg p-6 mb-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Select Class and Month</h3>
    <form action="{{ route('attendance.showReport') }}" method="POST"
        class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        @csrf
        <div>
            <label for="school_class_id" class="block text-sm font-medium text-gray-700">Class</label>
            <select id="school_class_id" name="school_class_id"
                class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required>
                <option value="">Select a class</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ (isset($schoolClass) && $schoolClass->id == $class->id) ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
            <input type="month" id="month" name="month" value="{{ $month ?? now()->format('Y-m') }}"
                class="mt-1 block w-full rounded-md p-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                required>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 items-end">
            <button type="submit"
                class="w-full sm:w-auto justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Generate Report
            </button>
            <a href="{{ route('attendance.create') }}"
                class="w-full sm:w-auto flex items-center justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Mark Attendance
            </a>
        </div>
    </form>

    {{-- Naya Legend Section --}}
    <div class="border-t mt-4 pt-4">
        <h4 class="text-xs font-semibold text-gray-600 uppercase mb-2">Legend</h4>
        <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm text-gray-700">
            <div class="flex items-center gap-1.5"><span class="att-status att-legend att-p">P</span> Present</div>
            <div class="flex items-center gap-1.5"><span class="att-status att-legend att-a">A</span> Absent</div>
            <div class="flex items-center gap-1.5"><span class="att-status att-legend att-l">L</span> Late</div>
            <div class="flex items-center gap-1.5"><span class="att-status att-legend att-lv">LV</span> Leave</div>
            <div class="flex items-center gap-1.5"><span class="att-status att-legend att-w">W</span> Weekend</div>
        </div>
    </div>
</div>

{{-- Attendance Report Cards --}}
@if(isset($students))
    <h3 class="text-xl font-semibold text-gray-800 mb-4">
        Attendance for {{ $schoolClass->name }} - {{ $carbonMonth->format('F Y') }}
    </h3>

    {{-- 
        ============================================================
        UNIFIED RESPONSIVE DESIGN (CARD-BASED GRID)
        ============================================================
    --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($students as $student)
            @php
                // Har student ke liye logic ko pre-calculate karein
                $studentRecords = $attendances->get($student->id) ?? collect(); // Fix for null
                $totalPresent = 0; $totalAbsent = 0; $totalLate = 0; $totalLeave = 0;
                $dateCells = []; // Din (days) aur unka HTML store karne ke liye

                foreach ($dateRange as $day) {
                    $date = $carbonMonth->copy()->day($day);
                    $dateString = $date->format('Y-m-d');
                    $record = $studentRecords->get($dateString) ?? null;
                    
                    // Sirf Sunday ko weekend mana jayega
                    $isWeekend = $date->isSunday();

                    $statusDisplay = '<span class="att-status att-status-sm att-na">-</span>'; 
                    $statusClass = ''; // Default background class
                    
                    if ($record) {
                        switch ($record->status) {
                            case 'present':
                                $statusDisplay = '<span class="att-status att-status-sm att-p">P</span>'; 
                                $totalPresent++; 
                                $statusClass = 'bg-green-50'; // att-p background
                                break;
                            case 'absent':
                                $statusDisplay = '<span class="att-status att-status-sm att-a">A</span>'; 
                                $totalAbsent++; 
                                $statusClass = 'bg-red-50'; // att-a background
                                break;
                            case 'late': // Student report uses 'late'
                                $statusDisplay = '<span class="att-status att-status-sm att-l">L</span>'; 
                                $totalLate++; 
                                $statusClass = 'bg-yellow-50'; // att-l background
                                break;
                            case 'leave': // Student report uses 'leave'
                                $statusDisplay = '<span class="att-status att-status-sm att-lv">LV</span>';
                                $totalLeave++; 
                                $statusClass = 'bg-blue-50'; // att-lv background
                                break;
                            default:
                                 $statusClass = 'bg-gray-100'; // att-w/att-na background
                        }
                    } elseif ($isWeekend) {
                        $statusDisplay = '<span class="att-status att-status-sm att-w">W</span>';
                        $statusClass = 'bg-gray-100'; // att-w background
                    } else {
                         // Past dates that were not marked or future
                         $statusDisplay = '<span class="att-status att-status-sm att-na">-</span>';
                         $statusClass = 'bg-gray-100'; // att-na background
                    }
                    
                    $dateCells[] = [
                        'day' => $date->format('j'),
                        'dayName' => $date->format('D'),
                        'html' => $statusDisplay,
                        'isWeekend' => $isWeekend,
                        'statusClass' => $statusClass // Pass the class to the view
                    ];
                }
            @endphp

            {{-- Student Card --}}
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="font-bold text-lg text-gray-900">{{ $student->user->name }}</h3>
                    <p class="text-xs text-gray-500">{{ $student->id_card_number }}</p>
                </div>
                
                <div class="flex justify-around text-center py-3 bg-gray-50">
                    <div>
                        <span class="text-xs text-gray-500">Present</span>
                        <span class="block text-xl font-bold text-green-600">{{ $totalPresent }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Absent</span>
                        <span class="block text-xl font-bold text-red-600">{{ $totalAbsent }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Late</span>
                        <span class="block text-xl font-bold text-yellow-600">{{ $totalLate }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500">Leave</span>
                        <span class="block text-xl font-bold text-blue-600">{{ $totalLeave }}</span>
                    </div>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-7 gap-1">
                        {{-- Haftay ke dinon ka header (Mon, Tue...) --}}
                        @foreach ($dateCells as $cell)
                            @if ($loop->iteration > 7) @break @endif
                            <div class="text-center text-xs font-semibold text-gray-400">{{ $cell['dayName'] }}</div>
                        @endforeach
                        
                        {{-- Asal din (days) --}}
                        @foreach ($dateCells as $cell)
                            <div class="flex flex-col items-center py-1 {{ $cell['statusClass'] }} rounded-sm">
                                <span class="text-xs text-gray-500">{{ $cell['day'] }}</span>
                                {!! $cell['html'] !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection