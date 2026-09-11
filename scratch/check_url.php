<?php
require 'vendor/autoload.php';
$r = new ReflectionMethod('Filament\Resources\Resource', 'getUrl');
foreach ($r->getParameters() as $p) {
    echo $p->getName().': '.($p->getType() ? $p->getType() : 'none')."\n";
}
