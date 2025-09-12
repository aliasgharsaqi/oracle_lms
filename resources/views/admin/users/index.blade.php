@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'All Users')

@section('content')
<!-- Active Users -->
<div class="grid grid-cols-1">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <!-- Card Header -->
        <div class="custom-card-header flex flex-wrap justify-between items-center gap-2 px-4 py-3 border-b">
            <h4 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="bi bi-people-fill"></i> Active Users
            </h4>
            @can('create', App\Models\User::class)
            <a href="{{ route('users.create') }}"
                class="bts hover-card btn btn-gradient-primary inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white">
                <i class="bi bi-plus-circle"></i> Add User
            </a>
            @endcan
        </div>

        <!-- Card Body -->
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 text-nowrap">
                        <tr>
                            <th class="px-4 py-3"><i class="bi bi-person"></i> Name</th>
                            <th class="px-4 py-3"><i class="bi bi-envelope"></i> Email</th>
                            <th class="px-4 py-3"><i class="bi bi-shield-lock"></i> Role</th>
                            <th class="px-4 py-3"><i class="bi bi-check2-circle"></i> Status</th>
                            <th class="px-4 py-3"><i class="bi bi-calendar3"></i> Joined</th>
                            <th class="px-4 py-3 text-center"><i class="bi bi-gear"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold max-w-[160px] truncate">{{ $user->name }}</td>
                            <td class="px-4 py-3 max-w-[200px] truncate">{{ $user->email }}</td>
                            <td class="px-4 py-3">
                                @if($user->roles->isNotEmpty())
                                @foreach($user->roles as $role)
                                <span class="badge badge-gradient-primary">{{ $role->name }}</span>
                                @endforeach
                                @else
                                <span class="badge badge-light">No Role</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($user->status == 1)
                                <span class="badge badge-gradient-success">Active</span>
                                @elseif($user->status == 2)
                                <span class="badge badge-gradient-warning">Pending</span>
                                @else
                                <span class="badge badge-gradient-danger">Suspended</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex flex-wrap justify-center gap-2">
                                    <a href="{{ route('users.show', $user->id) }}"
                                        class="btn btn-icon badge-gradient-warning">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('update', $user)
                                    <a href="{{ route('users.edit', $user->id) }}"
                                        class="btn btn-icon badge-gradient-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    @endcan
                                    @can('delete', $user)
                                    <button type="button" class="btn btn-icon badge-gradient-danger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $user->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-500 py-6">
                                <i class="bi bi-emoji-frown text-lg"></i> No active users found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Trashed Users -->
<div class="grid grid-cols-1 mt-6">
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
        <!-- Card Header -->
        <div class="px-4 py-3 border-b">
            <h4 class="text-lg font-bold text-red-600 flex items-center gap-2">
                <i class="bi bi-trash-fill"></i> Trashed Users
            </h4>
        </div>

        <!-- Card Body -->
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm text-left">
                    <thead class="bg-gray-100 text-gray-700 text-nowrap">
                        <tr>
                            <th class="px-4 py-3">Name</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Deleted On</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($trashedUsers as $user)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold">{{ $user->name }}</td>
                            <td class="px-4 py-3 max-w-[200px] truncate">{{ $user->email }}</td>
                            <td class="px-4 py-3">{{ $user->deleted_at->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-center">
                                <form action="{{ route('users.restore', $user->id) }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm btn-gradient-success flex items-center gap-1">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restore
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-6">
                                <i class="bi bi-inbox"></i> No trashed users found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection