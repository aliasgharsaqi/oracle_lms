@extends('layouts.admin')

@section('title', 'Admin - Financial Transactions')

{{-- Font Awesome CDN for icons (MUST BE IN HEAD SECTION) --}}
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Custom transition for action menu */
        .actions-menu {
            transition: all 0.2s ease-in-out;
        }
    </style>
@endpush

@section('content')

    {{-- Main container uses x-data with Laravel-passed JSON data --}}
    {{-- NOTE: Ensure you have <meta name="csrf-token" content="{{ csrf_token() }}"> in your main layout --}}
    <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto"
        x-data="transactionsApp('{{ $initialTransactions }}', {{ $totalIncome }}, {{ $totalExpenses }})">

        <h1 class="text-3xl font-extrabold text-gray-900 mb-6 border-b border-gray-200 pb-3">Financial Overview</h1>

        {{-- FINANCIAL SUMMARY CARDS --}}
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3 mb-10">

            {{-- Card 1: Total Income --}}
            <div
                class="flex items-center p-6 bg-white shadow-2xl rounded-xl border-l-4 border-green-600 hover:shadow-xl transition duration-300">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-14 h-14 flex items-center justify-center bg-green-100 rounded-full shadow-inner">
                        <i class="fa-solid fa-arrow-up text-green-700 text-2xl"></i>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Income (Completed)</div>
                    <div class="text-3xl font-bold text-gray-900 mt-1" x-text="formatCurrency(totalIncome)"></div>
                </div>
            </div>

            {{-- Card 2: Total Expenses --}}
            <div
                class="flex items-center p-6 bg-white shadow-2xl rounded-xl border-l-4 border-red-600 hover:shadow-xl transition duration-300">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-14 h-14 flex items-center justify-center bg-red-100 rounded-full shadow-inner">
                        <i class="fa-solid fa-arrow-down text-red-700 text-2xl"></i>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Expenses (Completed)</div>
                    <div class="text-3xl font-bold text-gray-900 mt-1" x-text="formatCurrency(totalExpenses)"></div>
                </div>
            </div>

            {{-- Card 3: Net Balance --}}
            <div
                class="flex items-center p-6 bg-white shadow-2xl rounded-xl border-l-4 border-indigo-600 hover:shadow-xl transition duration-300">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-14 h-14 flex items-center justify-center bg-indigo-100 rounded-full shadow-inner">
                        <i class="fa-solid fa-balance-scale text-indigo-700 text-2xl"></i>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Net Balance</div>
                    <div class="text-3xl font-bold mt-1" :class="netBalance >= 0 ? 'text-green-700' : 'text-red-700'"
                        x-text="formatCurrency(netBalance)"></div>
                </div>
            </div>
        </div>

        {{-- TRANSACTION TABLE AND CONTROLS --}}
        <div class="bg-white shadow-2xl rounded-2xl border border-gray-100">

            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex-auto">
                        <h2 class="text-2xl font-bold text-slate-800">Transaction History</h2>
                        <p class="text-sm text-slate-600">Review and manage all financial movements.</p>
                    </div>
                    <div class="flex-none">
                        <button @click="openModal = true" type="button"
                            class="inline-flex items-center justify-center rounded-xl border border-transparent bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 w-full sm:w-auto transition">
                            <i class="fa-solid fa-plus mr-2"></i> Record New Transaction
                        </button>
                    </div>
                </div>

                {{-- Search and Filters --}}
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                            </div>
                            <input type="text" x-model.debounce.300ms="search"
                                placeholder="Search by recipient or description..."
                                class="block w-full rounded-xl border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-gray-50">
                        </div>
                    </div>
                    <div>
                        <select x-model="filterType"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-gray-50">
                            <option value="all">All Types</option>
                            <option value="Income">Income</option>
                            <option value="Expense">Expense</option>
                        </select>
                    </div>
                    <div>
                        <select x-model="filterStatus"
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-gray-50">
                            <option value="all">All Statuses</option>
                            <option value="Completed">Completed</option>
                            <option value="Pending">Pending</option>
                            <option value="Failed">Failed</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Transaction Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-indigo-600 text-white shadow-md">
                        <tr>
                            <th scope="col"
                                class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider">
                                Recipient/Source</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">
                                Type</th>
                            <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold uppercase tracking-wider">
                                Amount</th>
                            <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold uppercase tracking-wider">
                                Status</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider">
                                Date</th>
                            <th scope="col"
                                class="relative py-3.5 pl-3 pr-6 text-center text-xs font-semibold uppercase tracking-wider">
                                Actions</th> {{-- ACTIONS HEADER --}}
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <template x-for="transaction in paginatedTransactions" :key="transaction.id">
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <img class="h-10 w-10 rounded-full object-cover border border-gray-200 shadow-md"
                                                :src="transaction.recipient.imageUrl" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900" x-text="transaction.recipient.name">
                                            </div>
                                            <div class="text-gray-500 text-xs" x-text="'ID: ' + transaction.id"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold"
                                    :class="transaction.type === 'Income' ? 'text-green-600' : 'text-red-600'"
                                    x-text="transaction.type">
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-right">
                                    <span class="font-bold"
                                        x-text="(transaction.type === 'Income' ? '+ ' : '- ') + formatCurrency(transaction.amount)"></span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-center">
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                        :class="{'bg-green-100 text-green-800': transaction.status === 'Completed', 'bg-yellow-100 text-yellow-800': transaction.status === 'Pending', 'bg-red-100 text-red-800': transaction.status === 'Failed' }"
                                        x-text="transaction.status">
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600" x-text="transaction.date">
                                </td>

                                {{-- ACTION BUTTONS --}}
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-center text-sm font-medium">
                                    <div class="inline-flex space-x-2">
                                        {{-- Edit Button (Pencil Icon) --}}
                                        <button @click="startEdit(transaction)" title="Edit Transaction"
                                            class="p-2 rounded-full text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 transition duration-150">
                                            <i class="fa-solid fa-pen-to-square text-xl"></i>Edit
                                        </button>

                                        {{-- Delete Button (Trash Icon) --}}
                                        <button @click="deleteTransaction(transaction.id)" title="Delete Transaction"
                                            class="p-2 rounded-full text-red-600 hover:text-red-800 hover:bg-red-50 transition duration-150">
                                            <i class="fa-solid fa-trash text-xl"></i>Delete
                                        </button>
                                    </div>
                                </td>
                                {{-- END ACTION BUTTONS --}}
                            </tr>
                        </template>
                        {{-- No Results Row --}}
                        <tr x-show="filteredTransactions.length === 0">
                            <td colspan="6" class="text-center py-10 px-4 text-gray-500 bg-gray-50"><i
                                    class="fa-solid fa-database text-4xl mb-2"></i>
                                <h3 class="mt-2 text-lg font-medium text-gray-900">No transactions found</h3>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-700">Showing <span x-text="pageStart" class="font-medium"></span> to <span
                            x-text="pageEnd" class="font-medium"></span> of <span x-text="filteredTransactions.length"
                            class="font-medium"></span> results</p>
                    <div class="flex-1 flex justify-end gap-2">
                        <button @click="prevPage" :disabled="currentPage === 1"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150">
                            <i class="fa-solid fa-chevron-left mr-2"></i> Previous
                        </button>
                        <button @click="nextPage" :disabled="currentPage === totalPages"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition duration-150">
                            Next <i class="fa-solid fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- New Transaction Modal (PKR Currency Updated) --}}
        <div x-show="openModal" @keydown.window.escape="openModal = false" class="relative z-50"
            aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-70 transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div @click.outside="openModal = false" x-show="openModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                        <form @submit.prevent="addTransaction()">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10 shadow-md">
                                        <i class="fa-solid fa-file-invoice-dollar text-indigo-600 text-xl"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-xl font-bold leading-6 text-gray-900" id="modal-title">Record New
                                            Transaction</h3>
                                        <p class="mt-1 text-sm text-gray-500">Fill in the details to record this financial
                                            event.</p>
                                    </div>
                                </div>

                                <div class="mt-6 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-6">
                                    <div class="sm:col-span-2">
                                        <label for="new_recipient"
                                            class="block text-sm font-medium leading-6 text-gray-900">Recipient/Source <span
                                                class="text-red-500">*</span></label>
                                        <div class="relative mt-2">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <i class="fa-solid fa-user text-gray-400"></i>
                                            </div>
                                            <input type="text" x-model="newTransaction.recipientName" id="new_recipient"
                                                placeholder="E.g., Salary, Rent, Vendor Payment" required
                                                class="block w-full rounded-xl border-gray-300 py-2 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="new_amount"
                                            class="block text-sm font-medium leading-6 text-gray-900">Amount <span
                                                class="text-red-500">*</span></label>
                                        <div class="relative mt-2">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm"><i class="fa-solid fa-rupee-sign"></i></span></div>
                                            <input type="number" x-model.number="newTransaction.amount" id="new_amount"
                                                step="0.01" placeholder="0.00" required
                                                class="block w-full rounded-xl border-gray-300 py-2 pl-7 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                <span class="text-gray-500 sm:text-sm">PKR</span></div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="new_date"
                                            class="block text-sm font-medium leading-6 text-gray-900">Date</label>
                                        <div class="relative mt-2">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <i class="fa-solid fa-calendar text-gray-400"></i>
                                            </div>
                                            <input type="date" x-model="newTransaction.date" id="new_date" required
                                                class="block w-full rounded-xl border-gray-300 py-2 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="new_type"
                                            class="block text-sm font-medium leading-6 text-gray-900">Type</label>
                                        <div class="relative mt-2">
                                            <select id="new_type" x-model="newTransaction.type"
                                                class="appearance-none block w-full rounded-xl border-gray-300 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50">
                                                <option>Income</option>
                                                <option>Expense</option>
                                            </select>
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="new_status"
                                            class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                                        <div class="relative mt-2">
                                            <select id="new_status" x-model="newTransaction.status"
                                                class="appearance-none block w-full rounded-xl border-gray-300 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50">
                                                <option>Completed</option>
                                                <option>Pending</option>
                                                <option>Failed</option>
                                            </select>
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="new_description"
                                            class="block text-sm font-medium leading-6 text-gray-900">Description
                                            (Optional)</label>
                                        <textarea id="new_description" x-model="newTransaction.description" rows="2"
                                            class="block w-full rounded-xl border-gray-300 py-2 pl-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50 resize-none"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-200">
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-xl border border-transparent bg-indigo-600 px-5 py-2.5 text-base font-semibold text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto transition">Save
                                    Transaction</button>
                                <button @click="openModal = false" type="button"
                                    class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-base font-medium text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Transaction Modal (PKR Currency Updated) --}}
        <div x-show="openEditModal" @keydown.window.escape="openEditModal = false" class="relative z-50"
            aria-labelledby="edit-modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div x-show="openEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900 bg-opacity-70 transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div @click.outside="openEditModal = false" x-show="openEditModal"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                        <form @submit.prevent="updateTransaction()">
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div
                                        class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10 shadow-md">
                                        <i class="fa-solid fa-pen-to-square text-indigo-600 text-xl"></i>
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                        <h3 class="text-xl font-bold leading-6 text-gray-900" id="edit-modal-title">Edit
                                            Transaction #<span x-text="editingTransaction.id"></span></h3>
                                        <p class="mt-1 text-sm text-gray-500">Update the details for this financial event.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-6 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-6">
                                    <div class="sm:col-span-2">
                                        <label for="edit_recipient"
                                            class="block text-sm font-medium leading-6 text-gray-900">Recipient/Source <span
                                                class="text-red-500">*</span></label>
                                        <div class="relative mt-2">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <i class="fa-solid fa-user text-gray-400"></i>
                                            </div>
                                            <input type="text" x-model="editingTransaction.recipient.name"
                                                id="edit_recipient" required
                                                class="block w-full rounded-xl border-gray-300 py-2 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="edit_amount"
                                            class="block text-sm font-medium leading-6 text-gray-900">Amount <span
                                                class="text-red-500">*</span></label>
                                        <div class="relative mt-2">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm"><i class="fa-solid fa-rupee-sign"></i></span></div>
                                            <input type="number" x-model.number="editingTransaction.amount"
                                                id="edit_amount" step="0.01" required
                                                class="block w-full rounded-xl border-gray-300 py-2 pl-7 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                                <span class="text-gray-500 sm:text-sm">PKR</span></div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="edit_date"
                                            class="block text-sm font-medium leading-6 text-gray-900">Date</label>
                                        <div class="relative mt-2">
                                            <div
                                                class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <i class="fa-solid fa-calendar text-gray-400"></i>
                                            </div>
                                            <input type="date" x-model="editingTransaction.date" id="edit_date"
                                                required
                                                class="block w-full rounded-xl border-gray-300 py-2 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50">
                                        </div>
                                    </div>

                                    <div>
                                        <label for="edit_type"
                                            class="block text-sm font-medium leading-6 text-gray-900">Type</label>
                                        <div class="relative mt-2">
                                            <select id="edit_type" x-model="editingTransaction.type"
                                                class="appearance-none block w-full rounded-xl border-gray-300 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50">
                                                <option>Income</option>
                                                <option>Expense</option>
                                            </select>
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="edit_status"
                                            class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                                        <div class="relative mt-2">
                                            <select id="edit_status" x-model="editingTransaction.status"
                                                class="appearance-none block w-full rounded-xl border-gray-300 py-2 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50">
                                                <option>Completed</option>
                                                <option>Pending</option>
                                                <option>Failed</option>
                                            </select>
                                            <div
                                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                                <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label for="edit_description"
                                            class="block text-sm font-medium leading-6 text-gray-900">Description
                                            (Optional)</label>
                                        <textarea id="edit_description" x-model="editingTransaction.description" rows="2"
                                            class="block w-full rounded-xl border-gray-300 py-2 pl-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 bg-gray-50 resize-none"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-200">
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-xl border border-transparent bg-indigo-600 px-5 py-2.5 text-base font-semibold text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto transition">Update
                                    Transaction</button>
                                <button @click="openEditModal = false" type="button"
                                    class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-2.5 text-base font-medium text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function transactionsApp(initialJson, initialIncome, initialExpenses) {
            let initialTransactions = [];
            
            // Get CSRF token from the meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]') ? 
                             document.querySelector('meta[name="csrf-token"]').getAttribute('content') : 
                             '';

            try {
                // IMPORTANT: Ensure db_id is correctly extracted/mapped here for API calls
                initialTransactions = JSON.parse(initialJson).map(tx => ({
                    ...tx,
                    // Check if db_id exists (from controller update), otherwise try to extract from 'id'
                    db_id: tx.db_id || (tx.id.startsWith('TXN-') ? tx.id.substring(4) : tx.id), 
                    recipient: tx.recipient || { name: tx.recipientName || tx.description || 'N/A', imageUrl: 'https://placehold.co/150x150/7c3aed/ffffff/png?text=TX' }
                }));
            } catch (e) {
                console.error("Failed to parse initial transactions JSON:", e);
            }

            return {
                // Modal States
                openModal: false,
                openEditModal: false,
                loading: false,

                // Filter States
                search: '',
                filterType: 'all',
                filterStatus: 'all',

                // Pagination States
                currentPage: 1,
                itemsPerPage: 8,
                _lastSearch: '',
                _lastType: 'all',
                _lastStatus: 'all',

                // Core States (Financial Summary)
                totalIncome: parseFloat(initialIncome),
                totalExpenses: parseFloat(initialExpenses),
                transactions: initialTransactions,

                // New Transaction Form State (for Add Modal)
                newTransaction: {
                    recipientName: '',
                    amount: null,
                    type: 'Income',
                    status: 'Completed',
                    // Use correct date format for input binding
                    date: new Date().toISOString().slice(0, 10), 
                    description: '',
                },

                // Edit Transaction State (for Edit Modal)
                editingTransaction: {
                    id: null,
                    db_id: null, // Critical for API route
                    recipient: { name: '', imageUrl: 'https://placehold.co/150x150/7c3aed/ffffff/png?text=TX' },
                    amount: null,
                    type: 'Income',
                    status: 'Completed',
                    date: new Date().toISOString().slice(0, 10),
                    description: '',
                },

                // --- C: Create (Add New Transaction) ---
                async addTransaction() {
                    if (!this.newTransaction.recipientName || !this.newTransaction.amount) {
                        alert('Please fill in Recipient/Source and Amount.');
                        return;
                    }

                    this.loading = true;

                    try {
                        // 1. Prepare data payload to match TransactionController expectations
                        const payload = {
                            recipientName: this.newTransaction.recipientName,
                            amount: this.newTransaction.amount,
                            type: this.newTransaction.type,
                            status: this.newTransaction.status,
                            date: this.newTransaction.date,
                            description: this.newTransaction.description,
                        };

                        const response = await fetch('/admin/transactions', { 
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                            body: JSON.stringify(payload)
                        });
                        
                        // Handle validation errors (422) specifically
                        if (response.status === 422) {
                            const errorData = await response.json();
                            alert("Validation Error: " + Object.values(errorData.errors).flat().join('\n'));
                            this.loading = false;
                            return;
                        }
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        
                        const newTx = await response.json(); 
                        
                        // 1. Update the local array
                        this.transactions.unshift(newTx);
                        
                        // 2. Update summary
                        this.updateSummaryTotals(null, newTx);
                        
                        // 3. Reset form and close modal
                        this.newTransaction.recipientName = '';
                        this.newTransaction.amount = null;
                        this.newTransaction.description = '';
                        this.openModal = false;
                        
                    } catch (error) {
                        console.error('Error adding transaction:', error);
                        alert('Error saving transaction. Check console for details.');
                    } finally {
                        this.loading = false;
                    }
                },

                // --- R: Read (Start Edit) ---
                startEdit(transaction) {
                    // Deep clone the transaction object to populate the edit form
                    const transactionClone = JSON.parse(JSON.stringify(transaction));
                    
                    // Convert date format from "M d, Y" (Laravel format) to "YYYY-MM-DD" for input[type=date]
                    const dateObj = new Date(transactionClone.date);
                    // Check for invalid date and ensure date is correctly formatted
                    const formattedDate = isNaN(dateObj.getTime()) ? transactionClone.date : dateObj.toISOString().slice(0, 10);

                    this.editingTransaction = {
                        ...transactionClone,
                        recipient: { name: transactionClone.recipient.name, imageUrl: transactionClone.recipient.imageUrl },
                        date: formattedDate // Use YYYY-MM-DD format
                    };
                    this.openEditModal = true;
                },

                // --- U: Update (Save Edited Transaction) ---
                async updateTransaction() {
                    if (!this.editingTransaction.recipient.name || !this.editingTransaction.amount) {
                        alert('Please fill in Recipient/Source and Amount.');
                        return;
                    }

                    this.loading = true;

                    const index = this.transactions.findIndex(t => t.id === this.editingTransaction.id);
                    if (index === -1) {
                         this.loading = false;
                         alert('Transaction not found in list.');
                         return;
                    }
                    
                    const dbId = this.editingTransaction.db_id;

                    try {
                        const response = await fetch(`/admin/transactions/${dbId}`, { 
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-HTTP-Method-Override': 'PUT' },
                            body: JSON.stringify({
                                // Send data in the format the Laravel Controller expects
                                recipientName: this.editingTransaction.recipient.name,
                                amount: this.editingTransaction.amount,
                                type: this.editingTransaction.type,
                                status: this.editingTransaction.status,
                                date: this.editingTransaction.date,
                                description: this.editingTransaction.description,
                            })
                        });

                        // Handle validation errors (422)
                        if (response.status === 422) {
                            const errorData = await response.json();
                            alert("Validation Error: " + Object.values(errorData.errors).flat().join('\n'));
                            this.loading = false;
                            return;
                        }
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        
                        const updatedTx = await response.json();

                        // 1. Update summary using the old values returned by the controller
                        this.updateSummaryTotals(updatedTx, updatedTx);

                        // 2. Update the transaction in the array
                        this.transactions[index] = updatedTx;
                        this.transactions = [...this.transactions]; // Force reactivity
                        this.openEditModal = false;

                    } catch (error) {
                        console.error('Error updating transaction:', error);
                        alert('Error updating transaction. Check console for details.');
                    } finally {
                        this.loading = false;
                    }
                },

                // --- D: Delete (Remove Transaction) ---
                async deleteTransaction(id) {
                    if (!confirm('Are you sure you want to delete this transaction? This action cannot be undone.')) {
                        return;
                    }

                    this.loading = true;

                    const index = this.transactions.findIndex(t => t.id === id);
                    if (index === -1) {
                         this.loading = false;
                         return;
                    }
                    
                    const dbId = this.transactions[index].db_id;
                    
                    try {
                         const response = await fetch(`/admin/transactions/${dbId}`, { 
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken }
                        });
                        
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        
                        const deletedData = await response.json(); 
                        
                        // 1. Update summary: Subtract the deleted transaction using the data returned by server
                        const mockOldTx = {
                            amount: deletedData.old_amount,
                            type: deletedData.old_type,
                            status: deletedData.old_status
                        };
                        this.updateSummaryTotals(mockOldTx, null); 

                        // 2. Remove the transaction from the array
                        this.transactions.splice(index, 1);
                        this.transactions = [...this.transactions]; // Force reactivity

                    } catch (error) {
                        console.error('Error deleting transaction:', error);
                        alert('Error deleting transaction. Check console for details.');
                    } finally {
                        this.loading = false;
                    }
                },

                // --- Helper for Summary Update (Handles Create, Update, Delete of Completed Txs) ---
                updateSummaryTotals(oldTx, newTx) {
                    // Normalize oldTx structure for easy access (using updatedTx.old_amount/type/status)
                    const oldAmount = parseFloat(oldTx?.old_amount || 0);
                    const oldType = oldTx?.old_type;
                    const oldStatus = oldTx?.old_status;
                    
                    // Normalize newTx structure for easy access
                    const newAmount = parseFloat(newTx?.amount || 0);
                    const newType = newTx?.type;
                    const newStatus = newTx?.status;
                    
                    // 1. Reverse the effect of the OLD transaction (for Update and Delete)
                    if (oldTx && oldStatus === 'Completed') {
                        if (oldType === 'Income') {
                            this.totalIncome = Math.max(0, this.totalIncome - oldAmount); // Ensure total doesn't go below zero
                        } else {
                            this.totalExpenses = Math.max(0, this.totalExpenses - oldAmount);
                        }
                    }
                    
                    // 2. Apply the effect of the NEW transaction (for Create and Update)
                    if (newTx && newStatus === 'Completed') {
                        if (newType === 'Income') {
                            this.totalIncome += newAmount;
                        } else {
                            this.totalExpenses += newAmount;
                        }
                    }
                },

                // --- COMPUTED PROPERTIES (Filtering and Pagination) ---
                get filteredTransactions() {
                    const currentSearch = this.search.toLowerCase();
                    const currentFilterType = this.filterType;
                    const currentFilterStatus = this.filterStatus;

                    // Reset page to 1 if filters/search change
                    if (this._lastSearch !== currentSearch || this._lastType !== currentFilterType || this._lastStatus !== currentFilterStatus) {
                        this.currentPage = 1;
                    }

                    this._lastSearch = currentSearch;
                    this._lastType = currentFilterType;
                    this._lastStatus = currentFilterStatus;

                    return this.transactions.filter(item => {
                        const searchMatch = item.recipient.name.toLowerCase().includes(currentSearch) ||
                            item.id.toLowerCase().includes(currentSearch);
                        const typeMatch = (currentFilterType === 'all' || item.type === currentFilterType);
                        const statusMatch = (currentFilterStatus === 'all' || item.status === currentFilterStatus);
                        return searchMatch && typeMatch && statusMatch;
                    });
                },
                get paginatedTransactions() {
                    const start = (this.currentPage - 1) * this.itemsPerPage;
                    const end = start + this.itemsPerPage;
                    return this.filteredTransactions.slice(start, end);
                },
                nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                prevPage() { if (this.currentPage > 1) this.currentPage--; },
                get totalPages() { return Math.ceil(this.filteredTransactions.length / this.itemsPerPage); },
                get pageStart() { if (this.filteredTransactions.length === 0) return 0; return (this.currentPage - 1) * this.itemsPerPage + 1; },
                get pageEnd() { return Math.min(this.currentPage * this.itemsPerPage, this.filteredTransactions.length); },
                get netBalance() {
                    return parseFloat(this.totalIncome) - parseFloat(this.totalExpenses);
                },
                formatCurrency(amount) {
                    // Use Pakistani Rupee (PKR) format
                    return new Intl.NumberFormat('en-PK', { 
                        style: 'currency', 
                        currency: 'PKR',
                        minimumFractionDigits: 2
                    }).format(amount || 0);
                }
            }
        }
    </script>
@endpush