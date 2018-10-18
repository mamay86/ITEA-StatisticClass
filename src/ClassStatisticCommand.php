<?php

/*
 * This file is part of the "Statistic Class" project.
 *
 * (c) Roman Nagriy <karlson2006@ukr.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mamay86\StatisticClass;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

/**
 * Command implements logic of statistic class.
 *
 * @author Roman Nagriy <karlson2006@ukr.net>
 */
class ClassStatisticCommand extends Command
{
    private $srcDir;
    private $rootNamespace;

    public function __construct(string $srcDir, string $rootNamespace, string $name = null)
    {
        parent::__construct($name);

        $this->srcDir = $srcDir;
        $this->rootNamespace = $rootNamespace;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:class-statistic')
            ->setDescription('Show statistic of the classe.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in($this->srcDir)
            ->name('/^[A-Z].+\.php$/')
        ;

        foreach ($finder as $file) {
            $path = $file->getRelativePathname();
            $fullClassName = $this->rootNamespace . '\\' . \rtrim($path, '.php');

            try {
                $reflector = new \ReflectionClass($fullClassName);
            } catch (\ReflectionException $e) {
                var_dump($e->getMessage());
                continue;
            }

            $classStatus = "";
            if ($reflector->isAbstract()) {
                $classStatus = "(Abstract class)";
            } elseif ($reflector->isFinal()) {
                $classStatus = "(Final class)";
            }
            $output->writeln(\sprintf('<info>Class: %s %s</info>', $reflector->getShortName(), $classStatus));
            $output->writeln(\sprintf('<info>Properties:</info>'));
            $output->writeln("\t".\sprintf('<info>public: %s</info>', count($reflector->getProperties(\ReflectionProperty::IS_PUBLIC))));
            $output->writeln("\t".\sprintf('<info>protected: %s</info>', count($reflector->getProperties(\ReflectionProperty::IS_PROTECTED))));
            $output->writeln("\t".\sprintf('<info>private: %s</info>', count($reflector->getProperties(\ReflectionProperty::IS_PRIVATE))));
            $output->writeln(\sprintf('<info>Methods:</info>'));
            $output->writeln("\t".\sprintf('<info>public: %s</info>', count($reflector->getMethods(\ReflectionProperty::IS_PUBLIC))));
            $output->writeln("\t".\sprintf('<info>protected: %s</info>', count($reflector->getMethods(\ReflectionProperty::IS_PROTECTED))));
            $output->writeln("\t".\sprintf('<info>private: %s</info>', count($reflector->getMethods(\ReflectionProperty::IS_PRIVATE)))."\n");
        }

    }
}
