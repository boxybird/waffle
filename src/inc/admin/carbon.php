<?php

use Carbon_Fields\Field;
use Carbon_Fields\Container;

Container::make('theme_options', __('Waffle Options'))
    ->set_page_file('waffle-options.php')
    ->set_page_parent('tools.php')
    ->add_fields([
        Field::make('html', 'waggle_cache_form', )
            ->set_html('<h3>WIP</h3>'),
    ]);
