#Countdown Clock Generator (GIF) PHP

##Description
Generates a GIF of a countdown clock when the end point is pinged using the parameters given under "using the generator". Useful for Websites and/or Emails.

##Specification

Runs on PHP5.5+ using the GIFEncoder Version 2.0 by László Zsidi

##Using the Generator

```
  Implement ClockInterface then:

  $clockItf = new Clock();
  // set $clock params ..
  $countdown = new CountDownClock($clockItf);
  //Call specifically (incase any further changes)
  $clock->generateImage();
?>
```

# Example #
There is a runnable example in the example folder called clock.php - this can be run off of the back of PHP's builtin web server


*Licence:* MIT

##Contributors

- https://github.com/tom-power
- https://github.com/matt-barber


# Changelog

04/11/2016 - v1.0.0

A lot of breaking changes so please look at the example if you are coming from an older iteration of this project

- Merged in contributions
- Refactored folder structure
- Added autoloading / PSR-4
- Coding Standards
- Comments
- Added support for no background images
- Built example
