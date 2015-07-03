#Countdown Clock Generator (GIF) PHP

##Description
Generates a GIF of a countdown clock when the end point is pinged using the parameters given under "using the generator". Useful for Websites and/or Emails.

##Specification

Runs on PHP5.5+ using the GIFEncoder Version 2.0 by László Zsidi

##Using the Generator

At the endpoint you'd like to use simply add the following PHP
```php
<?php
  require(countdown.php);
  //Holds the design - This could be either hard coded or read in from a URL / resource

  $design = [
    'font' => [
        'font' => './lib/NotoSans-Regular.ttf',
        'colour' => [
                      'r'=>'60',
                      'g'=>'147',
                      'b'=>'147'],
        'size' => 40,
        'format' => '%a:%H:%I:%S'
      ],
    'background' => [
        'colour' => [
                      'r'=>'39',
                      'g'=>'69',
                      'b'=>'94'],
      ],
    'image' => [
        'height' => 100,
        'width'  => 600,
        'frames' => 60,
        'font_x' => 150,
        'font_y' => 70
      ]
  ];
  $design = json_decode(json_encode($design), false);
  //Usually again, from a hard resource or a web request parameter
  $deadline = 'tomorrow';
  //Instantiate
  $clock = new CountDownClock($deadline, 'Europe/London', $design);
  //Call specifically (incase any further changes)
  $clock->generateImage();
?>
```
*Licence:* BSD
