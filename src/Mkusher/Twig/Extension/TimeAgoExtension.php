<?php
/**
 * This file is part of the sm package.
 *
 * (c) Aleh Kashnikau <aleh.kashnikau@gmail.com>
 *
 * Created: 18/02/2014 8:33 PM
 */

namespace Mkusher\Twig\Extension;

use Symfony\Component\Translation\IdentityTranslator;
use Mkusher\Date\Distance;
use \DateTime;
use \Twig_SimpleFilter;
use \Twig_Extension;
use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;
use Symfony\Component\Intl\DateFormatter\IntlDateFormatter;

class TimeAgoExtension extends Twig_Extension {
    protected $translator;
    /** @var DateTimeHelper */
    protected $datetimeformater;

    /**
     * Constructor method
     *
     * @param IdentityTranslator $translator
     */
    public function __construct($translator, $formater)
    {
        $this->translator = $translator;

        $this->datetimeformater = $formater;
    }

    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('distance_of_time_in_words', array($this, 'distanceOfTimeInWordsFilter')),
            new Twig_SimpleFilter('time_ago_in_words', array($this, 'timeAgoInWordsFilter')),
            new Twig_SimpleFilter('ago', array($this, 'timeAgoInWordsFilter')),
        );
    }

    /**
     * Like distance_of_time_in_words, but where to_time is fixed to timestamp()
     *
     * @param $from_time String or DateTime
     * @param bool $include_seconds
     * @param bool $include_months
     *
     * @return mixed
     */
    function timeAgoInWordsFilter($from_time, $include_seconds = true, $include_months = false)
    {
        return $this->distanceOfTimeInWordsFilter($from_time, new \DateTime('now'), $include_seconds, $include_months);
    }

    /**
     * Reports the approximate distance in time between two times given in seconds
     * or in a valid ISO string like.
     * For example, if the distance is 47 minutes, it'll return
     * "about 1 hour". See the source for the complete wording list.
     *
     * Integers are interpreted as seconds. So, by example to check the distance of time between
     * a created user an it's last login:
     * {{ user.createdAt|distance_of_time_in_words(user.lastLoginAt) }} returns "less than a minute".
     *
     * Set include_seconds to true if you want more detailed approximations if distance < 1 minute
     * Set include_months to true if you want approximations in months if days > 30
     *
     * @param $from_time String or DateTime
     * @param $to_time String or DateTime
     * @param bool $include_seconds
     * @param bool $include_months
     *
     * @return mixed
     */
    public function distanceOfTimeInWordsFilter($from_time, $to_time = null, $include_seconds = true, $include_months = false)
    {
        $distance = $this->getDistance($from_time, $to_time);
        $message = $this->prepareMessage($distance, $include_seconds, $include_months);
        return $message;
    }


    protected function prepareMessage(Distance $distance, $include_seconds, $include_months)
    {
        $distance_in_minutes = $distance->getMinutes();
        $distance_in_seconds = $distance->getSeconds();

        if ($distance_in_seconds <= 60){
            if ($include_seconds){
                if($distance_in_seconds < 20){
                    return $this->translator->transchoice('less than %seconds seconds ago', $distance_in_seconds, array('%seconds' => $distance_in_seconds));
                }
                elseif($distance_in_seconds < 40){
                    return $this->translator->trans('half a minute ago');
                }
                elseif($distance_in_seconds < 60){
                    return $this->translator->trans('less than a minute ago');
                }
                else {
                    return $this->translator->trans('1 minute ago');
                }
            }
            return ($distance_in_minutes===0) ? $this->translator->trans('less than a minute ago', array()) : $this->translator->trans('1 minute ago', array());
        }
        elseif ($distance_in_minutes <= 45){
            return $this->translator->transchoice('%minutes minutes ago', $distance_in_minutes, array('%minutes' => $distance_in_minutes));
        }
        elseif ($distance_in_minutes <= 90){
            return $this->translator->trans('about 1 hour ago');
        }
        elseif ($distance_in_minutes <= 240){
            return $this->translator->transchoice('about %hours hours ago', $distance->getHours(), array('%hours' => $distance->getHours()));
        }
        elseif ($distance->isToday()){
            return $this->translator->trans('today at %time', array(
                '%time' =>  $this->datetimeformater->formatTime($distance->getFrom(), null, null, IntlDateFormatter::SHORT)
                ));
        }
        elseif ($distance->isYesterday()){
            return $this->translator->trans('yersterday at %time', array(
                '%time' =>  $this->datetimeformater->formatTime($distance->getFrom(), null, null, IntlDateFormatter::SHORT)
            ));
        }
        elseif ($distance->isThisYear()){
            return $this->translator->trans('%date at %time', array(
                '%time' =>  $this->datetimeformater->formatTime($distance->getFrom(), null, null, IntlDateFormatter::SHORT),
                '%date' => $this->datetimeformater->format($distance->getFrom(), "d LLL")
            ));
        }
        else
            return $this->translator->trans('%date', array(
                '%date' => $this->datetimeformater->format($distance->getFrom(), "d LLL yyyy")
            ));
    }

    protected function getDistance($from_time, $to_time)
    {
        $distance = new Distance($from_time, $to_time);
        return $distance;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'time_ago_extension';
    }
} 