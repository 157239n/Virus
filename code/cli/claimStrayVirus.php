<?php

global $virusFactory, $requestData;

$virusFactory->new($requestData->getExplodedPath()[2], true, $requestData->getExplodedPath()[3]);
