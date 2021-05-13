<?php

return [
    // Default width, no UTF-8, single line, no wrapping
    0 => [
        // $input
        [
            'string' => 'Some short string',
        ],
        // $expected
        'Some short string',
    ],

    // Default width, UTF-8, single line, no wrapping
    1 => [
        // $input
        [
            'string' => 'Söme shört string',
        ],
        // $expected
        'Söme shört string',
    ],

    // Default width, no UTF-8, single line, wrapping
    2 => [
        // $input
        [
            'string' => 'This is a string which is longer than 80 characters but does not contain UTF-8-encoded characters. So what?',
        ],
        // $expected
        "This is a string which is longer than 80 characters but does not contain\nUTF-8-encoded characters. So what?"
    ],

    // Default width, no UTF-8, multiple lines, no wrapping
    3 => [
        // $input
        [
            'string' => "This is a string which is longer than 80 \ncharacters but does not contain UTF-8-encoded\n characters. So what?",
        ],
        // $expected
        "This is a string which is longer than 80 \ncharacters but does not contain UTF-8-encoded\n characters. So what?"
    ],

    // Default width, UTF-8, multiple lines, no wrapping
    4 => [
        // $inpüt
        [
            'string' => "This is ä string which is lönger thän 80 \nchäräcters büt döes nöt cöntäin UTF-8-encöded\n chäräcters. Sö whät?",
        ],
        // $expected
        "This is ä string which is lönger thän 80 \nchäräcters büt döes nöt cöntäin UTF-8-encöded\n chäräcters. Sö whät?"
    ],

    // Default width, no UTF-8, multiple lines, wrapping
    5 => [
        // $input
        [
            'string' => "This is\n a string which is longer than 80 characters but does not contain UTF-8-encoded characters.\n So what?",
        ],
        // $expected
        "This is\n a string which is longer than 80 characters but does not contain UTF-8-encoded\ncharacters.\n So what?"
    ],

    // Default width, no UTF-8, multiple lines, wrapping
    6 => [
        // $inpüt
        [
            'string' => "This is\n ä string which is lönger thän 80 chäräcters büt döes nöt cöntäin UTF-8-encöded chäräcters. Sö whät?",
        ],
        // $expected
        "This is\n ä string which is lönger thän 80 chäräcters büt döes nöt cöntäin UTF-8-encöded\nchäräcters. Sö whät?"
    ],

// -----------------

    // Custom width, no UTF-8, single line, no wrapping
    7 => [
        // $input
        [
            'string' => 'Some short string',
            'length' => 18,
        ],
        // $expected
        'Some short string',
    ],

    // Custom width, UTF-8, single line, no wrapping
    8 => [
        // $input
        [
            'string' => 'Söme shört string',
            'length' => 18,
        ],
        // $expected
        'Söme shört string',
    ],

    // Custom width, no UTF-8, single line, wrapping
    9 => [
        // $input
        [
            'string' => 'This is a string which is longer than 80 characters but does not contain UTF-8-encoded characters. So what?',
            'length' => 17,
        ],
        // $expected
        "This is a string\nwhich is longer\nthan 80\ncharacters but\ndoes not contain\nUTF-8-encoded\ncharacters. So\nwhat?"
    ],

    // Custom width, UTF-8, single line, wrapping
    10 => [
        // $input
        [
            'string' => 'This is ä string which is lönger thän 80 chäräcters büt döes nöt cöntäin UTF-8-encöded chäräcters. Sö whät?',
            'length' => 17,
        ],
        // $expected
        "This is ä string\nwhich is lönger\nthän 80\nchäräcters büt\ndöes nöt cöntäin\nUTF-8-encöded\nchäräcters. Sö\nwhät?"
    ],

    // Default width, UTF-8, wrapping
    11 => [
        // $input
        [
            'string' => 'This is ä string which is lönger thän 80 chäräcters büt döes nöt cöntäin UTF-8-encöded chäräcters. Sö whät?',
        ],
        // $expected
        "This is ä string which is lönger thän 80 chäräcters büt döes nöt cöntäin\nUTF-8-encöded chäräcters. Sö whät?"
    ],

// -----------------

    // Preserve, single line, non UTF-8
    12 => [
        // $input
        [
            'string' => 'This is a string which is longer than 80 characters but does not contain UTF-8-encoded characters. So what?',
            'length' => 60,
            'separator' => "\n",
            'preserve' => true,
        ],
        // $expected
        "This is a string which is longer than 80 characters but does\nnot contain UTF-8-encoded characters. So what?"
    ],

    // Preserve, single line, UTF-8
    13 => [
        // $input
        [
            'string' => 'This is ä string which is lönger thän 75 chäräcters büt döes nöt cöntäin UTF-8-encöded chäräcters. Sö whät?',
            'length' => 75,
            'separator' => "\n",
            'preserve' => true,
        ],
        // $expected
        "This is ä string which is lönger thän 75 chäräcters büt döes nöt cöntäin\nUTF-8-encöded chäräcters. Sö whät?"
    ],

    // No preserve, multiple lines, non UTF-8
    14 => [
        // $input
        [
            'string' => "This\nis a string which is longer than 75 characters but does not contain UTF-8-encoded characters. So what?",
            'length' => 75,
            'separator' => "\n",
            'preserve' => false,
        ],
        // $expected
        "This\nis a string which is longer than 75 characters but does not contain\nUTF-8-encoded characters. So what?"
    ],

    // No preserve, multiple lines, UTF-8
    15 => [
        // $input
        [
            'string' => "This\nis ä string which is lönger thän 75 chäräcters büt döes nöt cöntäin UTF-8-encöded chäräcters. Sö whät?",
            'length' => 75,
            'separator' => "\n",
            'preserve' => false,
        ],
        // $expected
        "This\nis ä string which is lönger thän 75 chäräcters büt döes nöt cöntäin\nUTF-8-encöded chäräcters. Sö whät?"
    ],

// -----------------

    // Default width, no UTF-8, single line, wrapping, custom wrap char
    16 => [
        // $input
        [
            'string' => 'This is a string which is longer than 75 characters but does not contain UTF-8-encoded characters. So what?',
            'length' => 75,
            'separator' => '---'
        ],
        // $expected
        'This is a string which is longer than 75 characters but does not contain---UTF-8-encoded characters. So what?'
    ],

    // Default width, no UTF-8, multiple lines, wrapping, custom wrap char
    17 => [
        // $input
        [
            'string' => "This is a string which is longer than 75 \ncharacters but does not contain UTF-8-encoded\n characters. So what?",
            'length' => 75,
            'separator' => '---'
        ],
        // $expected
        "This is a string which is longer than 75 \ncharacters but does not contain---UTF-8-encoded\n characters. So what?"
    ],

    // Default width, UTF-8, multiple lines, wrapping, custom wrap char
    18 => [
        // $inpüt
        [
            'string' => "This is ä string which is lönger thän 75 \nchäräcters büt döes nöt cöntäin UTF-8-encöded\n chäräcters. Sö whät?",
            'length' => 75,
            'separator' => '---'
        ],
        // $expected
        "This is ä string which is lönger thän 75 \nchäräcters büt döes nöt cöntäin---UTF-8-encöded\n chäräcters. Sö whät?"
    ],

// ---------- PHP tests to ensure compatibility ------------

// ext/standard/tests/strings/wordwrap.phpt

    19 => [
        [
            'string' => '12345 12345 12345 12345',
        ],
        '12345 12345 12345 12345',
    ],

    20 => [
        [
            'string' => '12345 12345 1234567890 1234567890',
            12
        ],
        "12345 12345\n1234567890\n1234567890",
    ],

    21 => [
        [
            'string' => '12345 12345 12345 12345',
            0
        ],
        "12345\n12345\n12345\n12345",
    ],
    22 => [
        [
            'string' => '12345 12345 12345 12345',
            0,
            'ab'
        ],
        '12345ab12345ab12345ab12345',
    ],
    23 => [
        [
            'string' => '12345 12345 1234567890 1234567890',
            12,
            'ab'
        ],
        '12345 12345ab1234567890ab1234567890'
    ],
    24 => [
        [
            'string' => '123ab123ab123',
            3,
            'ab'
        ],
        '123ab123ab123'
    ],
    25 => [
        [
            'string' => '123ab123ab123',
            5,
            'ab'
        ],
        '123ab123ab123',
    ],
    26 => [
        [
            'string' => '123  123ab123',
            3,
            'ab'
        ],
        '123ab 123ab123',
    ],
    27 => [
        [
            'string' => '123 123ab123',
            5,
            'ab'
        ],
        '123ab123ab123',
    ],
    28 => [
        [
            'string' => '123 123 123',
            10,
            'ab'
        ],
        '123 123ab123',
    ],
    29 => [
        [
            'string' => '123ab123ab123',
            3,
            'ab',
            1
        ],
        '123ab123ab123',
    ],
    30 => [
        [
            'string' => '123ab123ab123',
            5,
            'ab',
            1
        ],
        '123ab123ab123',
    ],
    31 => [
        [
            'string' => '123  123ab123',
            3,
            'ab',
            1
        ],
        '123ab 12ab3ab123',
    ],
    32 => [
        [
            'string' => '123  123ab123',
            5,
            'ab',
            1
        ],
        '123 ab123ab123'
    ],
    33 => [
        [
            'string' => '123  123  123',
            8,
            'ab',
            1
        ],
        '123  123ab 123'
    ],
    34 => [
        [
            'string' => '123  12345  123',
            8,
            'ab',
            1
        ],
        '123 ab12345 ab123'
    ],
    35 => [
        [
            'string' => '1234',
            1,
            'ab',
            1
        ],
        '1ab2ab3ab4'
    ],
    36 => [
        [
            'string' => '12345 1234567890',
            5,
            '|',
            1
        ],
        '12345|12345|67890'
    ],
    37 => [
        [
            'string' => '123 1234567890 123',
            10,
            '|==',
            1
        ],
        '123|==1234567890|==123'
    ],
    38 => [
        [
            chr(0),
            0,
            ''
        ],
        false
    ],

// ext/standard/tests/strings/wordwrap_basic.phpt

    39 => [
        [
            'string' => 'The quick brown foooooooooox jummmmmmmmmmmmped over the lazzzzzzzzzzzy doooooooooooooooooooooog.',
        ],
        "The quick brown foooooooooox jummmmmmmmmmmmped over the lazzzzzzzzzzzy\ndoooooooooooooooooooooog.",
    ],

    40 => [
        [
            'string' => 'The quick brown foooooooooox jummmmmmmmmmmmped over the lazzzzzzzzzzzy doooooooooooooooooooooog.',
            'length' => 80,
        ],
        "The quick brown foooooooooox jummmmmmmmmmmmped over the lazzzzzzzzzzzy\ndoooooooooooooooooooooog.",
    ],

    41 => [
        [
            'string' => 'The quick brown foooooooooox jummmmmmmmmmmmped over the lazzzzzzzzzzzy doooooooooooooooooooooog.',
            'length' => 80,
            'separator' => '<br />\n',
        ],
        'The quick brown foooooooooox jummmmmmmmmmmmped over the lazzzzzzzzzzzy<br />\ndoooooooooooooooooooooog.',
    ],

    42 => [
        [
            'string' => 'The quick brown foooooooooox jummmmmmmmmmmmped over the lazzzzzzzzzzzy doooooooooooooooooooooog.',
            'length' => 10,
            'separator' => '<br />\n',
            'preserve' => true,
        ],
        'The quick<br />\nbrown<br />\nfooooooooo<br />\nox<br />\njummmmmmmm<br />\nmmmmped<br />\nover the<br />\nlazzzzzzzz<br />\nzzzy<br />\ndooooooooo<br />\noooooooooo<br />\nooog.'
    ],

    43 => [
        [
            'string' => 'The quick brown foooooooooox jummmmmmmmmmmmped over the lazzzzzzzzzzzy doooooooooooooooooooooog.',
            'length' => 10,
            'separator' => '<br />\n',
            'preserve' => false,
        ],
        'The quick<br />\nbrown<br />\nfoooooooooox<br />\njummmmmmmmmmmmped<br />\nover the<br />\nlazzzzzzzzzzzy<br />\ndoooooooooooooooooooooog.'
    ],

// ext/standard/tests/strings/wordwrap_variation5.phpt

    44 => [
        [
            'string' => 'Testing wordrap function',
            'length' => 1,
        ],
        "Testing\nwordrap\nfunction",
    ],

    45 => [
        [
            'string' => 'Testing wordrap function',
            'length' => 1,
            'separator' => ' ',
        ],
        'Testing wordrap function'
    ],

    46 => [
        [
            'string' => 'Testing wordrap function',
            'length' => 1,
            'separator' => '  ',
        ],
        'Testing  wordrap  function'
    ],

    47 => [
        [
            'string' => 'Testing wordrap function',
            'length' => 1,
            'separator' => ' ',
            'cut' => false,
        ],
        'Testing wordrap function'
    ],

    48 => [
        [
            'string' => 'Testing wordrap function',
            'length' => 1,
            'separator' => '  ',
            'cut' => false,
        ],
        'Testing  wordrap  function'
    ],

    49 => [
        [
            'string' => 'Testing wordrap function',
            'length' => 1,
            'separator' => ' ',
            'cut' => true,
        ],
        'T e s t i n g w o r d r a p f u n c t i o n'
    ],

    50 => [
        [
            'string' => 'Testing wordrap function',
            'length' => 1,
            'separator' => '  ',
            'cut' => true,
        ],
        'T  e  s  t  i  n  g  w  o  r  d  r  a  p  f  u  n  c  t  i  o  n'
    ],
];
