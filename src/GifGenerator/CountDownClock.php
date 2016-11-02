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
    private $fixedWidth;
    private $offsets;

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

        // get fixed width
        $this->fixedWidth = $this->getWidth('0');

        // get offsets
        for ($index = 0; $index < 10; $index++) {
            $strIndex = (string) $index;
            $this->offsets[$strIndex] = $this->getOffset($strIndex);
        }
        $this->offsets[$this->clock->getSeparator()] = $this->getOffset($this->clock->getSeparator());
    }

    private function getOffset($char) {
        $width = $this->getWidth($char);
        return ($this->fixedWidth - $width) / 2;
    }

    private function getWidth($char) {
        $bbox = imagettfbbox($this->clock->getFontsize(), $this->clock->getFontangle(), $this->clock->getFontFilePath(), $char);
        return $bbox[2] - $bbox[0];
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

        $separator = $this->clock->getSeparator();

        //Count through our frames
        for ($i = 0; $i <= 60; $i++) {

            $interval = date_diff($dates->deadline, $dates->now);

            //If we're at or after the deadline - then just 0 the clock
            if ($dates->deadline < $dates->now) {
                $text = $interval->format(str_pad('0', $this->clock->getDaysLen(), '0') . $separator . '00' . $separator . '00' . $separator . '00');
                $loops = 1;
            }
            //Else format the interval and add a preceeding 0 if it's missing
            else {
                $days = '';
                if ($this->clock->getDaysLen() > 0) {
                    $days = str_pad($interval->days, $this->clock->getDaysLen(), '0', STR_PAD_LEFT);
                }
                $text = $interval->format($days . $separator . '%H' . $separator . '%I' . $separator . '%S');
                $loops = 0;
            }
            //create a new image resource
            $image = \imagecreatefrompng($this->clock->getBackgroundImageFilePath());

            //overlay the text on this resource
            $this->imagettftextSp($image, $this->clock->getFontsize(), $this->clock->getFontangle(), $this->clock->getFontx(), $this->clock->getFonty(), imagecolorallocate($image, $this->clock->getFontr(), $this->clock->getFontg(), $this->clock->getFontb()), $this->clock->getFontFilePath(), $text, $this->clock->getSpacing(), $this->clock->getSeparator(), $this->clock->getSeparatorSpacing());

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

    function imagettftextSp($image, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0, $separator = null, $separatorSpacing = 0) {
        if ($spacing == 0 && $separatorSpacing == 0) {
            imagettftext($image, $size, $angle, $x, $y, $color, $font, $text);
        } else {
            $thisX = $x;
            for ($i = 0; $i < strlen($text); $i++) {
                $thisX += $this->offsets[$text[$i]];
                imagettftext($image, $size, $angle, $thisX, $y, $color, $font, $text[$i]);
                $thisX -= $this->offsets[$text[$i]];
                $thisSpacing = $this->isSeperatorSpacing($text, $i, $separator) ? $separatorSpacing : $spacing;
                $thisX += $thisSpacing + $this->fixedWidth;
            }
        }
    }

    function isSeperatorSpacing($text, $i, $separator) {
        return $this->getNextChar($text, $i) == $separator || $text[$i] == $separator;
    }

    function getNextChar($text, $i) {
        return $i + 1 < strlen($text) ? $text[$i + 1] : null;
    }

}
