#Countdown Clock Generator (GIF) PHP

##Description
Generates a GIF of a countdown clock when the end point is pinged using the parameters given under "using the generator". Useful for Websites and/or Emails.

##Specification

Runs on PHP5.5+ using the GIFEncoder Version 2.0 by László Zsidi, Fork of https://github.com/Matt-Barber/Countdown-Gif-Generator

##Using the Generator

  Implement ClockInterface then:
   
  $clockItf = new Clock();
  // set $clock params ..
  $countdown = new CountDownClock($clockItf);
  //Call specifically (incase any further changes)
  $clock->generateImage();
?>
```
*Licence:* BSD

##Contributors

https://github.com/tom-power
https://github.com/matt-barber
