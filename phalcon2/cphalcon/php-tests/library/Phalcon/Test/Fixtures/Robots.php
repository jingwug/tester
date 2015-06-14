<?php
/**
 * Robots.php
 * Phalcon_Test_Fixtures_Robots
 *
 * Used to populate the tables (fixtures) in the database
 *
 * PhalconPHP Framework
 *
 * @copyright (c) 2011-2015 Phalcon Team
 * @link      http://www.phalconphp.com
 * @author    Andres Gutierrez <andres@phalconphp.com>
 * @author    Nikolaos Dimopoulos <nikos@phalconphp.com>
 *
 * The contents of this file are subject to the New BSD License that is
 * bundled with this package in the file docs/LICENSE.txt
 *
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email to license@phalconphp.com
 * so that we can send you a copy immediately.
 */

namespace Phalcon\Test\Fixtures;

class Robots
{
    public static function get()
    {
        $data[] = "(1, 'Robotina', 'mechanical', 1972)";
        $data[] = "(2, 'Astro Boy', 'mechanical', 1952)";
        $data[] = "(3, 'Terminator', 'cyborg', 2029)";

        return $data;
    }
}
