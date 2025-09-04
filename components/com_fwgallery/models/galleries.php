<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelGalleries extends JModelLegacy {
    function getUserState($name, $def='') {
        $app = JFactory::getApplication();
        $context = 'com_fwgallery.galleries.';
        return $app->getUserStateFromRequest($context.$name, $name, $def, 'cmd');
    }
	function _getWhereClause($parent = null) {
		$where = array(
			'p.published = 1'
		);
		if (!is_null($parent)) {
			if ($parent == 0) {
				$app = JFactory :: getApplication();
				$params = $app->getParams();
				$ids = array();
				if ($buff = (array)$params->get('galleries_id')) {
					foreach ($buff as $row) if ($row and is_numeric($row)) $ids[] = $row;
				}
				if ($ids) $where[] = 'p.id IN ('.implode(',', $ids).')';
				 else $where[] = 'p.parent = '.$parent;
			} else $where[] = 'p.parent = '.$parent;
		}

        $user = JFactory::getUser();
		if ($user->id) {
			$where[] = '(p.gid = 0 OR p.gid IS NULL OR EXISTS(SELECT * FROM #__usergroups AS ug WHERE pg.lft=ug.lft AND ug.id IN ('.implode(',',$user->groups).')))';
		} else $where[] = '(p.gid = 0 OR p.gid IS NULL)';

		return 'WHERE '.implode(' AND ', $where);
	}
	function _getOrderClause() {
		$params = JComponentHelper :: getParams('com_fwgallery');
		switch ($this->getUserState('order', $params->get('ordering_galleries'))) {
			case 'new' : $order = 'p.created DESC';
			break;
			case 'old' : $order = 'p.created';
			break;
			case 'name' : $order = 'p.name';
			break;
			default : $order = 'p.ordering';
		}
		return 'ORDER BY '.$order;
	}
    function getAllList($parent_id = null) {
        $db = JFactory :: getDBO();
        $params = JComponentHelper :: getParams('com_fwgallery');

        $db->setQuery('
SELECT
    p.*,
	p.user_id AS _user_id,
    u.name AS _user_name,
    CASE WHEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) IS NOT NULL THEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) ELSE (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id ORDER BY ordering LIMIT 1) END AS filename,
	(SELECT COUNT(*) FROM #__fwg_files AS f, #__fwg_types AS t WHERE f.project_id = p.id AND f.published = 1 AND f.type_id = t.id AND t.name = \'Image\') AS _photos,
	(SELECT COUNT(*) FROM #__fwg_files AS f, #__fwg_types AS t WHERE f.project_id = p.id AND f.published = 1 AND f.type_id = t.id AND t.name = \'Video\') AS _videos,
	(SELECT COUNT(*) FROM #__fwg_projects AS pp WHERE pp.parent = p.id AND pp.published = 1) AS _subfolders
FROM
    #__fwg_projects AS p
    LEFT JOIN #__users AS u ON (u.id = p.user_id)
    LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
'.$this->_getWhereClause().'
'.$this->_getOrderClause()
		);
		if ($list = $db->loadObjectList('id')) {
			foreach ($list as $i=>$row) {
				if ($row->parent and !empty($list[$row->parent])) {
					$parent = $row->parent;
					do {
						$list[$parent]->_photos += $row->_photos;
						$list[$parent]->_videos += $row->_videos;
						$list[$parent]->_subfolders += $row->_subfolders;
						$parent = $list[$parent]->parent;
					} while (!empty($list[$parent]));
				}
			}

			if (is_null($parent_id)) return $list;
			elseif (!is_array($parent_id)) $parent_id = array($parent_id);
			JArrayHelper :: toInteger($parent_id, '0');

			$result = array();
			foreach ($list as $i=>$row) if (in_array($row->parent, $parent_id)) $result[] = $row;
	        return $result;
		}
    }
    function getList() {
        $db = JFactory :: getDBO();
        $params = JComponentHelper :: getParams('com_fwgallery');

        $db->setQuery('
SELECT
    p.*,
	p.user_id AS _user_id,
    u.name AS _user_name,
    CASE WHEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) IS NOT NULL THEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) ELSE (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id ORDER BY ordering LIMIT 1) END AS filename,
	(SELECT COUNT(*) FROM #__fwg_projects AS pp WHERE pp.parent = p.id AND pp.published = 1) AS _subfolders
FROM
    #__fwg_projects AS p
    LEFT JOIN #__users AS u ON (u.id = p.user_id)
    LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
'.$this->_getWhereClause($parent = 0).'
'.$this->_getOrderClause(),
    		JRequest :: getInt('limitstart'),
    		$this->getUserState('limit', $params->get('galleries_limit', 20))
		);
        return $db->loadObjectList();
    }
    function getQty() {
        $db = JFactory :: getDBO();
        $db->setQuery('
SELECT
	COUNT(*)
FROM
	#__fwg_projects AS p
	LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
'.$this->_getWhereClause($parent = 0)
		);
        return $db->loadResult();
    }
    function getPagination() {
        $params = JComponentHelper :: getParams('com_fwgallery');
        jimport('joomla.html.pagination');
        $pagination = new JPagination(
        	$this->getQty(),
        	JRequest :: getInt('limitstart'),
        	$this->getUserState('limit', $params->get('galleries_limit', 20))
    	);
        return $pagination;
    }
    function getObj() {
        $obj = JFactory :: getUser(JRequest::getInt('id'));
        return $obj;
    }
    function getTitle() {
    	$menu = JMenu :: getInstance('site');
    	$item = $menu->getActive();
    	return (is_object($item) and strpos($item->link, 'com_fwgallery') !== false)?$item->title:JText :: _('FWG_GALLERIES');
    }
}
?>