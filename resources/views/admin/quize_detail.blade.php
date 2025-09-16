@extends('layouts.admin')

@section('title', 'LMS - Quiz Generator')

@push('styles')
<style>
    /* Styles for printing the quiz */
    @media print {
        /* Hide the admin layout's sidebar and header */
        .sidebar, .main-header {
            display: none !important;
        }
        /* Ensure the quiz content takes full width */
        .main-content {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>
@endpush

@section('content')
<div class="p-4 sm:p-6 lg:p-8" x-data="quizApp()">

    <div class="bg-white shadow-lg rounded-lg mb-8 print:hidden" id="quiz-generator-form">
        <div class="px-6 py-5 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-slate-800">Quiz Generator</h2>
            <p class="text-sm text-slate-600">Create a custom quiz by selecting the criteria below.</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label for="class" class="block text-sm font-medium leading-6 text-gray-900">1. Select Class</label>
                    <select x-model="selectedClass" id="class" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        <option value="">-- Choose a Class --</option>
                        <template x-for="classItem in lmsData" :key="classItem.id">
                            <option :value="classItem.id" x-text="classItem.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium leading-6 text-gray-900">2. Select Subject</label>
                    <select x-model="selectedSubject" id="subject" :disabled="!subjects.length" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6 disabled:bg-gray-100">
                        <option value="">-- Choose a Subject --</option>
                        <template x-for="subject in subjects" :key="subject.id">
                            <option :value="subject.id" x-text="subject.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="chapter" class="block text-sm font-medium leading-6 text-gray-900">3. Select Chapter</label>
                    <select x-model="selectedChapter" id="chapter" :disabled="!chapters.length" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6 disabled:bg-gray-100">
                        <option value="">-- Choose a Chapter --</option>
                        <template x-for="chapter in chapters" :key="chapter.id">
                            <option :value="chapter.id" x-text="chapter.name"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label for="topic" class="block text-sm font-medium leading-6 text-gray-900">4. Select Topic</label>
                    <select x-model="selectedTopic" id="topic" :disabled="!topics.length" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6 disabled:bg-gray-100">
                        <option value="">-- Choose a Topic --</option>
                        <template x-for="topic in topics" :key="topic.id">
                            <option :value="topic.id" x-text="topic.name"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="mt-6" x-show="selectedTopic">
                <label class="block text-sm font-medium leading-6 text-gray-900">5. Select Question Types</label>
                <div class="mt-4 flex flex-wrap gap-x-6 gap-y-4">
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center"><input id="mcqs" x-model="questionTypes.mcqs" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"></div>
                        <div class="ml-3 text-sm leading-6"><label for="mcqs" class="font-medium text-gray-900">MCQs</label></div>
                    </div>
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center"><input id="short" x-model="questionTypes.short" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"></div>
                        <div class="ml-3 text-sm leading-6"><label for="short" class="font-medium text-gray-900">Short Questions</label></div>
                    </div>
                    <div class="relative flex items-start">
                        <div class="flex h-6 items-center"><input id="long" x-model="questionTypes.long" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"></div>
                        <div class="ml-3 text-sm leading-6"><label for="long" class="font-medium text-gray-900">Long Questions</label></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-5 border-t border-gray-200">
                <div class="flex justify-end">
                    <button @click="generateQuiz" :disabled="!selectedTopic || (!questionTypes.mcqs && !questionTypes.short && !questionTypes.long)" type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:bg-indigo-300 disabled:cursor-not-allowed">
                        Generate Quiz
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div x-show="quizGenerated" x-cloak id="quiz-content" class="bg-white shadow-lg rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-slate-800">Generated Quiz</h2>
                <p class="text-sm text-slate-600">
                    <span x-text="getDrilldownText()"></span>
                </p>
            </div>
            <button @click="window.print()" class="print:hidden inline-flex items-center gap-x-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 2.75C5 1.784 5.784 1 6.75 1h6.5c.966 0 1.75.784 1.75 1.75v3.552c.377.046.74.14 1.095.278.369.143.68.358.918.625a2.535 2.535 0 01.625.918c.138.355.232.718.278 1.095v4.552c-.046.377-.14.74-.278 1.095a2.553 2.553 0 01-.625.918 2.535 2.535 0 01-.918.625c-.355.138-.718.232-1.095.278V19.25c0 .966-.784 1.75-1.75 1.75h-6.5A1.75 1.75 0 015 19.25v-3.552a4.425 4.425 0 01-1.373-.903 2.535 2.535 0 01-.625-.918A4.425 4.425 0 012.722 13.5v-4.552c.046-.377.14-.74.278-1.095a2.535 2.535 0 01.625-.918A2.553 2.553 0 014.542 6.3c.355-.138.718-.232 1.095-.278V2.75zM6.5 2.5a.25.25 0 00-.25.25v14.5c0 .138.112.25.25.25h6.5a.25.25 0 00.25-.25V2.75a.25.25 0 00-.25-.25h-6.5z" clip-rule="evenodd" />
                    <path d="M9 12.5a1 1 0 102 0 1 1 0 00-2 0z" />
                </svg>
                Print Quiz
            </button>
        </div>
        
        <div class="p-6 space-y-8">
            <div x-show="questionTypes.mcqs">
                <h3 class="text-lg font-bold border-b pb-2 mb-4">Multiple Choice Questions (MCQs)</h3>
                <div x-show="mcqsSubmitted" class="mb-6 p-4 bg-indigo-50 border border-indigo-200 rounded-lg">
                    <h4 class="font-bold text-indigo-800">MCQ Results</h4>
                    <p class="text-indigo-700">You scored <span x-text="score" class="font-extrabold"></span> out of <span x-text="quizData.mcqs.length" class="font-extrabold"></span>.</p>
                </div>
                <div class="space-y-6">
                    <template x-for="(mcq, index) in quizData.mcqs" :key="index">
                        <div class="border rounded-lg p-4" :class="{ 'border-gray-200': !mcqsSubmitted, 'bg-green-50 border-green-300': mcqsSubmitted && userAnswers[index] == mcq.correct, 'bg-red-50 border-red-300': mcqsSubmitted && userAnswers[index] != mcq.correct }">
                            <p class="font-semibold"><span x-text="index + 1"></span>. <span x-text="mcq.question"></span></p>
                            <div class="mt-4 space-y-3 pl-6">
                                <template x-for="(option, optionIndex) in mcq.options" :key="optionIndex">
                                    <div class="flex items-center">
                                        <input :id="'q'+index+'o'+optionIndex" :name="'question_'+index" type="radio" x-model="userAnswers[index]" :value="optionIndex" :disabled="mcqsSubmitted" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                        <label :for="'q'+index+'o'+optionIndex" class="ml-3 block text-sm text-gray-700" x-text="option"></label>
                                        <span x-show="mcqsSubmitted && optionIndex == mcq.correct" class="ml-4 text-green-600 font-bold text-sm">(Correct Answer)</span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="mt-6 print:hidden">
                    <button @click="checkMcqs" x-show="!mcqsSubmitted" class="inline-flex items-center justify-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700">Check Answers</button>
                </div>
            </div>

            <div x-show="questionTypes.short">
                <h3 class="text-lg font-bold border-b pb-2 mb-4">Short Questions</h3>
                <ol class="list-decimal list-inside space-y-6">
                    <template x-for="question in quizData.short" :key="question">
                        <li class="font-semibold" x-text="question">
                            <div class="mt-2 border-b-2 border-dotted h-16 print:h-24"></div>
                        </li>
                    </template>
                </ol>
            </div>

            <div x-show="questionTypes.long">
                <h3 class="text-lg font-bold border-b pb-2 mb-4">Long Questions</h3>
                <ol class="list-decimal list-inside space-y-8">
                    <template x-for="question in quizData.long" :key="question">
                        <li class="font-semibold" x-text="question">
                            <div class="mt-2 border-b-2 border-dotted h-24 print:h-48"></div>
                        </li>
                    </template>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function quizApp() {
    return {
        // --- DATA ---
        // EXPANDED MOCK DATA
        lmsData: [
            { id: 'c1', name: 'Class 10', subjects: [
                { id: 's1', name: 'Physics', chapters: [
                    { id: 'ch1', name: 'Chapter 1: Simple Harmonic Motion', topics: [{ id: 't1', name: 'Basics of SHM' }, { id: 't2', name: 'Damped Oscillations' }] },
                    { id: 'ch2', name: 'Chapter 2: Waves', topics: [{ id: 't3', name: 'Transverse Waves' }] },
                ]},
                { id: 's2', name: 'Chemistry', chapters: [
                    { id: 'ch3', name: 'Chapter 1: Chemical Equilibrium', topics: [{ id: 't4', name: 'Reversible Reactions' }] },
                ]},
            ]},
            { id: 'c2', name: 'Class 9', subjects: [
                { id: 's3', name: 'Biology', chapters: [
                    { id: 'ch4', name: 'Chapter 1: Introduction to Biology', topics: [{ id: 't5', name: 'Branches of Biology' }] },
                    { id: 'ch10', name: 'Chapter 2: Cell Biology', topics: [{ id: 't13', name: 'The Microscope' }, { id: 't14', name: 'Cell Structure' }] },
                ]},
                { id: 's6', name: 'History', chapters: [
                    { id: 'ch11', name: 'Chapter 5: The Mughal Empire', topics: [{ id: 't15', name: 'Founding of the Empire' }, { id: 't16', name: 'Reign of Akbar' }] },
                ]},
            ]},
            { id: 'c3', name: 'Class 11', subjects: [
                { id: 's4', name: 'Computer Science', chapters: [
                    { id: 'ch5', name: 'Chapter 1: Programming Fundamentals', topics: [{ id: 't6', name: 'Variables & Data Types' }, { id: 't7', name: 'Conditional Statements' }] },
                ]},
                { id: 's5', name: 'Mathematics', chapters: [
                    { id: 'ch6', name: 'Chapter 2: Sets and Functions', topics: [{ id: 't8', name: 'Venn Diagrams' }] },
                ]},
            ]},
            { id: 'c4', name: 'Class 8', subjects: [
                { id: 's7', name: 'General Science', chapters: [
                    { id: 'ch7', name: 'Chapter 3: The Solar System', topics: [{ id: 't9', name: 'Planets' }, { id: 't10', name: 'Stars and Galaxies' }] },
                ]},
                { id: 's8', name: 'Geography', chapters: [
                    { id: 'ch8', name: 'Chapter 1: Maps and Globes', topics: [{ id: 't11', name: 'Reading a Map' }] },
                    { id: 'ch9', name: 'Chapter 4: Climate', topics: [{ id: 't12', name: 'Weather vs. Climate' }] },
                ]},
            ]},
        ],
        
        selectedClass: '',
        selectedSubject: '',
        selectedChapter: '',
        selectedTopic: '',
        questionTypes: { mcqs: true, short: false, long: false },
        quizGenerated: false,
        quizData: { mcqs: [], short: [], long: [] },
        userAnswers: {},
        mcqsSubmitted: false,
        score: 0,

        get subjects() {
            if (!this.selectedClass) return [];
            return this.lmsData.find(c => c.id === this.selectedClass)?.subjects || [];
        },
        get chapters() {
            if (!this.selectedSubject) return [];
            return this.subjects.find(s => s.id === this.selectedSubject)?.chapters || [];
        },
        get topics() {
            if (!this.selectedChapter) return [];
            return this.chapters.find(ch => ch.id === this.selectedChapter)?.topics || [];
        },
        
        generateQuiz() {
            this.mcqsSubmitted = false; this.userAnswers = {}; this.score = 0;
            this.quizData = this.getMockQuestions(this.selectedTopic);
            this.quizGenerated = true;
        },

        checkMcqs() {
            this.score = 0;
            this.quizData.mcqs.forEach((mcq, index) => {
                if (this.userAnswers[index] == mcq.correct) {
                    this.score++;
                }
            });
            this.mcqsSubmitted = true;
        },

        getDrilldownText() {
            const classObj = this.lmsData.find(c => c.id === this.selectedClass);
            const subjectObj = classObj?.subjects.find(s => s.id === this.selectedSubject);
            const chapterObj = subjectObj?.chapters.find(ch => ch.id === this.selectedChapter);
            const topicObj = chapterObj?.topics.find(t => t.id === this.selectedTopic);
            return [classObj?.name, subjectObj?.name, chapterObj?.name, topicObj?.name].filter(Boolean).join(' → ');
        },

        getMockQuestions(topicId) {
            // EXPANDED MOCK DATABASE OF QUESTIONS
            const allQuestions = {
                t1: { mcqs: [{ question: 'What is the condition for an object to execute Simple Harmonic Motion (SHM)?', options: ['Constant velocity', 'Acceleration is proportional to displacement', 'Constant force', 'Zero acceleration'], correct: 1 }, { question: 'The time period of a simple pendulum depends on its:', options: ['Mass', 'Amplitude', 'Length', 'Color'], correct: 2 }], short: ['Define Simple Harmonic Motion.', 'What is a restoring force?'], long: ['Derive the formula for the time period of a simple pendulum.'] },
                t2: { mcqs: [], short: ['What are damped oscillations?'], long: [] },
                t3: { mcqs: [{ question: 'Which of the following is an example of a transverse wave?', options: ['Sound wave', 'Light wave', 'Ultrasound'], correct: 1 }], short: [], long: [] },
                t4: { mcqs: [], short: ['Define chemical equilibrium.'], long: [] },
                t5: { mcqs: [{ question: 'The study of insects is called:', options: ['Zoology', 'Entomology', 'Botany', 'Genetics'], correct: 1 }, { question: 'Which branch deals with the study of fossils?', options: ['Ecology', 'Physiology', 'Histology', 'Paleontology'], correct: 3 }, { question: 'Anatomy is the study of:', options: ['Internal structures', 'Functions of living organisms', 'Viruses', 'Cells'], correct: 0 }], short: ['What is Morphology?', 'Differentiate between Anatomy and Physiology.'], long: [] },
                t6: { mcqs: [{ question: 'Which of the following is an integer data type?', options: ['"hello"', '3.14', '42', 'true'], correct: 2 }, { question: 'A variable that can only be true or false is called a:', options: ['String', 'Float', 'Integer', 'Boolean'], correct: 3 }], short: ['What is a variable in programming?', 'Explain the difference between an integer and a float.'], long: ['Describe the concept of "type casting" with an example.'] },
                t7: { mcqs: [{ question: 'Which keyword is used for a conditional statement?', options: ['for', 'while', 'if', 'return'], correct: 2 }], short: ['What is an "if-else" statement?'], long: [] },
                t8: { mcqs: [{ question: 'What does the overlapping area of two circles in a Venn diagram represent?', options: ['Union', 'Intersection', 'Difference', 'Complement'], correct: 1 }], short: ['Draw a Venn diagram to represent A ∪ B.'], long: ['Explain how to represent disjoint sets using a Venn diagram.'] },
                t9: { mcqs: [{ question: 'Which planet is known as the Red Planet?', options: ['Earth', 'Mars', 'Jupiter', 'Saturn'], correct: 1 }], short: ['Name the eight planets in our solar system.'], long: [] },
                t10: { mcqs: [], short: ['What is a galaxy?'], long: ['Explain the life cycle of a star.'] },
                t11: { mcqs: [{ question: 'What does a map key or legend do?', options: ['Shows direction', 'Explains symbols', 'Shows the scale'], correct: 1 }], short: ['What is a compass rose?'], long: [] },
                t12: { mcqs: [], short: ['What is the main difference between weather and climate?'], long: [] },
                t13: { mcqs: [], short: ['Who is credited with inventing the microscope?'], long: [] },
                t14: { mcqs: [{ question: 'Which organelle is known as the powerhouse of the cell?', options: ['Nucleus', 'Ribosome', 'Mitochondria', 'Cell Wall'], correct: 2 }], short: ['What is the function of the cell membrane?'], long: [] },
                t15: { mcqs: [], short: ['Who was the founder of the Mughal Empire?'], long: [] },
                t16: { mcqs: [], short: ['What was Din-i-Ilahi?'], long: ['Describe the key administrative reforms under Akbar.'] },
            };
            return allQuestions[topicId] || { mcqs: [], short: [], long: [] };
        },
        
        init() {
            this.$watch('selectedClass', () => { this.selectedSubject = ''; this.quizGenerated = false; });
            this.$watch('selectedSubject', () => { this.selectedChapter = ''; this.quizGenerated = false; });
            this.$watch('selectedChapter', () => { this.selectedTopic = ''; this.quizGenerated = false; });
            this.$watch('selectedTopic', () => { this.quizGenerated = false; });
        }
    }
}
</script>
@endpush