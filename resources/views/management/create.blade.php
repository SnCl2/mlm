@extends('layout.app')

@section('title', 'Create Management User')

@section('content')
  <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Create Management User</h2>

    @if(session('success'))
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif

    <form method="POST" action="{{ route('management.store') }}" class="space-y-5">
      @csrf

      <div>
        <label class="block mb-1 font-medium text-sm text-gray-700">Name</label>
        <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm" required>
      </div>

      <div>
        <label class="block mb-1 font-medium text-sm text-gray-700">Email</label>
        <input type="email" name="email" class="w-full border-gray-300 rounded-md shadow-sm" required>
      </div>

      <div>
        <label class="block mb-1 font-medium text-sm text-gray-700">Phone</label>
        <input type="text" name="phone" class="w-full border-gray-300 rounded-md shadow-sm">
      </div>

      <div>
        <label class="block mb-1 font-medium text-sm text-gray-700">Password</label>
        <input type="password" name="password" class="w-full border-gray-300 rounded-md shadow-sm" required>
      </div>

      <div class="flex items-center space-x-2">
        <input type="checkbox" name="is_active" class="form-checkbox text-orange-500" checked>
        <label class="text-sm text-gray-700">Active?</label>
      </div>

      <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded shadow">
        Create User
      </button>
    </form>
  </div>
@endsection
