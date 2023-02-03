<?php

namespace Kematjaya\MenuBundle\Builder;

use Kematjaya\MenuBundle\Parser\MenuParserInterface;

/**
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface MenuParserBuilderInterface 
{
    public function addParser(MenuParserInterface $element): self;

    public function getParser(string $className):MenuParserInterface;
}
