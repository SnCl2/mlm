@extends('layout.app')

@section('title', 'Manage Products')

@section('content')
<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Products</h1>

    {{-- Add / Edit Form --}}
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">{{ isset($editProduct) ? 'Edit Product' : 'Add New Product' }}</h2>

        <form 
            action="{{ isset($editProduct) ? route('products.update', $editProduct->id) : route('products.store') }}" 
            method="POST" 
            enctype="multipart/form-data"
            class="space-y-4"
        >
            @csrf
            @if(isset($editProduct))
                @method('PUT')
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700">Product Name</label>
                <input type="text" name="name" value="{{ old('name', $editProduct->name ?? '') }}" 
                    class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-orange-500 focus:border-orange-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Image</label>
                <input type="file" name="image" class="mt-1 block w-full text-sm text-gray-600">
                @if(isset($editProduct) && $editProduct->image)
                    <img src="{{ asset('storage/'.$editProduct->image) }}" alt="Product Image" class="h-20 mt-2 rounded">
                @endif
            </div>

            <button type="submit" 
                class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">
                {{ isset($editProduct) ? 'Update' : 'Add' }} Product
            </button>
        </form>
    </div>

    {{-- Product List --}}
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">All Products</h2>

        @if($products->count())
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="p-3 border-b">#</th>
                        <th class="p-3 border-b">Name</th>
                        <th class="p-3 border-b">Image</th>
                        <th class="p-3 border-b text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $index => $product)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3 border-b">{{ $index + 1 }}</td>
                            <td class="p-3 border-b">{{ $product->name }}</td>
                            <td class="p-3 border-b">
                                @if($product->image)
                                    <img src="{{ asset('public/storage/'.$product->image) }}" alt="Product" class="h-12 rounded">
                                @else
                                    <span class="text-gray-400">No Image</span>
                                @endif
                            </td>
                            <td class="p-3 border-b text-center space-x-2">
                                {{-- Edit --}}
                                <a href="{{ route('products.index', ['edit' => $product->id]) }}" 
                                    class="text-blue-500 hover:underline">
                                    Edit
                                </a>

                                {{-- Delete --}}
                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-500">No products found.</p>
        @endif
    </div>
</div>
@endsection
