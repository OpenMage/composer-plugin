<?php

$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PER-CS2.0' => true,
        'logical_operators' => true,
        'modernize_types_casting' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'single_quote' => true,
        'php_unit_test_case_static_method_calls' => true,
        'trailing_comma_in_multiline' => ['after_heredoc' => true, 'elements' => ['arguments', 'array_destructuring', 'arrays']],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__ . '/src',
                __DIR__ . '/tests',
            ])
            ->name('*.php')
            ->ignoreDotFiles(true)
            ->ignoreVCS(true),
    );
