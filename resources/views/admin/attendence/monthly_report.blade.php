@extends('layouts.admin')

@section('title', 'Monthly Attendance Report')
@section('page-title', 'Monthly Attendance Report')

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
    .att-p { background-color: #dcfce7; color: #166534; } /* Present */
    .att-a { background-color: #fee2e2; color: #991b1b; } /* Absent */
    .att-l { background-color: #fef9c3; color: #854d0e; } /* Late */
    .att-sl { background-color: #f3e8ff; color: #6b21a8; } /* Short Leave */
    .att-lv { background-color: #e0f2fe; color: #0369a1; } /* Leave */
    .att-w { background-color: #f3f4f6; color: #4b5563; } /* Weekend */
    .att-na { background-color: #f3f4f6; color: #9ca3af; } /* Not Marked */

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

    {{-- Filter aur Legend Card --}}
    <div class="mb-6 p-4 bg-white rounded-lg shadow-md">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <form method="GET" action="{{ route('attendence.teacher.monthly_report') }}">
                <label for="month" class="block text-sm font-medium text-gray-700">Select Month</label>
                <div class="flex items-center gap-4 mt-1">
                    <input type="month" name="month" id="month"
                           value="{{ $selectedMonth }}"
                           class="block w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <button type="submit"
                            class="px-5 py-2 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Filter
                    </button>
                </div>
            </form>
             <div class="mt-4 md:mt-0 md:text-right">
                <a href="{{ route('attendence.teacher') }}" class="text-sm text-blue-600 hover:underline">
                    &larr; Back to Daily Attendance
                </a>
            </div>
        </div>
        
        {{-- Naya Legend Section --}}
        <div class="border-t mt-4 pt-4">
            <h4 class="text-xs font-semibold text-gray-600 uppercase mb-2">Legend</h4>
            <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm text-gray-700">
                <div class="flex items-center gap-1.5"><span class="att-status att-legend att-p">P</span> Present</div>
                <div class="flex items-center gap-1.5"><span class="att-status att-legend att-a">A</span> Absent</div>
                <div class="flex items-center gap-1.5"><span class="att-status att-legend att-l">L</span> Late</div>
                <div class="flex items-center gap-1.5"><span class="att-status att-legend att-lv">LV</span> Leave</div>
                <div class="flex items-center gap-1.5"><span class="att-status att-legend att-sl">SL</span> Short Leave</div>
                <div class="flex items-center gap-1.5"><span class="att-status att-legend att-w">W</span> Weekend</div>
            </div>
        </div>
    </div>

    {{-- 
        ============================================================
        UNIFIED RESPONSIVE DESIGN (CARD-BASED GRID)
        ============================================================
        Ye section ab har screen par nazar ayega.
        Ye 1-col (mobile), 2-col (tablet/desktop), aur 3-col (large desktop) layout hai.
    --}}
    <div class="grid grid-cols-1 gap-4">
        @foreach ($teachers as $teacher)
            @php
                // Har teacher ke liye logic ko pre-calculate karein
                $totalPresent = 0; $totalAbsent = 0; $totalLate = 0; $totalLeave = 0; $totalShortLeave = 0;
                $dateCells = []; // Din (days) aur unka HTML store karne ke liye

                foreach ($dates as $date) {
                    $dateString = $date->format('Y-m-d');
                    $record = $attendanceMatrix[$teacher->id][$dateString] ?? null;
                    
                    // att-status-sm mobile ke liye choti class hai
                    $statusDisplay = '<span class="att-status att-status-sm att-na">-</span>'; 
                    
                    if ($record) {
                        switch ($record->status) {
                            case 'present':
                                $statusDisplay = '<span class="att-status att-status-sm att-p">P</span>'; $totalPresent++; break;
                            case 'absent':
                                $statusDisplay = '<span class="att-status att-status-sm att-a">A</span>'; $totalAbsent++; break;
                            case 'late_arrival':
                                $statusDisplay = '<span class="att-status att-status-sm att-l">L</span>'; $totalLate++; break;
                            case 'leave':
                                $statusDisplay = '<span class="att-status att-status-sm att-lv">L</span>'; $totalLeave++; break;
                            case 'short_leave':
                                $statusDisplay = '<span class="att-status att-status-sm att-sl">SL</span>'; $totalShortLeave++; break;
                        }
                    } elseif ($date->isSunday()) {
                        $statusDisplay = '<span class="att-status att-status-sm att-w">W</span>';
                    } elseif ($date->isFuture()) {
                        $statusDisplay = '<span class="att-status att-status-sm att-na">-</span>';
                    }
                    
                    $dateCells[] = [
                        'day' => $date->format('j'),
                        'dayName' => $date->format('D'),
                        'html' => $statusDisplay,
                        'isWeekend' => $date->isSunday(),
                    ];
                }
            @endphp

            {{-- Mobile Card --}}
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-4 border-b">
                    <h3 class="font-bold text-lg text-gray-900">{{ optional($teacher->user)->name ?? 'N/A' }}</h3>
                    <p class="text-xs text-gray-500">{{ optional($teacher->user)->email ?? 'N/A' }}</p>
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
                        <span class="block text-xl font-bold text-blue-600">{{ $totalLeave + $totalShortLeave }}</span>
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
                            <div class="flex flex-col items-center py-1 {{ $cell['isWeekend'] ? 'bg-gray-50' : '' }} rounded-sm">
                                <span class="text-xs text-gray-500">{{ $cell['day'] }}</span>
                                {!! $cell['html'] !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- 
        ============================================================
        PURANA DESKTOP VIEW (TABLE-BASED) - (REMOVED)
        ============================================================
        Maine desktop wala table wala section poori tarah delete kar diya hai.
    --}}

@endsection