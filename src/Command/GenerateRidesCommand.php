<?php declare(strict_types=1);

namespace App\Command;

use App\Api\RideApiInterface;
use App\CycleFetcher\CycleFetcherInterface;
use App\Logger\Logger;
use App\Model\Api\ApiResultInterface;
use App\Model\Api\ErrorResult;
use App\Model\CityCycle;
use App\Model\Ride;
use App\RideGenerator\CycleRideGeneratorInterface;
use App\RidePusher\RidePusherInterface;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateRidesCommand extends Command
{
    protected static $defaultDescription = 'Create rides for a parameterized year and month automatically';

    public function __construct(protected CycleRideGeneratorInterface $rideGenerator, protected CycleFetcherInterface $cycleFetcher, protected RidePusherInterface $ridePusher, protected RideApiInterface $rideApi)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:cycles:generate-rides')
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
            ->addOption(
                'strip-duplicates',
                null,
                InputOption::VALUE_NONE,
                'Do not create duplicates'
            )
            ->addArgument(
                'cities',
                InputArgument::IS_ARRAY,
                'List of cities'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rideList = null;
        $io = new SymfonyStyle($input, $output);
        Logger::setIo($io);

        $dateTime = $input->getOption('dateTime') ? new Carbon($input->getOption('dateTime')) : null;
        $fromDateTime = $input->getOption('from') ? new Carbon($input->getOption('from')) : null;
        $untilDateTime = $input->getOption('until') ? new Carbon($input->getOption('until')) : null;

        $citySlugList = $input->getArgument('cities');
        $existingRideList = [];

        if ($fromDateTime && $untilDateTime) {
            $monthInterval = new \DateInterval('P1M');

            do {
                $this->rideGenerator->addDateTime($fromDateTime);
                $existingRideList = array_merge($rideList, $this->rideApi->getRideListInMonth($fromDateTime));

                $fromDateTime->add($monthInterval);
            } while ($fromDateTime <= $untilDateTime);
        } elseif ($dateTime) {
            $this->rideGenerator->setDateTime($dateTime);
            $existingRideList = $this->rideApi->getRideListInMonth($dateTime);
        } else {
            $this->rideGenerator->setDateTime(new Carbon());
            $existingRideList = $this->rideApi->getRideListInMonth(new Carbon());
        }

        $cycleList = $this->cycleFetcher->fetchCycles($citySlugList);

        $io->success(sprintf('Fetched %d cycles', count($cycleList)));

        $this->printCycleList($io, $cycleList);

        if ('y' !== $io->ask(sprintf('Should I proceed and generate rides for these %d cycles? [Y/n]', count($cycleList)), 'n')) {
            return Command::SUCCESS;
        }

        $rideList = $this
            ->rideGenerator
            ->setCycleList($cycleList)
            ->execute()
            ->getRideList();

        $io->success(sprintf('Generated %d rides', is_countable($rideList) ? count($rideList) : 0));

        $this->printRideList($io, $rideList);

        if ($input->getOption('strip-duplicates')) {
            $rideList = $this->stripDuplicateRides($rideList, $existingRideList, $io);
        }

        $io->success(sprintf('There are %d rides left', is_countable($rideList) ? count($rideList) : 0));

        $this->printRideList($io, $rideList);

        if ('y' !== $io->ask(sprintf('Should I proceed and push these %d rides to critical mass? [Y/n]', is_countable($rideList) ? count($rideList) : 0), 'n')) {
            return Command::SUCCESS;
        }

        $resultList = $this->ridePusher->pushRides($rideList);

        $io->success(sprintf('Got %d results for %d rides', count($resultList), is_countable($rideList) ? count($rideList) : 0));

        $this->printResultList($io, $resultList);

        return Command::SUCCESS;
    }

    protected function printCycleList(SymfonyStyle $io, array $cycleList): void
    {
        $io->table([
            'City', 'Day of week', 'Week of month', 'Ride Calculator'
        ],
            array_map(function (CityCycle $cityCycle): array
            {
                $rideCalculatorParts = explode('\\', $cityCycle->getRideCalculatorFqcn() ?? 'Standard');
                $rideCalculator = array_pop($rideCalculatorParts);

                return [
                    $cityCycle->getCity()->getName(), $cityCycle->getDayOfWeek(), $cityCycle->getWeekOfMonth(), $rideCalculator,
                ];
            }, $cycleList));
    }

    protected function printRideList(SymfonyStyle $io, array $rideList): void
    {
        $io->table([
            'City', 'Date Time UTC', 'Location', 'Title', 'Ride Calculator',
        ], array_map(function (Ride $ride): array {
            if ($ride->getLocation()) {
                $location = sprintf('%s (%f, %f)', $ride->getLocation(), $ride->getLatitude(), $ride->getLongitude());
            } else {
                $location = null;
            }

            $rideCalculatorParts = explode('\\', $ride->getCycle()->getRideCalculatorFqcn() ?? 'Standard');
            $rideCalculator = array_pop($rideCalculatorParts);

            return [
                $ride->getCity()->getName(), sprintf('%s (%d)', $ride->getDateTime()->format('Y-m-d H:i:s'), $ride->getDateTime()->format('U')), $location, $ride->getTitle(), $rideCalculator,
            ];
        }, $rideList));
    }

    protected function printResultList(SymfonyStyle $io, array $resultList): void
    {
        $io->table([
            'City', 'Date Time UTC', 'Location', 'Title', 'Http status code', 'Result',
        ], array_map(function (ApiResultInterface $result): array {
            $ride = $result->getRide();

            if ($ride->getLocation()) {
                $location = sprintf('%s (%f, %f)', $ride->getLocation(), $ride->getLatitude(), $ride->getLongitude());
            } else {
                $location = null;
            }

            $tableRow = [
                $ride->getCity()->getName(), $ride->getDateTime()->format('Y-m-d H:i:s'), $location, $ride->getTitle(),
            ];

            if ($result instanceof ErrorResult) {
                $tableRow += [
                    3 => $result->getHttpStatusCode(),
                    4 => implode(',', $result->getErrorMessageList())
                ];
            }

            return $tableRow;
        }, $resultList));
    }

    protected function stripDuplicateRides(array $rideList, array $existingRideList, SymfonyStyle $io): array
    {
        /**
         * @var Ride $newRide
         * @var Ride $existingRide
         */
        foreach ($rideList as $newRideKey => $newRide) {
            foreach ($existingRideList as $existingRide) {
                if (
                    $newRide->getDateTime()->format('Y-m-d') === $existingRide->getDateTime()->format('Y-m-d') &&
                    $newRide->getCity()->getId() === $existingRide->getCity()->getId()
                ) {
                    $io->note(sprintf('Ride %s in City %s is duplicated and removed from list', $newRide->getDateTime()->format('Y-m-d'), $newRide->getCity()->getName()));
                    unset($rideList[$newRideKey]);
                }
            }
        }

        return $rideList;
    }
}
