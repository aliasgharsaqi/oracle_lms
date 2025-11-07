@extends('layouts.admin')

@section('title', 'Student Attendance Report')
@section('page-title', 'Attendance Report')

@push('styles')
<style>
    .attendance-cell {
        text-align: center;
        font-weight: bold;
        width: 40px;
        height: 40px;
        padding: 0.5rem;
    }
    .status-present { color: #10b981; } /* Green */
    .status-absent { color: #ef4444; } /* Red */
    .status-late { color: #f59e0b; } /* Amber */
    .status-leave { color: #6b7280; } /* Gray */
    .weekend-col { background-color: #f9fafb; } /* Gray-50 */
</style>
@endpush

@section('content')

{{-- Filter Form --}}
<div class="bg-white shadow-lg rounded-lg p-6 mb-6">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Select Class and Month</h3>
    <form action="{{ route('attendance.showReport') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        @csrf
        <div>
            <label for="school_class_id" class="block text-sm font-medium text-gray-700">Class</label>
            <select id="school_class_id" name="school_class_id" class="mt-1 block w-full p-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
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
            <input type="month" id="month" name="month" 
                   value="{{ $month ?? now()->format('Y-m') }}" 
                   class="mt-1 block w-full rounded-md p-2 border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
        </div>
       <div class="flex flex-col sm:flex-row gap-3 items-end">
            <button type="submit" class="w-full sm:w-auto justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Generate Report
            </button>
            <a href="{{ route('attendance.create') }}" class="w-full sm:w-auto flex items-center justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Mark Attendance
            </a>
        </div>
    </form>
</div>

{{-- Attendance Report Table --}}
@if(isset($students))
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">
            Attendance for {{ $schoolClass->name }} - {{ $carbonMonth->format('F Y') }}
        </h3>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider sticky left-0 bg-gray-100 z-10">Student</th>
                        @foreach($dateRange as $day)
                            @php
                                $date = $carbonMonth->copy()->day($day);
                                $isWeekend = $date->isSaturday() || $date->isSunday();
                            @endphp
                            <th scope="col" class="px-2 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider {{ $isWeekend ? 'weekend-col' : '' }}">
                                {{ $day }}<br>{{ $date->format('D') }}
                            </th>
                        @endforeach
                        <th scope="col" class="px-2 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider bg-green-50">P</th>
                        <th scope="col" class="px-2 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider bg-red-50">A</th>
                        <th scope="col" class="px-2 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider bg-yellow-50">L</th>
                        <th scope="col" class="px-2 py-3 text-center text-xs font-medium text-gray-600 uppercase tracking-wider bg-gray-50">LV</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($students as $student)
                        @php
                            $studentRecords = $attendances->get($student->id);
                            $presentCount = 0;
                            $absentCount = 0;
                            $lateCount = 0;
                            $leaveCount = 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 sticky left-0 bg-white z-10">
                                {{ $student->user->name }}<br>
                                <span class="text-xs text-gray-500">{{ $student->id_card_number }}</span>
                            </td>
                            @foreach($dateRange as $day)
                                @php
                                    $dateStr = $carbonMonth->copy()->day($day)->format('Y-m-d');
                                    $record = $studentRecords->get($dateStr) ?? null;
                                    $status = $record->status ?? null;
                                    
                                    if ($status == 'present') { $presentCount++; $char = 'P'; $class = 'status-present'; }
                                    elseif ($status == 'absent') { $absentCount++; $char = 'A'; $class = 'status-absent'; }
                                    elseif ($status == 'late') { $lateCount++; $char = 'L'; $class = 'status-late'; }
                                    elseif ($status == 'leave') { $leaveCount++; $char = 'LV'; $class = 'status-leave'; }
                                    else { $char = '-'; $class = 'text-gray-400'; }

                                    $isWeekend = $carbonMonth->copy()->day($day)->isSaturday() || $carbonMonth->copy()->day($day)->isSunday();
                                @endphp
                                <td class="attendance-cell {{ $class }} {{ $isWeekend && !$status ? 'weekend-col' : '' }}">
                                    {{ $isWeekend && !$status ? '' : $char }}
                                </td>
                            @endforeach
                            {{-- Totals --}}
                            <td class="attendance-cell bg-green-50 text-green-700">{{ $presentCount }}</td>
                            <td class="attendance-cell bg-red-50 text-red-700">{{ $absentCount }}</td>
                            <td class="attendance-cell bg-yellow-50 text-yellow-700">{{ $lateCount }}</td>
                            <td class="attendance-cell bg-gray-50 text-gray-700">{{ $leaveCount }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@endsection