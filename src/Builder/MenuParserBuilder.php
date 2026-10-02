<?php

namespace Kematjaya\MenuBundle\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Kematjaya\MenuBundle\Parser\MenuParserInterface;

/**
 * Description of MenuParserBuilder
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuParserBuilder implements MenuParserBuilderInterface
{
    private readonly ArrayCollection $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function addParser(MenuParserInterface $element): MenuParserBuilderInterface
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
        }

        return $this;
    }

    public function getParser(string $className): MenuParserInterface
    {
        $classes = $this->elements->filter(fn(MenuParserInterface $menuParser): bool => $className === $menuParser::class);

        if ($classes->isEmpty()) {
            throw new \Exception(
                sprintf("class %s not found", $className)
            );
        }

        return $classes->first();
    }

}
