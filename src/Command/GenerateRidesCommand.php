<?php declare(strict_types=1);

namespace App\Command;

use App\CycleFetcher\CycleFetcherInterface;
use App\Model\CityCycle;
use App\Model\Ride;
use App\RideGenerator\CityRideGeneratorInterface;
use App\RideGenerator\CycleRideGeneratorInterface;
use App\RideGenerator\RideGeneratorInterface;
use App\RidePusher\RidePusherInterface;
use Carbon\Carbon;
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
    protected RidePusherInterface $ridePusher;

    public function __construct($name = null, CycleRideGeneratorInterface $rideGenerator, CycleFetcherInterface $cycleFetcher, RidePusherInterface $ridePusher)
    {
        $this->rideGenerator = $rideGenerator;
        $this->cycleFetcher = $cycleFetcher;
        $this->ridePusher = $ridePusher;

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

        $io->success(sprintf('Fetched %d cycles', count($cycleList)));

        $io->table([
            'City', 'Day of week', 'Week of month'
        ],
        array_map(function (CityCycle $cityCycle): array
        {
            return [
                $cityCycle->getCity()->getName(), $cityCycle->getDayOfWeek(), $cityCycle->getWeekOfMonth(),
            ];
        }, $cycleList));

        $rideList = $this
            ->rideGenerator
            ->setDateTime(new Carbon())
            ->setCycleList($cycleList)
            ->execute()
            ->getRideList();

        $io->success(sprintf('Generated %d rides', count($rideList)));

        $io->table([
            'City', 'Date Time', 'Location',
        ], array_map(function (Ride $ride): array
        {
            return [
                $ride->getCity()->getName(), $ride->getDateTime()->format('Y-m-d H:i:s'), $ride->getLocation()
            ];
        }, $rideList));

        $successCounter = $this->ridePusher->pushRides($rideList);

        if (0 < $successCounter) {
            $io->success(sprintf('Pushed %d rides to critical mass api', $successCounter));
        }

        if ($successCounter < count($rideList)) {
            $io->error(sprintf('Failed to push %d rides to api', count($rideList) - $successCounter));
        }

        return Command::SUCCESS;
    }
}
