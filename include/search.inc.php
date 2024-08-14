<?php

declare(strict_types=1);

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * wgFileManager module for xoops
 *
 * @copyright    2021 XOOPS Project (https://xoops.org)
 * @license      GPL 2.0 or later
 * @package      wgfilemanager


 * @author       Goffy - Wedega - Email:webmaster@wedega.com - Website:https://xoops.wedega.com
 */

use XoopsModules\Wgfilemanager;


/**
 * search callback functions
 *
 * @param $queryarray
 * @param $andor
 * @param $limit
 * @param $offset
 * @param $userid
 * @return array $itemIds
 */
function wgfilemanager_search($queryarray, $andor, $limit, $offset, $userid)
{
    $ret = [];
    $helper = \XoopsModules\Wgfilemanager\Helper::getInstance();

    // search in table file
    // search keywords
    $elementCount = 0;
    $fileHandler = $helper->getHandler('File');
    if (\is_array($queryarray)) {
        $elementCount = \count($queryarray);
    }
    if ($elementCount > 0) {
        $crKeywords = new \CriteriaCompo();
        for ($i = 0; $i  <  $elementCount; $i++) {
            $crKeyword = new \CriteriaCompo();
            unset($crKeyword);
        }
    }
    // search user(s)
    if ($userid && \is_array($userid)) {
        $userid = array_map('\intval', $userid);
        $crUser = new \CriteriaCompo();
        $crUser->add(new \Criteria('submitter', '(' . \implode(',', $userid) . ')', 'IN'), 'OR');
    } elseif (is_numeric($userid) && $userid > 0) {
        $crUser = new \CriteriaCompo();
        $crUser->add(new \Criteria('submitter', $userid), 'OR');
    }
    $crSearch = new \CriteriaCompo();
    if (isset($crKeywords)) {
        $crSearch->add($crKeywords);
    }
    if (isset($crUser)) {
        $crSearch->add($crUser);
    }
    $crSearch->setStart($offset);
    $crSearch->setLimit($limit);
    $crSearch->setSort('date_created');
    $crSearch->setOrder('DESC');
    $fileAll = $fileHandler->getAll($crSearch);
    foreach (\array_keys($fileAll) as $i) {
        $ret[] = [
            'image'  => 'assets/icons/16/file.png',
            'link'   => 'file.php?op=show&amp;id=' . $fileAll[$i]->getVar('id'),
            'title'  => $fileAll[$i]->getVar('directory_id'),
            'time'   => $fileAll[$i]->getVar('date_created')
        ];
    }
    unset($crKeywords);
    unset($crKeyword);
    unset($crUser);
    unset($crSearch);

    return $ret;

}
