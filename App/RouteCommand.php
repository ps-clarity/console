<?php
/**
 * PhalconSlayer\Framework.
 *
 * @copyright 2015-2016 Daison Carino <daison12006013@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://docs.phalconslayer.com
 */

/**
 */
namespace Clarity\Console\App;

use League\Flysystem\Filesystem;
use Clarity\Console\Brood;
use Symfony\Component\Console\Input\InputArgument;
use League\Flysystem\Adapter\Local as LeagueFlysystemAdapterLocal;

/**
 * A console command that generates a route template.
 */
class RouteCommand extends Brood
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'app:route';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Generate a new route group';

    /**
     * This handles the filesystem app folder.
     * @var \League\Flysystem\Filesystem
     */
    private $app;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->app = new Filesystem(
            new LeagueFlysystemAdapterLocal(config()->path->app, 0)
        );

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function slash()
    {
        $app_path = str_replace(config('path.root'), '', config()->path->app);
        $arg_name = studly_case(str_slug($this->input->getArgument('name'), '_'));

        $stub = file_get_contents(__DIR__.'/stubs/makeRoute.stub');
        $stub = stubify($stub, [
            'routeName' => $arg_name,
            'prefixRouteName' => strtolower($arg_name),
        ]);

        $file_name = $arg_name.'Routes.php';

        $module = $this->input->getArgument('module');
        $has_dir = is_dir(config()->path->app.$module);

        if ($has_dir === false) {
            $this->error('Module not found `'.$module.'`');

            return;
        }

        $module = $this->input->getArgument('module');

        if ($this->app->has($module) === false) {
            $this->error('Module not found `'.$module.'`');

            return;
        }

        $routes = $module.'/routes/';
        if ($this->app->has($routes) === false) {
            $this->error('Routes folder not found from your module: `'.$module.'`');

            return;
        }

        $stub = stubify(
            $stub, [
                'namespace' => path_to_namespace($app_path.$routes),
                'controllerNamespace' => path_to_namespace(
                    $app_path.$module.'/controllers/'
                ),
            ]
        );

        $this->info('Crafting Route...');

        if ($this->app->has($file_name)) {
            $this->error('   Route already exists!');

            return;
        }

        $this->app->put($routes.$file_name, $stub);
        $this->info('   '.$file_name.' created!');

        $this->callDumpAutoload();
    }

    /**
     * {@inheritdoc}
     */
    protected function arguments()
    {
        $arguments = [
            ['name', InputArgument::REQUIRED, 'The route name to use'],
            ['module', InputArgument::REQUIRED, 'The module to link on'],
        ];

        return $arguments;
    }
}
