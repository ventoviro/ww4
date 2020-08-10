<?php

/**
 * Part of ww4 project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Windwalker\Data\Collection;

include __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/Console.php';

define('PROJECT_ROOT', realpath(dirname(__DIR__)));
define('PACKAGES_PATH', PROJECT_ROOT . '/packages');

class MergeComposer extends \Asika\SimpleConsole\Console
{
    /**
     * doExecute
     *
     * @return  int
     */
    protected function doExecute()
    {
        $packages = \Windwalker\Filesystem\Filesystem::folders(PACKAGES_PATH);

        $rootJsonFile = PROJECT_ROOT . '/composer.json';
        $rootJson = \Windwalker\collect(file_get_contents($rootJsonFile));

        foreach ($packages as $package) {
            if (!is_file($composerFile = $package->getPathname() . '/composer.json')) {
                continue;
            }
            
            $json = \Windwalker\collect($composerFile);

            if (!$json->get('name')) {
                continue;
            }
            
            $this->out('Sync: ' . $composerFile);

            $this->mergeRequires($rootJson->proxy('require'), (array) $json->get('require'));
            $this->mergeRequires($rootJson->proxy('require-dev'), (array) $json->get('require-dev'));
            $this->mergeRequires($rootJson->proxy('suggest'), (array) $json->get('suggest'));

            $this->mergeAutoload($rootJson, $json, 'autoload.psr-4', 'src');
            $this->mergeAutoload($rootJson, $json, 'autoload.files', 'bootstrap.php');
            $this->mergeAutoload($rootJson, $json, 'autoload-dev.psr-4', 'test');
        }

        \Windwalker\Filesystem\Filesystem::write(
            $rootJsonFile,
            $rootJson->toJson(['options' => JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES])
        );

        $this->out('Sync to composer.json');

        return 1;
    }

    protected function mergeRequires(Collection $rootRequires, array $requires)
    {
        foreach ($requires as $package => $version) {
            if (str_starts_with($package, 'windwalker')) {
                continue;
            }

            $rootRequires[$package] = $version;
        }
    }

    protected function mergeAutoload(Collection $rootJson, Collection $json, string $path, string $dir)
    {
        $target = $rootJson->proxy($path);

        $name = explode('/', $json->get('name'))[1];

        foreach ((array) $json->getDeep($path) as $key => $item) {
            if (is_numeric($key)) {
                $target->push("packages/$name/$dir");
            } else {
                $target->set($key, "packages/$name/$dir");
            }
        }
    }
}

(new MergeComposer())->execute();
