<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'custom:test',
    description: 'Test a command.'
)]
class TestCommand extends Command
{
    protected function configure(): void
    {
        
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vars = $this->retrieveVars('AccessPoint\\ItemBoxKey');
        dump($vars);

        return Command::SUCCESS;
    }

    private function retrieveVars($entityClass): array
    {
        $vars = [];

        $vars['entityFullName'] = $entityClass;
        $matches = [];
        if (str_contains($vars['entityFullName'], '\\')) {
            preg_match('/(.+)\\\\(.+?)$/', $vars['entityFullName'], $matches);
            $vars['entityNamespace'] = $matches[1];
            $vars['entityName'] = $matches[2];
        } else {
            $vars['entityNamespace'] = '';
            $vars['entityName'] = $vars['entityFullName'];
        }
        $vars['entityTitle'] = preg_replace('/([a-z])([A-Z])/s','$1 $2', $vars['entityName']);

        $rootDir = __DIR__ . '/../../';
        foreach (file($rootDir . 'src/Controller/' . str_replace('\\', '/', $vars['entityFullName']) . 'Controller.php') as $line) {
            $matches = [];
            if (preg_match('/name: \'(.+)_index\'/', $line, $matches)) {
                $vars['routeNamePrefix'] = $matches[1];
            } else if (preg_match('/render\(\'(.+)\/index\.html\.twig\'/', $line, $matches)) {
                $vars['templatePathPrefix'] = $matches[1];
            } else if (preg_match('/\$(.+) = new/', $line, $matches)) {
                $vars['instanceNameSingular'] = $matches[1];
            } else if (preg_match('/\'(.+)\'.+findAll/', $line, $matches)) {
                $vars['instanceNamePlural'] = str_replace('_', '', lcfirst(ucwords($matches[1], '_')));
            }
        }
        foreach (file($rootDir . 'src/Form/' . str_replace('\\', '/', $vars['entityFullName']) . 'Type.php') as $line) {
            $matches = [];
            if (preg_match('/add\(\'(.+)\'\)/', $line, $matches)) {
                $vars['formFields'][] = $matches[1];
            }
        }
        $templatePathParts = explode('/', $vars['templatePathPrefix']);
        $instanceName = $templatePathParts[array_key_last($templatePathParts)];
        foreach (file($rootDir . "templates/{$vars['templatePathPrefix']}/index.html.twig") as $line) {
            $matches = [];
            if (preg_match('/<th>(.+)<\/th>/', $line, $matches)) {
                if ($matches[1] !== 'Id' && $matches[1] !== 'actions') {
                    $vars['dataHeaders'][] =  preg_replace('/([a-z])([A-Z])/s','$1 $2', $matches[1]);
                }
            } else if (preg_match('/<td>{{ (.+) }}<\/td>/', $line, $matches)) {
                if ($matches[1] !== "{$instanceName}.id") {
                    $vars['dataFields'][] = str_replace($instanceName, $vars['instanceNameSingular'], $matches[1]);
                }
            }
        }

        return $vars;
    }
}
