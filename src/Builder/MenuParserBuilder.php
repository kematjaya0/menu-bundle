<?php

namespace Kematjaya\MenuBundle\Builder;

use Doctrine\Common\Collections\Collection;
use Kematjaya\MenuBundle\Parser\MenuParserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Description of MenuParserBuilder
 *
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class MenuParserBuilder implements MenuParserBuilderInterface 
{
    private Collection $elements;
    
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
        $classes = $this->elements->filter(function (MenuParserInterface $menuParser) use ($className) {
            
            return $className === get_class($menuParser);
        });
        
        if ($classes->isEmpty()) {
            throw new \Exception(
                sprintf("class %s not found", $className)
            );
        }
        
        return $classes->first();
    }

}
