@extends('layouts.admin')

@section('title', 'Edit Siswa')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Data Siswa</h1>
        <p class="text-gray-600">Update informasi siswa</p>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('admin.students.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">NISN</label>
                    <input type="text" 
                           name="nisn" 
                           value="{{ old('nisn', $user->nisn) }}" 
                           maxlength="10"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('nisn')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $user->name) }}" 
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                    <select name="class" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @foreach($classes as $class)
                            <option value="{{ $class }}" {{ $user->class == $class ? 'selected' : '' }}>
                                {{ $class }}
                            </option>
                        @endforeach
                    </select>
                    @error('class')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">No. HP</label>
                    <input type="tel" 
                           name="phone" 
                           value="{{ old('phone', $user->phone) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex space-x-3">
                    <a href="{{ route('admin.students') }}" 
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white text-center font-semibold py-3 px-6 rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>

                    <button type="submit" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection