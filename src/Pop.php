<?php
/**
 * Popcorn Micro-Framework (http://popcorn.popphp.org/)
 *
 * @link       https://github.com/nicksagona/Popcorn
 * @category   Pop
 * @package    Pop
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    https://raw.github.com/nicksagona/Popcorn/master/LICENSE.TXT     New BSD License
 */

/**
 * @namespace
 */
namespace Pop;
require_once __DIR__ . '/Project/Project.php';

/**
 * This is the alias child class for the main Pop Project class.
 * Aside from ease of use, the purpose of this class is to satisfy
 * dependencies and requirements in certain sub-components.
 *
 * @category   Pop
 * @package    Pop
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2014 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    https://raw.github.com/nicksagona/Popcorn/master/LICENSE.TXT     New BSD License
 * @version    1.3.0
 */
class Pop extends \Pop\Project\Project {}