@extends('layouts.app')

@section('title', 'Modifier une formation')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('formations.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                &larr; Retour aux formations
            </a>
        </div>

        <h1 class="text-3xl font-bold text-gray-800 mb-8">Modifier la formation</h1>

        <form method="POST" action="{{ route('formations.update', $formation) }}"
              class="bg-white rounded-lg shadow p-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="titre" class="block text-sm font-semibold text-gray-700 mb-1">
                    Titre <span class="text-red-500">*</span>
                </label>
                <input type="text" id="titre" name="titre" value="{{ old('titre', $formation->titre) }}"
                       class="w-full border @error('titre') border-red-500 @else border-gray-300 @enderror
                              rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('titre')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4"
                          class="w-full border @error('description') border-red-500 @else border-gray-300 @enderror
                                 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $formation->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="duree" class="block text-sm font-semibold text-gray-700 mb-1">
                        Durée <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="duree" name="duree" value="{{ old('duree', $formation->duree) }}"
                           placeholder="ex: 20h"
                           class="w-full border @error('duree') border-red-500 @else border-gray-300 @enderror
                                  rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('duree')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="niveau" class="block text-sm font-semibold text-gray-700 mb-1">
                        Niveau <span class="text-red-500">*</span>
                    </label>
                    <select id="niveau" name="niveau"
                            class="w-full border @error('niveau') border-red-500 @else border-gray-300 @enderror
                                   rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($niveaux as $niveau)
                            <option value="{{ $niveau }}"
                                {{ old('niveau', $formation->niveau) === $niveau ? 'selected' : '' }}>
                                {{ $niveau }}
                            </option>
                        @endforeach
                    </select>
                    @error('niveau')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('formations.index') }}"
                   class="px-6 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit"
                        class="px-6 py-2 rounded-lg bg-indigo-600 text-white font-semibold hover:bg-indigo-700">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
@endsection
