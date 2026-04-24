<?php

namespace Database\Seeders;

use App\Models\Formation;
use Illuminate\Database\Seeder;

class FormationSeeder extends Seeder
{
    public function run(): void
    {
        $formations = [
            [
                'titre' => 'Introduction au DevOps',
                'description' => 'Découvrez les principes fondamentaux du DevOps : culture, automatisation, mesure et partage.',
                'duree' => '20h',
                'niveau' => 'Débutant',
            ],
            [
                'titre' => 'Développement Web avec Laravel',
                'description' => 'Apprenez à construire des applications web modernes avec le framework PHP Laravel.',
                'duree' => '40h',
                'niveau' => 'Intermédiaire',
            ],
            [
                'titre' => 'Conteneurisation avec Docker',
                'description' => 'Maîtrisez Docker, Docker Compose et les bonnes pratiques de conteneurisation.',
                'duree' => '25h',
                'niveau' => 'Intermédiaire',
            ],
            [
                'titre' => 'CI/CD avec GitHub Actions',
                'description' => 'Automatisez le build, les tests et le déploiement de vos applications avec GitHub Actions.',
                'duree' => '15h',
                'niveau' => 'Intermédiaire',
            ],
            [
                'titre' => 'Python pour le Web avec Flask',
                'description' => 'Créez des APIs et applications web légères avec le micro-framework Flask.',
                'duree' => '30h',
                'niveau' => 'Débutant',
            ],
            [
                'titre' => 'Orchestration avec Kubernetes',
                'description' => 'Déployez et gérez des applications conteneurisées à grande échelle avec Kubernetes.',
                'duree' => '35h',
                'niveau' => 'Avancé',
            ],
        ];

        foreach ($formations as $formation) {
            Formation::updateOrCreate(
                ['titre' => $formation['titre']],
                $formation
            );
        }
    }
}
