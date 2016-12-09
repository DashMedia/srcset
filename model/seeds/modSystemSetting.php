<?php
/*-----------------------------------------------------------------
 * Lexicon keys for System Settings follows this format:
 * Name: setting_ + $key
 * Description: setting_ + $key + _desc
 -----------------------------------------------------------------*/
return array(

    array(
        'key'  		=>     'srcset.thumbnail_snippet',
		'value'		=>     'phpThumbOf',
		'xtype'		=>     'textfield',
		'namespace' => 'srcset',
		'area' 		=> 'srcset:default'
    ),
    array(
        'key'  		=>     'srcset.min_2x_ratio',
        'value'		=>     '2',
        'xtype'		=>     'textfield',
        'namespace' => 'srcset',
        'area' 		=> 'srcset:default'
    ),
    array(
        'key'  		=>     'srcset.compression_1x',
        'value'		=>     '90',
        'xtype'		=>     'textfield',
        'namespace' => 'srcset',
        'area' 		=> 'srcset:default'
    ),
    array(
        'key'  		=>     'srcset.compression_2x',
        'value'		=>     '40',
        'xtype'		=>     'textfield',
        'namespace' => 'srcset',
        'area' 		=> 'srcset:default'
    ),
);
/*EOF*/