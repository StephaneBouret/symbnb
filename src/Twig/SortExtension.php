<?php

namespace App\Twig;

use Twig\TwigFunction;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;

class SortExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sort_form_children', [$this, 'sortFormChildren'])
        ];
    }

    public function sortFormChildren(FormView $formView, string $property = 'label'): array
    {
        $children = iterator_to_array($formView->children);
        usort($children, function($a, $b) use ($property) {
            return $a->vars[$property] <=> $b->vars[$property];
        });

        return $children;
    }
}