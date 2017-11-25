<?php

namespace AppBundle\Twig;


class MarkdownExtension extends \Twig_Extension
{
    public function __construct()
    {
    }
    public function getName()
    {
        return 'app_markdown';
    }
}
