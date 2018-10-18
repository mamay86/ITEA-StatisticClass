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

use phpDocumentor\Reflection\DocBlockFactory;
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
        $counter = 0;
        $factory = DocBlockFactory::createInstance();

        $finder = new Finder();
        $finder
            ->files()
            ->in($this->srcDir)
            ->name('/^[A-Z].+\.php$/')
        ;

        foreach ($finder as $file) {
            $path = $file->getRelativePathname();
            var_dump($path);
            $fullClassName = $this->rootNamespace . '\\' . \rtrim($path, '.php');

            try {
                $reflector = new \ReflectionClass($fullClassName);
            } catch (\ReflectionException $e) {
                continue;
            }

            if (!$docComment = $reflector->getDocComment()) {
                continue;
            }

            $docBlock = $factory->create($docComment);
            /* @var \phpDocumentor\Reflection\DocBlock\Tags\Author[] $authors */
            $authors = $docBlock->getTagsByName('author');

            foreach ($authors as $author) {
                if ($author->getEmail() === $email) {
                    ++$counter;

                    break;
                }
            }
        }

        $output->writeln(
            \sprintf('<info>Developer with email "%s" was created %d classes.</info>', $email, $counter)
        );
    }
}
