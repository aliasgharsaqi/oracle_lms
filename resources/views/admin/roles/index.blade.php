@extends('layouts.admin')

@section('title', 'Roles')
@section('page-title', 'Roles Management')

@section('content')
<!-- Roles -->
<div class="grid grid-cols-1">
  <div class="bg-white shadow-lg rounded-2xl overflow-hidden">
    <!-- Card Header -->
    <div class="custom-card-header flex flex-wrap justify-between items-center gap-2 px-4 py-3 border-b">
      <h4 class="text-lg font-bold text-white flex items-center gap-2">
        <i class="bi bi-shield-lock-fill"></i> Roles
      </h4>
      @can('Add Role')
      <a href="{{ route('admin.roles.create') }}"
         class="btn btn-gradient-primary inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white">
        <i class="bi bi-plus-circle"></i> Add Role
      </a>
      @endcan
    </div>

    <!-- Card Body -->
    <div class="p-0">
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto text-sm text-left">
          <thead class="bg-gray-100 text-gray-700 text-nowrap">
            <tr>
              <th class="px-4 py-3">Name</th>
              <th class="px-4 py-3">Permissions</th>
              <th class="px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($roles as $role)
            <tr class="border-b hover:bg-gray-50">
              <td class="px-4 py-3 font-semibold">{{ $role->name }}</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap gap-1">
                  @foreach($role->permissions as $permission)
                  <span class="badge bg-info text-dark">{{ $permission->name }}</span>
                  @endforeach
                </div>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex flex-wrap justify-center gap-2">
                  @can('Edit Role')
                  <a href="{{ route('admin.roles.edit', $role->id) }}"
                     class="btn btn-icon badge-gradient-primary">
                    <i class="bi bi-pencil-square"></i>
                  </a>
                  @endcan

                  @can('Delete Role')
                  <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="inline-block">
                    @csrf @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Are you sure you want to delete this role?')"
                            class="btn btn-icon badge-gradient-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                  @endcan
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="p-4">
        {{ $roles->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>
</div>

@endsection