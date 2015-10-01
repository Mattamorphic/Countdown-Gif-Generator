<?php

namespace GifGenerator;

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
 * */
class CountDownClock {

    private $dates;
    private $clock;

    /**
     * Constructor takes clock object as argument
     * @param   $clock  ClockInterface
     * */
    public function __construct(ClockInterface $clock) {
        //Convert the deadline to a date time and assign top property
        $this->dates = new \stdClass();
        $this->dates->deadline = $clock->getdeadlineDateTime();
        $this->dates->deadline->setTimeZone(new \DateTimeZone($clock->getTimeZone()));

        $this->dates->now = new \DateTime(date('r', time()));
        $this->dates->now->setTimeZone(new \DateTimeZone($clock->getTimeZone()));

        $this->clock = $clock;
    }

    /**
     *  Generates the image using the given design settings and the GifEncoder plugin
     * */
    public function generateClock() {
        //Some overall variables
        $frames = [];
        $delays = [];
        $delay = 100;

        //Getting some data from the properties
        $dates = $this->dates;
        //Count through our frames
        for ($i = 0; $i <= 60; $i++) {
            $interval = date_diff($dates->deadline, $dates->now);

            $seperator = $this->clock->getSeperator();
            //If we're at or after the deadline - then just 0 the clock
            if ($dates->deadline < $dates->now) {
                $text = $interval->format('00' . $seperator . '00' . $seperator . '00' . $seperator . '00');
                $loops = 1;
            }
            //Else format the interval and add a preceeding 0 if it's missing
            else {
		$days = '';
                if ($this->clock->getDaysLen() > 0) {
                    $days = str_pad($interval->d, $this->clock->getDaysLen(), '0', STR_PAD_LEFT);
                }
                $text = $interval->format($days . $seperator . '%H' . $seperator . '%I' . $seperator . '%S');
                $text = (preg_match('/^[0-9]\:/', $text)) ? '0' . $text : $text;
                $loops = 0;
            }
            //create a new image resource
            $image = \imagecreatefrompng($this->clock->getBackgroundImageFilePath());

            //overlay the text on this resource
            imagettftextSp($image, $this->clock->getFontsize(), $this->clock->getFontangle(), $this->clock->getFontx(), $this->clock->getFonty(), imagecolorallocate($image, $this->clock->getFontr(), $this->clock->getFontg(), $this->clock->getFontb()), $this->clock->getFontFilePath(), $text, 23);

            function imagettftextSp($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0) {
                if ($spacing == 0) {
                    imagettftext($image, $size, $angle, $x, $y, $color, $font, $text);
                } else {
                    $temp_x = $x;
                    for ($i = 0; $i < strlen($text); $i++) {
                        $bbox = imagettftext($image, $size, $angle, $temp_x, $y, $color, $font, $text[$i]);
                        $temp_x += $spacing + ($bbox[2] - $bbox[0]);
                    }
                }
            }

            //buffer...
            ob_start();
            imagegif($image);
            $frames[] = ob_get_contents();
            $delays[] = $delay;
            ob_end_clean();
            //if we're after the deadline - break
            if ($dates->deadline < $dates->now)
                break;
            //else add a second and the next frame
            $dates->now->modify('+1 second');
        }
        //expire this image instantly
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        //generate a new GIF given the frames, the delay and the loop counter
        $gif = new AnimatedGif($frames, $delays, $loops);
        $gif->display();
    }

}
