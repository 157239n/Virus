<?php

namespace Kelvinho\Virus\Singleton;

/**
 * Class HtmlTemplate, provides sort of a shared structure. This is not really elegant, I will factor it later.
 *
 * @package Kelvinho\Virus\Singleton
 */
class HtmlTemplate {
    /**
     * The header (no <head> included. This contains:
     * - Css from 157239n.com
     * - Css from w3school.com
     * - Viewports
     * - .link classes have pointer cursor and their color blue
     */
    public static function header(): void { ?>
        <link rel="stylesheet" type="text/css" href="https://resource.kelvinho.org/assets/css/main.css">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta charset="utf-8">
        <!--suppress CssUnusedSymbol -->
        <style>
            .link {
                cursor: pointer;
                color: blue;
            }
        </style>
    <?php }

    /**
     * The scripts tag. This contains:
     * - jquery minified cdn
     * - javascript from 157239n.com
     */
    public static function scripts(): void { ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script type="text/javascript" src="https://157239n.com/page/assets/js/main.js"></script>
    <?php }
}
