<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

#[AsCommand(
    name: 'app:export-film-stats',
    description: 'Exporte les statistiques des films dans un fichier CSV et les envoie par email',
)]
class ExportFilmStatsCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    public function __construct(EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Adresse email pour envoyer le fichier')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Chemin du fichier CSV à générer', 'var/film_stats.csv');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $outputFile = $input->getOption('output');
        $email = $input->getOption('email');

        $movies = $this->entityManager->getRepository('App\Entity\Movie')->findAll();
        $actors = $this->entityManager->getRepository('App\Entity\Actor')->findAll();
        $categories = $this->entityManager->getRepository('App\Entity\Category')->findAll();

        $handle = fopen($outputFile, 'w');
        fputcsv($handle, ['Type', 'Nombre']);

        fputcsv($handle, ['Films', count($movies)]);
        fputcsv($handle, ['Acteurs', count($actors)]);
        fputcsv($handle, ['Catégories', count($categories)]);

        foreach ($categories as $category) {
            fputcsv($handle, ["Catégorie: " . $category->getNom(), count($category->getMovies())]);
        }

        fclose($handle);

        $io->success("Fichier CSV généré : $outputFile");

        if ($email) {
            $emailMessage = (new Email())
                ->from('noreply@searchfilm.com')
                ->to($email)
                ->subject('Statistiques des films')
                ->text('Veuillez trouver en pièce jointe le fichier CSV contenant les statistiques.')
                ->attachFromPath($outputFile, 'film_stats.csv');

            $this->mailer->send($emailMessage);
            $io->success("Email envoyé avec succès à $email avec le fichier CSV en pièce jointe.");
        }

        return Command::SUCCESS;
    }
}
