@extends('layout.app')

@section('title', 'Commission Levels')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-xl shadow space-y-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Commission Levels</h1>

    <!-- Add Level Form -->
    <form method="POST" action="{{ route('commission-levels.store') }}" class="grid grid-cols-3 gap-4 items-end">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Level</label>
            <input type="number" name="level" required min="1" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Percentage (%)</label>
            <input type="number" name="percentage" required step="0.000000001" min="0" max="100" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <button type="submit" class="bg-cyan-500 hover:bg-cyan-600 text-white px-4 py-2 rounded">Add Level</button>
        </div>
    </form>

    <!-- Table of Levels -->
    <table class="w-full border mt-6 text-sm">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="px-4 py-2">Level</th>
                <th class="px-4 py-2">Percentage</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($levels as $level)
                <tr class="border-b">
                    <form action="{{ route('commission-levels.update', $level->id) }}" method="POST" class="contents">
                        @csrf
                        @method('PUT')
                        <td class="px-4 py-2">{{ $level->level }}</td>
                        <td class="px-4 py-2">
                            <input type="number" name="percentage" value="{{ $level->percentage }}" step="0.00000001" class="w-24 border px-2 py-1 rounded">
                        </td>
                        <td class="px-4 py-2 text-right">
                            <button type="submit" class="text-blue-600 hover:underline mr-2">Update</button>
                    </form>
                    <form action="{{ route('commission-levels.destroy', $level->id) }}" method="POST" onsubmit="return confirm('Delete level {{ $level->level }}?')" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                    </form>
                        </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-gray-500 py-4">No levels found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
