# srcset

SrcSet is intended for use as an output modifier to easily generate a 1x and 2x version of images using the srcset attribute.

2x image should only be generated when the original image meets the minimum ratio requirements (see system settings, defaults to 2)

## ImagePlus

SrcSet aims to support ImagePlus, so far everything I've tried with ImagePlus and srcset works as expected, but if you find any bug please let me know

## Usage

```html
<img src="[[*tvName:srcset=`w=200`]]">
<img src="[[*tvName:srcset=`w=200&h=100&zc=1`]]">
<img src="[[srcset? &image=`path/to/iamge.jpg` &w=`200`]]">
```

Author: Jason <jason@dashmedia.com.au>
Copyright 2017

Official Documentation: https://github.com/DashMedia/srcset

Bugs and Feature Requests: https://github.com/DashMedia/srcset/issues