<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony'                    => true,
        '@PER-CS2.0'                  => true,
        '@PHP82Migration'             => true,
        'blank_line_before_statement' => [
            'statements' => ['return', 'if'],
        ],
        'binary_operator_spaces' => [
            'operators' => [
                '='   => 'align_single_space_minimal',
                '=>'  => 'align_single_space_minimal',
                '??=' => 'align_single_space_minimal',
            ],
        ],
    ])
    ->setFinder($finder)
;
