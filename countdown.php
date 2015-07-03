<?php
require('./lib/GifEncoder.php');
/**
  * Countdown gif generator
  * @license : BSD
  *
  * @category : Image production
  * @package : CountdownClock
  * @version : 1.0
  *
  * @author : Matt Barber
  * @created : 3rd July 2015
  * @updated : 3rd July 2015
**/


  class CountDownClock{
    private $dates;
    private $design;

    /**
     * Constructor takes in the deadline, a timezone and a design object as arguments
     * @param   $deadline     string    String of the deadline date time
     * @param   $timezone     string    String of a valid timezone
     * @param   $design       object    Object representing design aspects
    **/
    public function __construct($deadline, $timezone, $design){
      //Convert the deadline to a date time and assign top property
      $this->dates = new stdClass();
      $this->dates->deadline = new DateTime(date('r', strtotime($deadline)));
      $this->dates->deadline->setTimeZone(new DateTimeZone($timezone));

      $this->dates->now = new DateTime(date('r', time()));
      $this->dates->now->setTimeZone(new DateTimeZone($timezone));


      $this->design = $design;
    }
    /**
     *  Generates the image using the given design settings and the GifEncoder plugin
    **/
    public function generateImage(){
      //Some overall variables
      $frames = [];
      $delays = [];
      $delay = 100;

      //Getting some data from the properties
      $dates = $this->dates;
      $settings = $this->design->image;

      //Count through our frames
      for($i = 0; $i <= $settings->frames; $i++){
        $interval = date_diff($dates->deadline, $dates->now);
        //If we're at or after the deadline - then just 0 the clock
        if($dates->deadline < $dates->now){
          $text = $interval->format('00:00:00:00');
          $loops = 1;
        }
        //Else format the interval and add a preceeding 0 if it's missing
        else{
          $text = $interval->format('%a:%H:%I:%S');
          $text = (preg_match('/^[0-9]\:/', $text)) ? '0'.$text : $text;
          $loops = 0;
        }
        //create a new image resource
        $image = @imagecreate($settings->width, $settings->height);

        $background = $this->design->background;
        $font = $this->design->font;

        //fill the background
        imagefill($image, 0, 0, imagecolorallocate( $image,
                                                    $background->colour->r,
                                                    $background->colour->g,
                                                    $background->colour->b));
        //overlay the text on this resource
        imagettftext( $image,
                      $font->size,
                      0,
                      $settings->font_x,
                      $settings->font_y,
                      imagecolorallocate($image, $font->colour->r, $font->colour->g, $font->colour->b),
                      $font->font,
                      $text);
        //buffer...
        ob_start();
        imagegif($image);
        $frames[] = ob_get_contents();
        $delays[] = $delay;
        ob_end_clean();
        //if we're after the deadline - break
        if($dates->deadline < $dates->now) break;
        //else add a second and the next frame
        $dates->now->modify('+1 second');
      }
      //expire this image instantly
      header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
      header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
      header( 'Cache-Control: no-store, no-cache, must-revalidate' );
      header( 'Cache-Control: post-check=0, pre-check=0', false );
      header( 'Pragma: no-cache' );
      //generate a new GIF given the frames, the delay and the loop counter
      $gif = new AnimatedGif($frames, $delays, $loops);
      $gif->display();
    }
  }
?>
