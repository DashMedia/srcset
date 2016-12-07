<?php

/**
 * Created by PhpStorm.
 * User: jasoncarney
 * Date: 7/12/16
 * Time: 4:37 PM
 *
 */
class SrcSet
{

    /**
     * @var modX $modx
     */
    private $modx;
    /**
     * @var array $scriptProperties snippet script properties
     */
    private $scriptProperties;
    /**
     * @var string $tvName Name of TV holding Image path
     */
    private $tvName;
    /**
     * @var int $id The Doc ID of the page holding the tv value
     */
    private $id;
    /**
     * @var string $input File path to image
     */
    private $input;
    /**
     * @var string $output Final src value containing srcset values as well
     */
    private $output;
    /**
     * @var array $processedOptions array of values from $options string
     */
    private $processedOptions;

    /**
     * SrcSet constructor.
     * @param modX $modx
     * @param array $scriptProperties
     */
    public function __construct($modx, $scriptProperties)
    {
        $this->modx = $modx;
        $this->scriptProperties = $scriptProperties;

        $this->processOptions();
    }

    /**
     * @return string The src value containing srcset attributes for the given image
     */
    public function getSrc(){
        $srcString = '';
        return $srcString;
    }

    /**
     * @description process scriptProperties into usage variables
     */
    private function processOptions(){
        //first grab name based on the name of the TV
        $this->tvName = $this->modx->getOption('name', $this->scriptProperties);
        //check if tv name has been manually passed, overrides assumed name
        $this->tvName = $this->modx->getOption('tvName', $this->scriptProperties, $this->tvName);

        //current resource ID
        $this->id = $this->modx->resource->get('id');
        //check if ID has been manually passed, overrides assumed ID
        $this->id = $this->modx->getOption('id', $this->scriptProperties,$this->id );

        //check if image has been explicitly set
        $this->input = $this->modx->getOption('image', $this->scriptProperties);
        //override with input value (output modifier usage)
        $this->input = $this->modx->getOption('input', $this->scriptProperties, $this->input);

        //default options
        $this->processedOptions = array(
            'q' => 90
        );

        $options = $this->modx->getOption('options', $this->scriptProperties);
        $options = explode('&', $options);
        foreach ($options as $option) {
            $vars = explode('=',$option);
            if(!empty($vars[0]) && !empty($vars[1])){
                $this->processedOptions[$vars[0]] = $vars[1];
            }
        }

    }
}