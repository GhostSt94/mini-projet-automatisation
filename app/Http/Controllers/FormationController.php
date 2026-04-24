<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FormationController extends Controller
{
    private const NIVEAUX = ['Débutant', 'Intermédiaire', 'Avancé'];

    public function home()
    {
        return view('home');
    }

    public function index()
    {
        $formations = Formation::orderBy('niveau')->orderBy('titre')->get();
        return view('formations.index', compact('formations'));
    }

    public function create()
    {
        return view('formations.create', ['niveaux' => self::NIVEAUX]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255|unique:formations,titre',
            'description' => 'required|string|min:10',
            'duree' => 'required|string|max:50',
            'niveau' => ['required', Rule::in(self::NIVEAUX)],
        ]);

        Formation::create($validated);

        return redirect()
            ->route('formations.index')
            ->with('success', 'Formation ajoutée avec succès.');
    }

    public function edit(Formation $formation)
    {
        return view('formations.edit', [
            'formation' => $formation,
            'niveaux' => self::NIVEAUX,
        ]);
    }

    public function update(Request $request, Formation $formation)
    {
        $validated = $request->validate([
            'titre' => [
                'required', 'string', 'max:255',
                Rule::unique('formations', 'titre')->ignore($formation->id),
            ],
            'description' => 'required|string|min:10',
            'duree' => 'required|string|max:50',
            'niveau' => ['required', Rule::in(self::NIVEAUX)],
        ]);

        $formation->update($validated);

        return redirect()
            ->route('formations.index')
            ->with('success', 'Formation modifiée avec succès.');
    }

    public function destroy(Formation $formation)
    {
        $formation->delete();

        return redirect()
            ->route('formations.index')
            ->with('success', 'Formation supprimée avec succès.');
    }
}
