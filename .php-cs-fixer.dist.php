<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PHP81Migration' => true,
        '@PSR12' => true,
        'single_line_throw' => false,
        'phpdoc_to_comment' => false,
        'single_line_comment_spacing' => false,
        'single_line_comment_style' => false,
        'php_unit_fqcn_annotation' => false,
        'concat_space' => ['spacing' => 'one'],
    ])
    ->setFinder($finder)
;
