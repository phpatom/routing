<?php

namespace Atom\Routing\Contracts;

interface AbstractRouteContract
{
    public function getPattern():string;
    public function setPattern(String $methods):self;
    public function setHandler($handler):self;
    public function getHandler();
}

