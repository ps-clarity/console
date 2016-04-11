<?php
/**
 * PhalconSlayer\Framework
 *
 * @copyright 2015-2016 Daison Carino <daison12006013@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phalconslayer.readme.io
 */

/**
 * @package Clarity
 * @subpackage Clarity\Console\Script
 */
namespace Clarity\Console\Script;

use Clarity\Console\CLI;
use Clarity\Console\SlayerCommand;
use Symfony\Component\Console\Input\InputArgument;

class RunCommand extends SlayerCommand
{
    protected $name = 'run';

    protected $description = 'Automated scripts to be run.';

    public function slash()
    {
        $script = $this->input->getArgument('script');

        $lists = config()->script->toArray();

        if ( isset($lists[$script]) === false ) {
            $this->error("\nWe can't find `" . $script . '` in the lists of script.' . "\n");

            return;
        }

        $lines = $lists[$script];

        $output = CLI::bash($lines);

        if ( $output ) {
            $this->comment($output);
        }
    }

    protected function arguments()
    {
        return [
            ['script', InputArgument::REQUIRED, 'Automated script to be use'],
        ];
    }
}
