<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'line_ending' => false,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'protected_to_private' => false,
        'yoda_style' => ['equal' => null, 'identical' => null, 'less_and_greater' => null],
        'no_superfluous_phpdoc_tags' => false,
        'single_line_throw' => false,
        'php_unit_mock_short_will_return' => false,
        'ordered_imports' => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__.'/src')
            ->append([__FILE__])
    )
;
