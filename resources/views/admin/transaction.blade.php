@extends('layouts.admin')

@section('title', 'Admin - Transactions')
@push('styles')
{{-- This component uses icons from Heroicons (MIT License), included as SVGs. --}}
<style>
    /* A little extra style for the action menu transition */
    .actions-menu {
        transition: all 0.2s ease-in-out;
    }
</style>
@endpush

@section('content')

<div class="p-4 sm:p-6 lg:p-8" x-data="transactionsApp()">

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3 mb-8">
        <div class="flex items-center p-5 bg-white shadow-sm rounded-lg">
            <div class="flex-shrink-0 mr-4">
                <div class="w-12 h-12 flex items-center justify-center bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-6-6h12" />
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-500 uppercase">Total Income</div>
                <div class="text-2xl font-bold text-gray-900" x-text="formatCurrency(totalIncome)"></div>
            </div>
        </div>
        <div class="flex items-center p-5 bg-white shadow-sm rounded-lg">
            <div class="flex-shrink-0 mr-4">
                <div class="w-12 h-12 flex items-center justify-center bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6" />
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-500 uppercase">Total Expenses</div>
                <div class="text-2xl font-bold text-gray-900" x-text="formatCurrency(totalExpenses)"></div>
            </div>
        </div>
        <div class="flex items-center p-5 bg-white shadow-sm rounded-lg">
            <div class="flex-shrink-0 mr-4">
                <div class="w-12 h-12 flex items-center justify-center bg-indigo-100 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414-.336.75-.75.75h-.75m0-1.5S21 4.5 21 6v9m0 0a.75.75 0 01-.75.75H3.75m16.5 0c.621 0 1.125-.504 1.125-1.125V6a1.125 1.125 0 00-1.125-1.125H3.75A1.125 1.125 0 002.625 6v9.75c0 .621.504 1.125 1.125 1.125h16.5z" />
                    </svg>
                </div>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-500 uppercase">Net Balance</div>
                <div class="text-2xl font-bold" :class="netBalance >= 0 ? 'text-gray-900' : 'text-red-600'" x-text="formatCurrency(netBalance)"></div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-lg rounded-lg">
        <div class="px-6 py-5 border-b border-gray-200">
             <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex-auto">
                    <h2 class="text-xl font-semibold text-slate-800">All Transactions</h2>
                    <p class="text-sm text-slate-600">Manage, search, and filter your transactions.</p>
                </div>
                <div class="flex-none">
                    <button @click="openModal = true" type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                        <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" /></svg>
                        Add Transaction
                    </button>
                </div>
            </div>
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <div class="relative"><div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg></div><input type="text" x-model.debounce.300ms="search" placeholder="Search by recipient..." class="block w-full rounded-md border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>
                </div>
                <div><select x-model="filterType" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><option value="all">All Types</option><option value="Income">Income</option><option value="Expense">Expense</option></select></div>
                <div><select x-model="filterStatus" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><option value="all">All Statuses</option><option value="Completed">Completed</option><option value="Pending">Pending</option><option value="Failed">Failed</option></select></div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Recipient/Source</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <template x-for="transaction in paginatedTransactions" :key="transaction.id">
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6"><div class="flex items-center"><div class="h-10 w-10 flex-shrink-0"><img class="h-10 w-10 rounded-full" :src="transaction.recipient.imageUrl" alt=""></div><div class="ml-4"><div class="font-medium text-gray-900" x-text="transaction.recipient.name"></div><div class="text-gray-500" x-text="transaction.id"></div></div></div></td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm"><span :class="transaction.type === 'Income' ? 'text-green-600' : 'text-red-600'" class="font-bold" x-text="(transaction.type === 'Income' ? '+ ' : '- ') + formatCurrency(transaction.amount)"></span></td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" :class="{'bg-green-100 text-green-800': transaction.status === 'Completed', 'bg-yellow-100 text-yellow-800': transaction.status === 'Pending', 'bg-red-100 text-red-800': transaction.status === 'Failed' }" x-text="transaction.status"></span></td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500" x-text="transaction.date"></td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <div x-data="{ open: false }" class="relative inline-block text-left">
                                    <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"><svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" /></svg></button>
                                    <div x-show="open" x-transition class="actions-menu origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10" style="display:none;"><div class="py-1"><a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View Details</a><a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit</a><a href="#" class="block px-4 py-2 text-sm text-red-700 hover:bg-red-50">Delete</a></div></div>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="paginatedTransactions.length === 0"><td colspan="5" class="text-center py-10 px-4 text-gray-500"><svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg><h3 class="mt-2 text-sm font-medium text-gray-900">No transactions found</h3><p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p></td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-700">Showing <span x-text="pageStart" class="font-medium"></span> to <span x-text="pageEnd" class="font-medium"></span> of <span x-text="filteredTransactions.length" class="font-medium"></span> results</p>
                <div class="flex-1 flex justify-end gap-2"><button @click="prevPage" :disabled="currentPage === 1" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Previous</button><button @click="nextPage" :disabled="currentPage === totalPages" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Next</button></div>
            </div>
        </div>
    </div>

    <div x-show="openModal" @keydown.window.escape="openModal = false" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="openModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div @click.outside="openModal = false" x-show="openModal" x-transition class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <form @submit.prevent="addTransaction()">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10"><svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg></div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left"><h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">New Transaction</h3><p class="mt-1 text-sm text-gray-500">Fill in the details to add a new transaction.</p></div>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-6">
                                <div class="sm:col-span-2">
                                    <label for="recipient" class="block text-sm font-medium leading-6 text-gray-900">Recipient</label>
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.41-1.412A6.972 6.972 0 0010 11.5a6.972 6.972 0 00-6.535 2.993z" /></svg></div>
                                        <input type="text" x-model="newTransaction.recipientName" id="recipient" placeholder="John Doe" required class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>

                                <div>
                                    <label for="amount" class="block text-sm font-medium leading-6 text-gray-900">Amount</label>
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><span class="text-gray-500 sm:text-sm">$</span></div>
                                        <input type="number" x-model.number="newTransaction.amount" id="amount" step="0.01" placeholder="0.00" required class="block w-full rounded-md border-0 py-1.5 pl-7 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3"><span class="text-gray-500 sm:text-sm">USD</span></div>
                                    </div>
                                </div>

                                <div>
                                    <label for="date" class="block text-sm font-medium leading-6 text-gray-900">Date</label>
                                    <div class="relative mt-2">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"><svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zM4.5 8.5A.75.75 0 005.25 9.25h9.5a.75.75 0 000-1.5h-9.5A.75.75 0 004.5 8.5z" clip-rule="evenodd" /></svg></div>
                                        <input type="date" x-model="newTransaction.date" id="date" required class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    </div>
                                </div>

                                <div>
                                    <label for="type" class="block text-sm font-medium leading-6 text-gray-900">Type</label>
                                    <div class="relative mt-2">
                                        <select id="type" x-model="newTransaction.type" class="appearance-none block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"><option>Income</option><option>Expense</option></select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"><svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                                    <div class="relative mt-2">
                                        <select id="status" x-model="newTransaction.status" class="appearance-none block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"><option>Completed</option><option>Pending</option><option>Failed</option></select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2"><svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">Save Transaction</button>
                            <button @click="openModal = false" type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-base font-medium text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
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
    function transactionsApp() {
        return {
            openModal: false,
            search: '',
            filterType: 'all',
            filterStatus: 'all',
            currentPage: 1,
            itemsPerPage: 5,
            
            // NEW: Object to hold the data for the new transaction form
            newTransaction: {
                recipientName: '',
                amount: null,
                type: 'Expense',
                status: 'Completed',
                date: new Date().toISOString().slice(0, 10), // Set today's date by default
            },

            transactions: [
                { id: 'TXN-7B3D9F', recipient: { name: 'Lindsay Walton', imageUrl: 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=256&h=256' }, amount: 2500.00, type: 'Expense', status: 'Completed', date: 'Sep 15, 2025' },
                { id: 'TXN-A1C8E2', recipient: { name: 'Michael Foster', imageUrl: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=256&h=256' }, amount: 1200.00, type: 'Income', status: 'Pending', date: 'Sep 14, 2025' },
                { id: 'TXN-F4B6A1', recipient: { name: 'Courtney Henry', imageUrl: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=256&h=256' }, amount: 750.50, type: 'Expense', status: 'Completed', date: 'Sep 12, 2025' },
                { id: 'TXN-9C8D7E', recipient: { name: 'Tom Cook', imageUrl: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=256&h=256' }, amount: 3200.00, type: 'Income', status: 'Completed', date: 'Sep 11, 2025' },
                { id: 'TXN-B3A2C1', recipient: { name: 'Leonard Krasner', imageUrl: 'https://images.unsplash.com/photo-1519345182560-3f2917c472ef?w=256&h=256' }, amount: 150.00, type: 'Expense', status: 'Failed', date: 'Sep 10, 2025' },
                { id: 'TXN-E6F5D4', recipient: { name: 'Floyd Miles', imageUrl: 'https://images.unsplash.com/photo-1463453091185-61582044d556?w=256&h=256' }, amount: 5000.00, type: 'Income', status: 'Completed', date: 'Sep 9, 2025' },
                { id: 'TXN-G8H7I6', recipient: { name: 'Emily Selman', imageUrl: 'https://images.unsplash.com/photo-1502685104226-ee32379fefbe?w=256&h=256' }, amount: 95.20, type: 'Expense', status: 'Pending', date: 'Sep 8, 2025' },
            ],

            // NEW: Function to handle adding a new transaction
            addTransaction() {
                if (!this.newTransaction.recipientName || !this.newTransaction.amount) {
                    alert('Please fill out all required fields.');
                    return;
                }

                // Create the new transaction object
                const newTx = {
                    id: 'TXN-' + Math.random().toString(36).substr(2, 6).toUpperCase(),
                    recipient: {
                        name: this.newTransaction.recipientName,
                        // Using a placeholder image for new entries
                        imageUrl: `https://ui-avatars.com/api/?name=${encodeURIComponent(this.newTransaction.recipientName)}&color=7F9CF5&background=EBF4FF`
                    },
                    amount: parseFloat(this.newTransaction.amount),
                    type: this.newTransaction.type,
                    status: this.newTransaction.status,
                    // Reformat date for display
                    date: new Date(this.newTransaction.date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
                };

                // Add the new transaction to the start of the main array
                this.transactions.unshift(newTx);
                
                // Reset the form fields and close the modal
                this.newTransaction.recipientName = '';
                this.newTransaction.amount = null;
                this.openModal = false;
            },

            // --- COMPUTED PROPERTIES AND HELPERS (Unchanged) ---
            get filteredTransactions() {
                this.currentPage = 1; 
                if (this.search === '' && this.filterType === 'all' && this.filterStatus === 'all') return this.transactions;
                return this.transactions.filter(item => {
                    const searchMatch = item.recipient.name.toLowerCase().includes(this.search.toLowerCase());
                    const typeMatch = (this.filterType === 'all' || item.type === this.filterType);
                    const statusMatch = (this.filterStatus === 'all' || item.status === this.filterStatus);
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
            get totalIncome() { return this.transactions.filter(t => t.type === 'Income' && t.status === 'Completed').reduce((sum, t) => sum + t.amount, 0); },
            get totalExpenses() { return this.transactions.filter(t => t.type === 'Expense' && t.status === 'Completed').reduce((sum, t) => sum + t.amount, 0); },
            get netBalance() { return this.totalIncome - this.totalExpenses; },
            formatCurrency(amount) { return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount); }
        }
    }
</script>
@endpush