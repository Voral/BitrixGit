<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

return [
    'js' => [
        './dist/vasoft-git.bundle.js',
    ],
    'css' => [
        './dist/vasoft-git.bundle.css',
    ],
    'rel' => [
		'main.polyfill.core',
		'ui.vue3',
	],
    'skip_core' => true,
];
