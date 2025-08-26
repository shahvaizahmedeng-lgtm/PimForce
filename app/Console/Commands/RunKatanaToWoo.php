<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunKatanaToWoo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:katana-to-woo {--dry-run} {--integration-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the KatanaPIM to WooCommerce Python sync script';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $script = base_path('scripts/katana_to_woo.py');
        $python = '/home/forge/app.pimforce.io/pim/bin/python3'; // or 'python' if that's your system command

        $args = [];
        if ($this->option('dry-run')) {
            $args[] = '--dry-run';
        }
        if ($this->option('integration-id')) {
            $args[] = '--integration-id ' . escapeshellarg($this->option('integration-id'));
        }
        $cmd = "$python $script " . implode(' ', $args);

        $this->info("Running: $cmd");
        exec($cmd . ' 2>&1', $output, $exitCode);
        foreach ($output as $line) {
            $this->line($line);
        }
        return $exitCode;
    }
}
