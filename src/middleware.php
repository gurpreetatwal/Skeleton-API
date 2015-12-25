<?php
$app->add(new CorsSlim\CorsSlim($container["settings"]["CORS"]));