<?php

namespace Kelvinho\Virus;

class HtmlTemplate {
    /**
     * The header (no <head> included. This contains:
     * - Css from 157239n.com
     * - Css from w3school.com
     * - Viewports
     * - .link classes have pointer cursor and their color blue
     *
     * @return string The html code to embed in
     */
    public static function header(): string {
        ob_start(); ?>
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
        <?php return ob_get_clean();
    }
}
