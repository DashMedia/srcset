<?php
/**
 * @name srcset
 * @description srcset
 *
 * USAGE
 *
 *  [[*tvName:srcset=`w=200&h=100&zc=1`]]
 *  [[*tvName:srcset=`w=200`]]
 *
 * Variables
 * ---------
 * @var string $input image path
 * @var string $options a phpThumb options string
 * @var modX $modx
 * @var array $scriptProperties
 *
 *
 * @package srcset
 */
// Your core_path will change depending on whether your code is running on your development environment
// or on a production environment (deployed via a Transport Package).  Make sure you follow the pattern
// outlined here. See https://github.com/craftsmancoding/repoman/wiki/Conventions for more info
// Only do the work if there is actually a file input
if(!empty($input)){
    $core_path = $modx->getOption('srcset.core_path', null, MODX_CORE_PATH.'components/srcset/');
    include_once $core_path .'/vendor/autoload.php';
    $srcset = new SrcSet($modx, $scriptProperties);
    return $srcset->getSrc();
}else{
    return $input;
}
