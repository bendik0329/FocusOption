<?php

/**
 * Class "DatesRange" (its method "create") 
 * will build different types of time-periods, 
 * within a specified range of dates.
 * An array will always be returned, but can be empty if an error occurred.
 * 
 * @author Anatoly Khaytovich
 */
class DatesRange
{
    const MODE_TYPE_MONTHLY     = 4;
    const MODE_TYPE_DAILY_RANGE = 3;
    const MODE_TYPE_DAILY       = 2;
    const MODE_TYPE_WEEKLY      = 1;
    const MODE_TYPE_NONE        = 0;
    
    /**
     * Private constructor used here in order to prevent an instance initialization.
     * 
     * @param void
     */
    private function __construct()
    { 
        // No code should be put here.
    }
    
    /**
     * Creates a dates-ranges monthly array.
     * 
     * @param  \DateTime $from
     * @param  \DateTime $to
     * @return array
     */
    private static function createMonthly(\DateTime $from, \DateTime $to)
    {
        $arrRanges = [];
        
        while (true) {
            if ($from->format('Y-m') == $to->format('Y-m')) {
                $arrRanges[] = [
                    'from' => $from->format('Y-m-d'),
                    'to'   => $to->format('Y-m-d 23:59:59'),
                ];
                break;
                
            } else {
                $arrRanges[] = [
                    'from' => $from->format('Y-m-d'),
                    'to'   => $from->format('Y-m-t 23:59:59'),
                ];
                
                $intYear  = $from->format('Y');
                $intMonth = $from->format('m');
                $intDay   = $from->format('t');
                $from     = $from->setDate($intYear, $intMonth, $intDay)->add(new \DateInterval('P1D'));
                unset($intYear, $intMonth, $intDay);
            }
        }
        
        return $arrRanges;
    }
    
    /**
     * Creates a dates-ranges weekly array.
     * 
     * @param  \DateTime $from
     * @param  \DateTime $to
     * @return array
     */
    private static function createWeekly(\DateTime $from, \DateTime $to)
    {
        $arrRanges = [];
        
        while (true) {
			//echo $from->format('Y-W') . " ---- " . $to->format('Y-W') ."<br/>";
            if ($from->format('Y-W') == $to->format('Y-W')) {
				$arrRanges[] = [
                    'from' => $from->format('Y-m-d'),
                    'to'   => $to->format('Y-m-d 23:59:59'), 
                ];
                break;
                
            } else {
				$cycleFrom = clone $from;                                  // Clone the '$from' object.
				//$from->setISODate($from->format('Y'), $from->format('W')); // Get a week of the given year.
                $cycleTo = $from->add(new \DateInterval('P0Y0M6D'));           // Get the last day of a calendar week.
                $arrRanges[] = [
                    'from' => $cycleFrom->format('Y-m-d'),
                    'to'   => $cycleTo->format('Y-m-d 23:59:59'),
                ];
                
                $from = $cycleTo->add(new \DateInterval('P1D')); // Continue to the next iteration with updated '$from' object.
                unset($cycleFrom, $cycleTo);                     // Clear up the memory.
            }
        }

       return $arrRanges;
    }
    
    /**
     * Creates a dates daily array.
     * 
     * @param  \DateTime $from
     * @param  \DateTime $to
     * @param  bool      $isRange
     * @return array
     */
    private static function createDaily(\DateTime $from, \DateTime $to, $isRange = false)
    {
        $arrRanges = [];
        
        while (true) {
            if ($from->format('Y-m-d') == $to->format('Y-m-d')) {
                $arrRanges[] = $isRange 
                         ? ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d 23:59:59'),]
                         : ['date' => $from->format('Y-m-d'),];
                
                break;
                
            } else {
                $arrRanges[] = $isRange 
                         ? ['from' => $from->format('Y-m-d'), 'to' => $from->format('Y-m-d 23:59:59'),]
                         : ['date' => $from->format('Y-m-d'),];
                
                $from = $from->add(new \DateInterval('P1D'));
            }
        }
        
        return $arrRanges;
    }
    
    /**
     * Creates a dates-ranges array.
     * 
     * @param  string $strFrom
     * @param  string $strTo
     * @param  int    $intMode
     * @return array
     */
    public static function create($strFrom, $strTo, $intMode = 0)
    {
        try {
            $from = new \DateTime($strFrom);
            $to   = new \DateTime($strTo);
            
            if ($from > $to) {
                $temp = $from;
                $from = $to;
                $to   = $temp;
                unset($temp);
            }
            
            switch ($intMode) {
                case self::MODE_TYPE_MONTHLY:
                    return self::createMonthly($from, $to);
                case self::MODE_TYPE_WEEKLY:
                    return self::createWeekly($from, $to);
                case self::MODE_TYPE_DAILY_RANGE:
                    return self::createDaily($from, $to, true);
                case self::MODE_TYPE_DAILY:
                    return self::createDaily($from, $to, false);
                default:
                    return [
                        [
                            'from' => $from->format('Y-m-d'),
                            'to'   => $to->format('Y-m-d 23:59:59'),
                        ]
                    ];
            }
            
        } catch (\Exception $e) {
            return [];
        }
    }
}