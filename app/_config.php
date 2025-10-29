<?php

use Bigfork\Vitesse\Vite;
use SilverStripe\Control\Director;
use SilverStripe\Core\Config\Config;
use SilverStripe\i18n\i18n;
use SilverStripe\TinyMCE\TinyMCEConfig;

// Set the site locale
i18n::set_locale('en_GB');

// TinyMCE Config
$config = TinyMCEConfig::get('cms');
$config->disablePlugins(['importcss', 'contextmenu']);
$config->enablePlugins(['anchor']);
$config->setButtonsForLine(1, 'blocks styles | bullist numlist | bold italic subscript superscript |
    sslink unlink anchor ssmedia ssembed hr');
$config->setButtonsForLine(2, 'table | pastetext undo redo | code');
$config->setOptions([
    'block_formats' => 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6',
    'table_advtab' => false,
    'table_appearance_options' => false,
    'table_cell_advtab' => false,
    'table_default_styles' => [
        'width' => '100%'
    ],
    'preview_styles' => 'line-height font-family font-size font-weight font-style text-decoration text-transform color background-color border border-radius outline text-shadow',
    'style_formats' => [
        [
            'title' => 'Text sizes',
            'items' => [
                [
                    'title' => 'Small',
                    'selector' => 'p, h1, h2, h3, h4, h5, h6',
                    'classes' => 'font-sm',
                ],
                [
                    'title' => 'Regular',
                    'selector' => 'p, h1, h2, h3, h4, h5, h6',
                    'classes' => 'font-rg',
                ],
                [
                    'title' => 'Medium',
                    'selector' => 'p, h1, h2, h3, h4, h5, h6',
                    'classes' => 'font-md',
                ],
                [
                    'title' => 'Large',
                    'selector' => 'p, h1, h2, h3, h4, h5, h6',
                    'classes' => 'font-lg',
                ],
                [
                    'title' => 'Extra large',
                    'selector' => 'p, h1, h2, h3, h4, h5, h6',
                    'classes' => 'font-xl',
                ],
            ],
        ],
    ],
    'style_formats_autohide' => true
]);

$editorCSS = Config::inst()->get(TinyMCEConfig::class, 'editor_css');
$editorPath = Vite::inst()->asset('src/scss/editor.scss');
if (!Vite::inst()->isRunningHot()) {
    $editorPath = Director::makeRelative($editorPath);
}
$editorCSS[] = $editorPath;
Config::modify()->set(TinyMCEConfig::class, 'editor_css', $editorCSS);
