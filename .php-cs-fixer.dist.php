<?php

$finder = (new PhpCsFixer\Finder())
    ->name('*.php')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'types_spaces' => ['space' => 'none'],
        'cast_spaces' => ['space' => 'none'],
        'native_function_invocation' => false,
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'remove_inheritdoc' => true],
        'phpdoc_to_comment' => false,
        'fopen_flags' => ['b_mode' => true],
        'function_declaration' => ['closure_function_spacing' => 'none', 'closure_fn_spacing' => 'none'],
        'phpdoc_separation' => ['groups' => [['ORM\\*'], ['Assert\\*'], ['Serializer\\*']]],
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => false,
            'attribute_placement' => 'same_line',
        ],
    ]);
