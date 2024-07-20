<?php


function root()
{
    return __DIR__;
}

function env(string $kee)
{
    return $_ENV[$kee];
}

function themes_dir()
{
    return __DIR__ .  "/themes";
}

function plugins_dir()
{
    return __DIR__ .  "/plugins";
}


// themes helper functions



// plugins helper functions