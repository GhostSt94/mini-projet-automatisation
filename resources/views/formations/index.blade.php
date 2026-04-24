@extends('layouts.app')

@section('title', 'Formations')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Nos formations</h1>
        <a href="{{ route('formations.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-5 py-2 rounded-lg">
            + Ajouter une formation
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-300 text-green-800 rounded-lg p-4 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($formations->isEmpty())
        <div class="bg-yellow-100 border border-yellow-300 text-yellow-800 rounded-lg p-4">
            Aucune formation disponible pour le moment.
        </div>
    @else
        <div class="grid md:grid-cols-2 gap-6">
            @foreach($formations as $formation)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition p-6">
                    <div class="flex justify-between items-start mb-3">
                        <h2 class="text-xl font-bold text-indigo-700">{{ $formation->titre }}</h2>
                        @php
                            $color = match($formation->niveau) {
                                'Débutant' => 'bg-green-100 text-green-800',
                                'Intermédiaire' => 'bg-yellow-100 text-yellow-800',
                                'Avancé' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="{{ $color }} text-xs font-semibold px-2 py-1 rounded">
                            {{ $formation->niveau }}
                        </span>
                    </div>
                    <p class="text-gray-600 mb-4">{{ $formation->description }}</p>
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Durée : {{ $formation->duree }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('formations.edit', $formation) }}"
                               title="Modifier"
                               aria-label="Modifier"
                               class="p-2 rounded-full text-indigo-600 hover:bg-indigo-50 hover:text-indigo-800 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('formations.destroy', $formation) }}"
                                  onsubmit="return confirm('Supprimer la formation « {{ $formation->titre }} » ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        title="Supprimer"
                                        aria-label="Supprimer"
                                        class="p-2 rounded-full text-red-600 hover:bg-red-50 hover:text-red-800 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
