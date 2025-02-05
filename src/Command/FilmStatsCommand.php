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
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;


#[AsCommand(
    name: 'app:film-stats',
    description: 'Retourne les statistiques sur les films, acteurs et catégories.',
)]

class FilmStatsCommand extends Command
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
            ->addArgument('type', InputArgument::REQUIRED, 'Type de données demandées (films, acteurs, categories)')
            ->addOption('mon-param', null, InputOption::VALUE_NONE, 'Une option facultative')
            ->addOption('log-file', null, InputOption::VALUE_REQUIRED, 'Chemin vers le fichier de log')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Adresse email pour envoyer les résultats');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');
        $logFile = $input->getOption('log-file');
        $email = $input->getOption('email');
        $data = '';

        switch ($type) {
            case 'films':
                $nbFilms = $this->entityManager->getRepository('App\Entity\Movie')->count([]);
                $data = "Nombre de films : $nbFilms";
                break;

            case 'acteurs':
                $nbActeurs = $this->entityManager->getRepository('App\Entity\Actor')->count([]);
                $data = "Nombre d'acteurs : $nbActeurs";
                break;

            case 'categories':
                $categories = $this->entityManager->getRepository('App\Entity\Category')->findAll();
                $data = "Nombre de catégories : " . count($categories) . "\n";
                foreach ($categories as $categorie) {
                    $nbFilms = count($categorie->getMovies());
                    $data .= "- {$categorie->getNom()} : $nbFilms films\n";
                }
                break;

            default:
                $io->error("Type inconnu. Utilise 'films', 'acteurs' ou 'categories'.");
                return Command::FAILURE;
        }

        $io->success($data);

        if ($input->getOption('mon-param')) {
            $output->writeln("L'option --mon-param a été activée !");
        }

        if ($logFile) {
            file_put_contents($logFile, $data . "\n", FILE_APPEND);
            $io->success("Résultat écrit dans le fichier : $logFile");
        }

        if ($email) {
            $emailMessage = (new Email())
                ->from('noreply@searchfilm.com')
                ->to($email)
                ->subject('Statistiques des films')
                ->text($data);

            $this->mailer->send($emailMessage);
            $io->success("Email envoyé à $email");
        }

        return Command::SUCCESS;
    }
}
