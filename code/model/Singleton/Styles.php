<?php

namespace Kelvinho\Virus\Singleton;

class Styles {
    public static function all($darkMode) {
        Styles::basics($darkMode);
        Styles::demo($darkMode);
        Styles::toast($darkMode);
        Styles::topBar($darkMode);
        Styles::w3Overrides($darkMode);
        Styles::scrollBars($darkMode);
    }

    private static function basics($darkMode) { ?>
        <!--suppress CssOverwrittenProperties -->
        <style>
            :root {
                --btnBorderRadius: 8px;
                --txtBorderRadius: 3px;
                --smooth: 200ms;
                --translucent: 0.2;
                --translucentColor: #ccc; /* for elements that has a translucent layer on top of it */
                --surface: #fff;
                --surface2: #ccc;
            <?php if ($darkMode) { ?> --bg: #000;
                --surface: #121212;
                --surfaceFocused: #222222;
                --surface2: #222;
                --surface2Focused: #333;
                --text: #fff;
            <?php } ?>
            }

            .link {
                cursor: pointer;
                color: blue;
            }

            .w3-table td {
                vertical-align: inherit;
            }

            body {
                padding: 0 7% 6vh;
                text-align: justify;
                font-family: "Open Sans", serif;
                line-height: 1.5;
            }

            h2 {
                color: #616161;
            }

            a {
                text-decoration: none;
            }

            table tr {
                transition: background-color var(--smooth);
            }

            .imgSmall {
                width: 50%;
            }

            .imgMedium {
                width: 70%;
            }

            .imgLarge {
                width: 100%;
            }

            @media only screen and (max-width: 1000px) {
                .imgSmall {
                    width: 70%;
                }

                .imgMedium {
                    width: 100%;
                }
            }

            @media only screen and (max-width: 500px) {
                .imgSmall {
                    width: 100%;
                }
            }

            <?php if ($darkMode) { ?>
            body {
                background-color: var(--bg);
            }

            p, .p, label, .table-heads, table tr, textarea, input[type="text"], select, .menuStreamSaved, pre {
                color: var(--text) !important;
            }

            h2 {
                color: #d9e2ec;
            }

            h3, h5 {
                color: #bcccdc;
            }

            ul, li {
                color: var(--text);
            }

            .link {
                color: #486581;
            }

            table tr, .table-heads, textarea, input[type="text"], select, pre {
                background-color: var(--surface) !important;
            }

            table tr:not(.w3-white):hover {
                background-color: var(--surfaceFocused) !important;
            }

            <?php } ?>
        </style>
    <?php }

    private static function demo($darkMode) { ?>
        <style>
            .demoNav {
                position: absolute;
                float: left;
                width: 5%;
                height: 100%;
                cursor: pointer;
                opacity: 0;
                background-color: var(--translucentColor);
                transition: opacity var(--smooth);
            }

            .demoNav:hover {
                opacity: var(--translucent);
            }

            .demoContent {
                float: left;
                display: none;
                width: 90%;
                left: 5%;
            }
        </style>
    <?php }

    private static function toast($darkMode) { ?>
        <style>
            #toast {
                position: fixed;
                max-width: 40vw;
                top: 70vh;
                left: 50vw;
                transform: translateX(-50%);
                background-color: #f0e68cff;
                opacity: 0;
                color: #111111ff;
                transition: opacity 0.3s;
                text-align: center;
                padding: 8px 30px;
                font-size: 1.5em;
                z-index: 50000;
                border-radius: 32px;
            }

            @media only screen and (max-width: 600px) {
                #toast {
                    top: unset;
                    max-width: unset;
                    transform: unset;
                    border-radius: unset;
                    font-size: 1em;
                    width: 100vw;
                    bottom: 0;
                    left: 0;
                }
            }

            #toast.activated {
                opacity: 1;
            }
        </style>
    <?php }

    private static function topBar($darkMode) { ?>
        <style>
            #topBar > div > div > a, #topBar > .w3-bar-item > a, #topBar > a.w3-bar-item, #topBar > .w3-dropdown-hover {
                transition: background-color var(--smooth);
            }

            <?php if ($darkMode) { ?>

            #topBar, #topBar > .w3-bar-item, #topBar > .w3-bar-item > a, #topBar > .w3-bar-item > div > a {
                color: var(--text) !important;
            }

            #topBar, #topBar > div > div > a {
                background-color: var(--surface2) !important;
            }

            #topBar > div > div > a:hover, #topBar > .w3-bar-item > a:hover, #topBar > a.w3-bar-item:hover, #topBar > .w3-dropdown-hover:hover {
                background-color: var(--surface2Focused) !important;
            }

            <?php } ?>
        </style>
    <?php }

    private static function w3Overrides($darkMode) { ?>
        <style>
            .w3-btn {
                border-radius: var(--btnBorderRadius);
                transition: box-shadow var(--smooth);
            }

            .table-round {
                border-radius: var(--btnBorderRadius);
            }

            textarea, input[type="text"], select {
                border-radius: var(--txtBorderRadius);
            }

            <?php if ($darkMode) { ?>

            .w3-bordered tr, textarea, input[type="text"], select {
                border-bottom: 1px solid #333 !important;
            }

            .w3-border {
                border: 1px solid #333 !important;
            }

            .w3-btn:hover {
                box-shadow: 0 0 20px 0 rgba(255, 255, 255, 0.3);
            }

            .w3-card {
                box-shadow: 0 0 10px 0 rgba(255, 255, 255, 0.3);
            }

            .w3-card-4 {
                box-shadow: 0 0 20px 0 rgba(255, 255, 255, 0.3);
            }

            <?php } ?>
        </style>
    <?php }

    private static function scrollBars($darkMode) { ?>
        <style>
            body::-webkit-scrollbar {
                width: 8px;
            }

            body::-webkit-scrollbar-track {
                background-color: #CFD8DC;
            }

            body::-webkit-scrollbar-thumb {
                background-color: #90A4AE;
            }

            <?php if ($darkMode) { ?>
            body::-webkit-scrollbar-track {
                background-color: #333;
            }

            body::-webkit-scrollbar-thumb {
                background-color: #666;
            }

            <?php } ?>
        </style>
        <?php
    }
}