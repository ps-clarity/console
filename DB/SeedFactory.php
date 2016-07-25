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
 * @subpackage Clarity\Console\DB
 */
namespace Clarity\Console\DB;

use Clarity\Support\DB\Factory;
use Clarity\Console\SlayerCommand;

/**
 * A console command that fills/seeds the database
 */
class SeedFactory extends SlayerCommand
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'db:seed:factory';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Seed based on the factories';

    /**
     * {@inheritdoc}
     */
    public function slash()
    {
        $factory = new Factory($this);
        $files = folder_files(config()->path->database . 'factories');

        if (!empty($files)) {
            foreach ($files as $file) {
                $this->comment('Processing ' . basename($file) . '...');

                require $file;

                $this->info('Done.' . "\n");
            }
        }

        return $this;
    }
}
