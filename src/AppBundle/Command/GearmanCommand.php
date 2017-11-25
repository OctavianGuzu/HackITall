<?php

namespace AppBundle\Command;

use EduardTrandafir\GearmanBundle\Service\GearmanClient;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GearmanCommand extends ContainerAwareCommand
{
    /** @var  GearmanClient */
    protected $gearmanClient;


    public function configure()
    {
        $this->setName("gearman:test")->setDescription("Publish a message to a gearman queue");
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $message = json_encode(["10000 " => time()]);

        /** @var SlowGenusGenerator $slowGenusGenerator */
        $slowGenusGenerator = $this->get('app.generate_genus');
        $lastGenus = $slowGenusGenerator->generateOneGenus();

        /** @var GearmanClient gearmanClient */
        $this->gearmanClient = $this->getContainer()->get("app.gearman.service")->getClient(false);
        $this->gearmanClient->doBackgroundJob('Test~readTest', $message);

    }
}
