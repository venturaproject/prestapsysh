<?php

namespace PrestaShop\Module\Prestapsysh\Command;

use Configuration;
use Psy\Shell;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PsyShellCommand extends Command
{
    protected static $defaultName = 'prestapsysh:shell';

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        parent::__construct();
        $this->translator = $translator;
    }

    protected function configure()
    {
        $this
            ->setName('prestapsysh:shell')
            ->setDescription('Execute PsySH shell');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Check if the command is enabled
        if (!Configuration::get('PSY_CC_ACTIVE')) {
            $errorMessage = $this->translator->trans('The command is disabled in PrestaShop Psysh.', [], 'Modules.Prestapsysh.Admin');
            $output->writeln(sprintf('<error>%s</error>', $errorMessage)).\PHP_EOL;
            return \defined(Command::class.'::INVALID') ? Command::INVALID : 2;
        }

        // Start the Psy Shell
        $infoMessage = $this->translator->trans('Starting Psy Shell...', [], 'Modules.Prestapsysh.Admin');
        $output->writeln(sprintf('<info>%s</info>', $infoMessage));

        // Initialize and run the Psy Shell
        $shell = new Shell();
        $shell->setScopeVariables([
            'output' => $output,
        ]);

        $shell->run();

        return \defined(Command::class.'::SUCCESS') ? Command::SUCCESS : 0;
    }
}

