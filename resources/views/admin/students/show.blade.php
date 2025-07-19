@extends('layouts.admin')

@section('title', 'Student Details')
@section('page-title', 'Student Profile: ' . $student->user->name)

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-body text-center">
                <img src="{{ asset('storage/' . $student->user->user_pic) }}" alt="{{ $student->user->name }}" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                <h4>{{ $student->user->name }}</h4>
                <p class="text-muted">{{ $student->schoolClass->name }} - Section {{ $student->section }}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-body">
                <h5 class="card-title border-bottom pb-2 mb-3">Student Information</h5>
                <div class="row">
                    <div class="col-sm-6"><strong>Email:</strong> {{ $student->user->email }}</div>
                    <div class="col-sm-6"><strong>Phone:</strong> {{ $student->user->phone }}</div>
                    <div class="col-sm-6"><strong>ID Card #:</strong> {{ $student->id_card_number }}</div>
                    <div class="col-sm-12 mt-2"><strong>Address:</strong> {{ $student->address }}</div>
                </div>

                <h5 class="card-title border-bottom pb-2 mt-4 mb-3">Guardian Information</h5>
                 <div class="row">
                    <div class="col-sm-6"><strong>Father's Name:</strong> {{ $student->father_name }}</div>
                    <div class="col-sm-6"><strong>Father's Phone:</strong> {{ $student->father_phone }}</div>
                </div>
                
                @if($student->previous_school_docs)
                <h5 class="card-title border-bottom pb-2 mt-4 mb-3">Documents</h5>
                 <div class="row">
                    <div class="col-sm-12">
                        <a href="{{ asset('storage/' . $student->previous_school_docs) }}" target="_blank">View Previous Documents</a>
                    </div>
                </div>
                @endif
                <div class="mt-4">
                    <a href="{{ route('students.index') }}" class="btn btn-secondary">Back to List</a>
                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-primary">Edit Record</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
