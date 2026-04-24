@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
    <div class="bg-white rounded-lg shadow p-10 text-center">
        <h1 class="text-4xl font-bold text-indigo-700 mb-4">Bienvenue sur FormaPro</h1>
        <p class="text-lg text-gray-600 mb-8">
            Notre plateforme vous propose une sélection de formations techniques
            pour développer vos compétences en développement web, DevOps et plus encore.
        </p>
        <a href="{{ route('formations.index') }}"
           class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-lg transition">
            Voir les formations
        </a>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mt-10">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-indigo-600 mb-2">Formations pratiques</h3>
            <p class="text-gray-600 text-sm">Des contenus orientés projet pour apprendre en faisant.</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-indigo-600 mb-2">Tous niveaux</h3>
            <p class="text-gray-600 text-sm">Du débutant à l'expert, chacun trouve sa formation.</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-indigo-600 mb-2">Technologies modernes</h3>
            <p class="text-gray-600 text-sm">Laravel, Docker, Kubernetes, CI/CD, et bien plus.</p>
        </div>
    </div>
@endsection
