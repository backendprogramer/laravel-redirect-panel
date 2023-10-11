<?php

namespace Backendprogramer\RedirectPanel\Commands;

use Backendprogramer\RedirectPanel\RedirectPanelServiceProvider;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\Command;

class RedirectPanelCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name         = 'redirect-panel:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  = 'Publish all RedirectPanel resources and config files';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature    = 'redirect-panel:publish
            {--tag= : One or many tags that have assets you want to publish.}
            {--force : Overwrite any existing files.}';


    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $args = [
            '--provider' => RedirectPanelServiceProvider::class,
        ];

        if ($this->option('force')) {
            $args['--force'] = true;
        }

        $args['--tag'] = [$this->option('tag')];

        $this->call('vendor:publish', $args);

        return static::SUCCESS;
    }

    /**
     * Get the console command options.
     *
     * @return array
     *
     * @codeCoverageIgnore
     */
    protected function getOptions()
    {
        return [
            ['tag', 't', InputOption::VALUE_OPTIONAL, 'One or many tags that have assets you want to publish.', ''],
            ['force', 'f', InputOption::VALUE_OPTIONAL, 'Overwrite any existing files.', false],
        ];
    }
}
