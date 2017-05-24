<?php

// Lista stop word italiana

$stopword_It = [
    'ad', //  a prima di una vocale
    'al', //  a + il
    'allo', //  a + lo
    'ai', //  a + i
    'agli', //  a + gli
    'all', //  a + l'
    'agl', //  a + gl'
    'alla', //  a + la
    'alle', //  a + le
    'con',
    'col', //  con + il
    'coi', //  con + i (nella forma collo, cogli etc sono rari e in disuso)
    'da',
    'dal', //  da + il
    'dallo', //  da + lo
    'dai', //  da + i
    'dagli', //  da + gli
    'dall', //  da + l'
    'dagl', //  da + gll'
    'dalla', //  da + la
    'dalle', //  da + le
    'di',
    'del', //  di + il
    'dello', //  di + lo
    'dei', //  di + i
    'degli', //  di + gli
    'dell', //  di + l'
    'degl', //  di + gl'
    'della', //  di + la
    'delle', //  di + le
    'in',
    'nel', //  in + el
    'nello', //  in + lo
    'nei', //  in + i
    'negli', //  in + gli
    'nell', //  in + l'
    'negl', //  in + gl'
    'nella', //  in + la
    'nelle', //  in + le
    'su',
    'sul', //  su + il
    'sullo', //  su + lo
    'sui', //  su + i
    'sugli', //  su + gli
    'sull', //  su + l'
    'sugl', //  su + gl'
    'sulla', //  su + la
    'sulle', //  su + le
    'per',
    'tra',
    'contro',
    'io',
    'tu',
    'lui',
    'lei',
    'noi',
    'voi',
    'loro',
    'mio', // declinazioni di mio
    'mia',
    'miei',
    'mie',
    'tuo', // declinazioni di tuo
    'tua',
    'tuoi',
    'tue',
    'suo', // declinazioni di suo
    'sua',
    'suoi',
    'sue',
    'nostro', // declinazioni di nostro
    'nostra',
    'nostri',
    'nostre',
    'vostro', //  declinazioni di vostro
    'vostra',
    'vostri',
    'vostre',
    'mi',
    'ti',
    'ci',
    'vi',
    'lo',
    'la',
    'li',
    'le',
    'gli',
    'ne',
    'né',
    'nè',
    'il',
    'un', // declinazioni di un
    'uno',
    'una',
    'ma',
    'ed', //  e davanti a vocale
    'se',
    'perché',
    'perchè', //  scritto sbagliato perché la gente è ignorante
    'anche',
    'come',
    'dov', //  usato come dov'
    'dove',
    'che',
    'chi',
    'cui',
    'non',
    'più',
    'qual', //  usato come qual'è o qual è
    'quale',
    'quanto', //  declinazioni di quanto
    'quanti',
    'quanta',
    'quante',
    'quello', //  declinazioni di quello
    'quelli',
    'quella',
    'quelle',
    'questo', //  declinazioni di questo
    'questi',
    'questa',
    'queste',
    'tanto', // declinazioni di tanto
    'tante',
    'tanti',
    'tante',
    'troppo', //  declinazioni di troppo
    'troppi',
    'troppa',
    'troppe',
    'si',
    'tutto', // declinazioni di tutto
    'tutti',
    'tutta',
    'tutte',

    '', //  lettere singole:
    'a',
    'à',
    'c', //  usato come c' per ce o ci
    'e',
    'i',
    'ì',
    'l', //  usato come l'
    'o',
    'ò',
    'ù',

// forme del verbo avere:
    'ho',
    'hai',
    'ha',
    'abbiamo',
    'avete',
    'hanno',
    'abbia',
    'abbiate',
    'abbiano',
    'avrà',
    'avrai',
    'avrò',
    'avremo',
    'avrete',
    'avranno',
    'avrei',
    'avresti',
    'avrebbe',
    'avremmo',
    'avreste',
    'avrebbero',
    'avevo',
    'avevi',
    'aveva',
    'avevamo',
    'avevate',
    'avevano',
    'ebbi',
    'avesti',
    'ebbe',
    'avemmo',
    'aveste',
    'ebbero',
    'avessi',
    'avesse',
    'avessimo',
    'avessero',
    'avendo',
    'avuto',
    'avuta',
    'avuti',
    'avute',

// forme del verbo of essere:
    'sono',
    'sei',
    'è',
    'siamo',
    'siete',
    'sia',
    'siate',
    'siano',
    'sarà',
    'sarai',
    'sarò',
    'saremo',
    'sarete',
    'saranno',
    'sarei',
    'saresti',
    'sarebbe',
    'saremmo',
    'sareste',
    'sarebbero',
    'ero',
    'eri',
    'era',
    'eravamo',
    'eravate',
    'erano',
    'fui',
    'fosti',
    'fu',
    'fummo',
    'foste',
    'furono',
    'fossi',
    'fosse',
    'fossimo',
    'fossero',
    'essendo',

// forme del verbo fare:
    'faccio',
    'fai',
    'facciamo',
    'fanno',
    'faccia',
    'facciate',
    'facciano',
    'farà',
    'farai',
    'farò',
    'faremo',
    'farete',
    'faranno',
    'farei',
    'faresti',
    'farebbe',
    'faremmo',
    'fareste',
    'farebbero',
    'facevo',
    'facevi',
    'faceva',
    'facevamo',
    'facevate',
    'facevano',
    'feci',
    'facesti',
    'fece',
    'facemmo',
    'faceste',
    'fecero',
    'facessi',
    'facesse',
    'facessimo',
    'facessero',
    'facendo',

// forme del verbo stare:
    'sto',
    'stò',
    'stai',
    'sta',
    'stà',
    'stiamo',
    'stanno',
    'stia',
    'stiate',
    'stiano',
    'starà',
    'starai',
    'starò',
    'staremo',
    'starete',
    'staranno',
    'starei',
    'staresti',
    'starebbe',
    'staremmo',
    'stareste',
    'starebbero',
    'stavo',
    'stavi',
    'stava',
    'stavamo',
    'stavate',
    'stavano',
    'stetti',
    'stesti',
    'stette',
    'stemmo',
    'steste',
    'stettero',
    'stessi',
    'stesse',
    'stessimo',
    'stessero',
    'stando',

// Altre stop words trovate nel tempo
    'praticamente',
    'sostanzialmente',
    'nonostante',
    'quindi',
    'dunque',
    'infatti',
    'completamente',
    'progressivamente',
    'proprio',
    'fino',

    'dando', //verbo dare

    'richiede', //verbo richiedere
    'richieda',
    'richiedono',
];

return array_unique($stopword_It);