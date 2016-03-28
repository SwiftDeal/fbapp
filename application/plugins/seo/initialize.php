<?php

// initialize seo
include("seo.php");

$seo = new SEO(array(
    "title" => "DinchakApps",
    "keywords" => "fun, quiz, facebook game, test, activity on facebook, cool facebook post",
    "description" => "We let you post cool stuff on facebook",
    "author" => "DinchakApps",
    "robots" => "INDEX,FOLLOW",
    "photo" => CDN . "img/logo.png"
));

Framework\Registry::set("seo", $seo);
