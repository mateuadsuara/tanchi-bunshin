<?php

class NullWriter implements Writer{
    function create($path){}
    function isReady(){}
    function writeRow($data){}
}