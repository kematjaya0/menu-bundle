<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace Kematjaya\MenuBundle\Builder;

use Kematjaya\MenuBundle\Parser\MenuParserInterface;

/**
 *
 * @author programmer
 */
interface MenuParserBuilderInterface 
{
    public function addParser(MenuParserInterface $element): self;

    public function getParser(string $className):MenuParserInterface;
}
