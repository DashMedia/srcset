<?php

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
     * @var array $imageSize[0] = width, $imageSize[1] = height
     */
    private $imageSize;
    private $thumbSnippet;

    private $min2xRatio;
    private $compression1x;
    private $compression2x;

    /**
     * SrcSet constructor.
     * @param modX $modx
     * @param array $scriptProperties
     */
    public function __construct($modx, $scriptProperties)
    {
        $this->modx = $modx;
        $this->scriptProperties = $scriptProperties;
        $this->thumbSnippet = $modx->getOption('srcset.thumbnail_snippet');
        $this->min2xRatio = floatval($modx->getOption('srcset.min_2x_ratio'));
        $this->compression1x = intval($modx->getOption('srcset.$compression1x'));
        $this->compression2x = intval($modx->getOption('srcset.$compression2x'));
    }

    /**
     * @return string The src value containing srcset attributes for the given image
     */
    public function getSrc(){
        $srcStrings = null;
        $output = '';
        $this->processOptions();
        if(empty($this->processedOptions['w'])&& empty($this->processedOptions['h'])){
            // no valid reference dimension, return original
            return $this->input;
        }

//        $this->imageSize = getimagesize($this->input);

        //check if we're dealing with an imagePlus tv
        if(!empty($this->tvName)){
            $inputTv = $this->modx->getObject('modTemplateVar', array('name'=>$this->tvName));

            if(!empty($inputTv) && $inputTv->get('type') == 'imageplus'){
                 $srcStrings = $this->processImagePlus($inputTv);
            }
        }

        if(!is_array($srcStrings)){
            $srcStrings = $this->processImageString();
        }

        foreach($srcStrings as &$imageString){
          $imageString = str_replace(' ', '%20', $imageString);
        }

        //markup contains leading and trailing double quotes
        $output = $srcStrings['x1'];
        if(isset($srcStrings['x2'])){
            $output .= "\" srcset=\"{$srcStrings['x2']} 2x";
        }
        return $output;
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

        $this->input = ltrim($this->input, '/\\');
        //default options
        $this->processedOptions = array(
            'q' => $this->compression1x
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


    /**
     * @param modTemplateVar $inputTv
     * @return array|null SrcStrings
     */
    private function processImagePlus($inputTv){
        $output = null;
        $tvValue = json_decode($inputTv->getValue($this->id));
        if(!empty($tvValue) && !empty($tvValue->sourceImg->src)){
            //valid image+ variable
            $output = array();
            $this->imageSize = array(
                $tvValue->crop->width,
                $tvValue->crop->height
            );
            $imagePlusOptions = array(
                'tvname'=> $inputTv->get('name'),
                'type'=>'thumb',
                'docid'=>$this->id
            );
            $imagePlusOptions['options'] = $this->getOptionStrings();
            $output['x1'] = $this->modx->runSnippet('ImagePlus', $imagePlusOptions);
            if($this->is2x()){
                $options2x = $this->get2xOptions();
                $imagePlusOptions['options'] = $this->getOptionStrings($options2x);

                $output['x2'] = $this->modx->runSnippet('ImagePlus', $imagePlusOptions);
            }
        }
        return $output;
    }

    private function processImageString(){
        $srcStrings = array();

        $this->imageSize = getimagesize($this->input);
        $snippetOptions = array(
            'input' => $this->input
        );

        $optionStrings = $this->getOptionStrings();

        $snippetOptions['options'] = $optionStrings;

        $srcStrings['x1'] = $this->modx->runSnippet($this->thumbSnippet, $snippetOptions);

        if($this->is2x()){
            $options2x = $this->get2xOptions();
            $optionStrings = $this->getOptionStrings($options2x);

            $snippetOptions['options'] = $optionStrings;

            $srcStrings['x2'] = $this->modx->runSnippet($this->thumbSnippet, $snippetOptions);
        }

        return $srcStrings;
    }

    private function get2xOptions(){
        $options = $this->processedOptions;
        if(isset($options['w'])){
            $options['w'] *= 2;
        }
        if(isset($options['h'])){
            $options['h'] *= 2;
        }
        $options['q'] = $this->compression2x;
        return $options;
    }

    private function getOptionStrings($options = null){
        if(is_null($options)){
            $options = $this->processedOptions;
        }
        $optionStrings = array();
        foreach ($options as $option => $value) {
            $optionStrings[] = "{$option}={$value}";
        }
        return implode('&',$optionStrings);
    }

    private function is2x(){
        $is2x = true;
        if(!empty($this->processedOptions['w'])){
            $this->processedOptions['w'] = intval($this->processedOptions['w']);
            if($this->imageSize[0] / $this->processedOptions['w'] < $this->min2xRatio){
                $is2x = false;
            }
        }
        if(!empty($this->processedOptions['h'])){
            $this->processedOptions['h'] = intval($this->processedOptions['h']);
            if($this->imageSize[1] / $this->processedOptions['h'] < $this->min2xRatio){
                $is2x = false;
            }
        }
        return $is2x;
    }
}