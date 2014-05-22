<?php
/**
 * This script parses my time-sheet text file & transform it to remdine time records
 *
 * Examples:
 *  $ /path/to/times2redmine.php SW52aXTDqXM=
 *  $ /path/to/times2redmine.php < file.b64
 *
 * Last commit of this file (GMT):
 * $Id$
 *
 * PHP version 5.3
 *
 * @category  PHP
 * @package   PhpTests
 * @author    Julien Moreau <jmoreau@pixeye.net>
 * @copyright 2013 VeePee
 * @license   http://choosealicense.com/licenses/no-license/ No license
 * @version   GIT: $Revision$
 * @link      https://github.com/PixEye/php-tests
 * @since     2013-09-30
 * @filesource
 */

/**
 * mysql> desc time_entries;
 * +-------------+--------------+------+-----+---------+----------------+
 * | Field       | Type         | Null | Key | Default | Extra          |
 * +-------------+--------------+------+-----+---------+----------------+
 * | id          | int(11)      | NO   | PRI | NULL    | auto_increment |
 * | project_id  | int(11)      | NO   | MUL | NULL    |                |
 * | user_id     | int(11)      | NO   | MUL | NULL    |                |
 * | issue_id    | int(11)      | YES  | MUL | NULL    |                |
 * | hours       | float        | NO   |     | NULL    |                |
 * | comments    | varchar(255) | YES  |     | NULL    |                |
 * | activity_id | int(11)      | NO   | MUL | NULL    |                |
 * | spent_on    | date         | NO   |     | NULL    |                |
 * | tyear       | int(11)      | NO   |     | NULL    |                |
 * | tmonth      | int(11)      | NO   |     | NULL    |                |
 * | tweek       | int(11)      | NO   |     | NULL    |                |
 * | created_on  | datetime     | NO   | MUL | NULL    |                |
 * | updated_on  | datetime     | NO   |     | NULL    |                |
 * +-------------+--------------+------+-----+---------+----------------+
 * 13 rows in set
 */

/**
 * RedmineTimeEntry
 *
 * @category PHP
 * @package  PhpTests
 * @author   Julien Moreau <jmoreau@pixeye.net>
 * @license  http://choosealicense.com/licenses/no-license/ No license
 * @link     https://github.com/PixEye/php-tests
 */
class RedmineTimeEntry
{
    /**
     * Data as an array
     */
    protected $data;

    /**
     * Constructor
     *
     * @param string $spent_on    Date as: YYYY-MM-DD
     * @param float  $hours       Nb hours
     * @param int    $issue_id    Issue ID
     * @param int    $activity_id Activity ID
     * @param string $comments    Some comments (optional)
     *
     * @return RedmineTimeEntry
     */
    public function __construct(
        $spent_on, $hours, $issue_id, $activity_id=0, $comments=''
    ) {
        $keys = self::getKeys();

        $tyear  = subStr($spent_on, 0, 4);
        $tmonth = subStr($spent_on, 5, 2);
        $tweek  = date('W', strToTime($spent_on));

        $created_on = $updated_on = strFTime('%F %T');

        forEach ($keys as $k) {
            $this->data[$k] = isSet($$k)?$$k:null;
        }

        return $this;
    }

    /**
     * Magic method that...
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            '%s #%4d %.1fh %s',
            $this->data['spent_on'],
            $this->data['issue_id'],
            $this->data['hours'],
            $this->data['comments']
        );
    }

    /**
     * Constructor
     *
     * @return array
     */
    static public function getKeys()
    {
        return array(
            'project_id',
            'user_id',
            'issue_id',     // (required)
            'hours',        // float (required)
            'comments',     // varchar(255) (important)
            'activity_id',  // (required)
            'spent_on',     // date (required)
            'tyear',
            'tmonth',
            'tweek',
            'created_on',   // datetime
            'updated_on',   // datetime
        );
    }
}
