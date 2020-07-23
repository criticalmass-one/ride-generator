<?php declare(strict_types=1);

namespace App\Command;

use App\CycleFetcher\CycleFetcherInterface;
use App\Model\CityCycle;
use App\RideGenerator\CityRideGeneratorInterface;
use App\RideGenerator\RideGeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateRidesCommand extends Command
{
    protected RideGeneratorInterface $rideGenerator;
    protected CycleFetcherInterface $cycleFetcher;

    public function __construct($name = null, CityRideGeneratorInterface $rideGenerator, CycleFetcherInterface $cycleFetcher)
    {
        $this->rideGenerator = $rideGenerator;
        $this->cycleFetcher = $cycleFetcher;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:cycles:generate-rides')
            ->setDescription('Create rides for a parameterized year and month automatically')
            ->addOption(
                'dateTime',
                null,
                InputOption::VALUE_OPTIONAL,
                'DateTime of month to generate'
            )
            ->addOption(
                'from',
                null,
                InputOption::VALUE_OPTIONAL,
                'DateTime of period to start'
            )
            ->addOption(
                'until',
                null,
                InputOption::VALUE_OPTIONAL,
                'DateTime of period to start'
            )
            ->addArgument(
                'cities',
                InputArgument::IS_ARRAY,
                'List of cities'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $dateTime = $input->getOption('dateTime') ? new \DateTime($input->getOption('dateTime')) : null;
        $fromDateTime = $input->getOption('from') ? new \DateTime($input->getOption('from')) : null;
        $untilDateTime = $input->getOption('until') ? new \DateTime($input->getOption('until')) : null;

        $citySlugList = $input->getArgument('cities');

        if ($fromDateTime && $untilDateTime) {
            $monthInterval = new \DateInterval('P1M');

            do {
                $this->rideGenerator->addDateTime($fromDateTime);

                $fromDateTime->add($monthInterval);
            } while ($fromDateTime <= $untilDateTime);
        } elseif ($dateTime) {
            $this->rideGenerator->setDateTime($dateTime);
        }

        $cycleList = $this->cycleFetcher->fetchCycles($citySlugList);

        $io->table([
            'City', 'Day of week', 'Week of month'
        ],
        array_map(function (CityCycle $cityCycle): array
        {
            return [
                $cityCycle->getCity()->getName(), $cityCycle->getDayOfWeek(), $cityCycle->getWeekOfMonth(),
            ];
        }, $cycleList));

        die;





        $this->rideGenerator->setCityList($cityList)->execute();

        $table = new Table($output);
        $table->setHeaders(['City', 'DateTime Location', 'DateTime UTC', 'Location', 'Title', 'Cycle Id']);

        $utc = new \DateTimeZone('UTC');

        $counter = 0;

        /** @var Ride $ride */
        foreach ($this->rideGenerator->getRideList() as $ride) {
            $table->addRow([
                $ride->getCity()->getCity(),
                $ride->getDateTime()->format('Y-m-d H:i'),
                $ride->getDateTime()->setTimezone($utc)->format('Y-m-d H:i'),
                $ride->getLocation(),
                $ride->getTitle(),
                $ride->getCycle()->getId(),
            ]);

            $manager->persist($ride);

            ++$counter;
        }

        $table->render();

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Save all created rides?', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $manager->flush();

        $output->writeln(sprintf('Saved %d rides', $counter));
    }

}
