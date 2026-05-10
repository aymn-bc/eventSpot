<?php

namespace App\Command;

use App\Repository\EvenementRepository;
use App\Repository\InscriptionRepository;
use App\Repository\LieuRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:eventspot:report',
    description: 'Génère un rapport sur les événements et inscriptions',
)]
class EventSpotReportCommand extends Command
{
    public function __construct(
        private EvenementRepository $eventRepo,
        private InscriptionRepository $inscRepo,
        private LieuRepository $lieuRepo
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('upcoming', null, InputOption::VALUE_NONE, 'Afficher uniquement les événements à venir')
            ->addOption('lieu', null, InputOption::VALUE_OPTIONAL, 'Filtrer par nom de lieu');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('📊 Rapport EventSpot');

        $evenements = $this->eventRepo->findAll();

        // Filtre upcoming
        if ($input->getOption('upcoming')) {
            $now = new \DateTime();
            $evenements = array_filter($evenements, fn($e) => $e->getDateDebut() > $now);
            $io->note('Filtre : événements à venir uniquement');
        }

        // Filtre lieu
        if ($lieu = $input->getOption('lieu')) {
            $evenements = array_filter($evenements, fn($e) => 
                $e->getLieuEvent() && str_contains($e->getLieuEvent()->getNom(), $lieu)
            );
            $io->note('Filtre : lieu "' . $lieu . '"');
        }

        $evenements = array_values($evenements);

        // Événements par statut
        $io->section('📌 Événements par statut');
        $statuts = [];
        foreach ($evenements as $e) {
            $statut = $e->getStatus()?->value ?? 'inconnu';
            $statuts[$statut] = ($statuts[$statut] ?? 0) + 1;
        }
        $statutRows = [];
        foreach ($statuts as $statut => $count) {
            $statutRows[] = [$statut, $count];
        }
        $io->table(['Statut', 'Nombre'], $statutRows);

        // Inscriptions par statut
        $io->section('📝 Inscriptions par statut');
        $inscriptions = $this->inscRepo->findAll();
        $inscStatuts = [];
        foreach ($inscriptions as $i) {
            $statut = $i->getStatus()?->value ?? 'inconnu';
            $inscStatuts[$statut] = ($inscStatuts[$statut] ?? 0) + 1;
        }
        $inscRows = [];
        foreach ($inscStatuts as $statut => $count) {
            $inscRows[] = [$statut, $count];
        }
        $io->table(['Statut', 'Nombre'], $inscRows);

        // Taux de remplissage moyen
        $io->section('📈 Taux de remplissage moyen');
        $totalTaux = 0;
        foreach ($evenements as $e) {
            $nb = $this->inscRepo->count(['evenement' => $e]);
            $totalTaux += $e->getCapaciteMax() > 0 ? ($nb / $e->getCapaciteMax()) * 100 : 0;
        }
        $moyenneTaux = count($evenements) > 0 ? round($totalTaux / count($evenements), 2) : 0;
        $io->note('Taux de remplissage moyen : ' . $moyenneTaux . '%');

        // Répartition par catégorie
        $io->section('🗂 Répartition par catégorie');
        $categories = [];
        foreach ($evenements as $e) {
            $cat = $e->getCategorie()?->value ?? 'inconnu';
            $categories[$cat] = ($categories[$cat] ?? 0) + 1;
        }
        $catRows = [];
        foreach ($categories as $cat => $count) {
            $catRows[] = [$cat, $count];
        }
        $io->table(['Catégorie', 'Nombre'], $catRows);

        // Top 3 événements
        $io->section('🏆 Top 3 des événements les plus populaires');
        $popularity = [];
        foreach ($evenements as $e) {
            $nb = $this->inscRepo->count(['evenement' => $e]);
            $popularity[] = ['evenement' => $e, 'nb' => $nb];
        }
        usort($popularity, fn($a, $b) => $b['nb'] - $a['nb']);
        $top3 = array_slice($popularity, 0, 3);
        $topRows = [];
        foreach ($top3 as $item) {
            $topRows[] = [$item['evenement']->getTitre(), $item['nb']];
        }
        $io->table(['Événement', 'Inscrits'], $topRows);

        // Revenu total estimé
        $io->section('💰 Revenu total estimé');
        $revenu = 0;
        foreach ($inscriptions as $i) {
            if ($i->getStatus()?->value === 'confirmee' && $i->getEvenement()) {
                $revenu += $i->getEvenement()->getPrix();
            }
        }
        $io->success('Revenu total estimé : ' . number_format($revenu, 2, ',', ' ') . ' €');

        return Command::SUCCESS;
    }
}