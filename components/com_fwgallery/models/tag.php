<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelTag extends JModelLegacy {
    function getUserState($name, $def='', $type='cmd') {
        $app = JFactory :: getApplication();
        $context = 'com_fwgallery.gallery.';
        return $app->getUserStateFromRequest($context.$name, $name, $def, $type);
    }
	function _getWhereClause() {
        $user = JFactory :: getUser();

        $db = JFactory :: getDBO();
		$app = JFactory :: getApplication();
		$params = $app->getParams();

		$where = array(
			'f.published = 1',
			'p.published = 1',
			'f.id IN (SELECT ft.file_id FROM #__fwg_tags AS t, #__fwg_files_tags AS ft WHERE ft.tag_id = t.id AND t.name = '.$db->quote(JRequest :: getString('id', $params->get('tag'))).')'
		);
		if ($user->id) {
			$where[] = '(p.gid = 0 OR p.gid IS NULL OR EXISTS(SELECT * FROM #__usergroups AS ug WHERE pg.lft=ug.lft AND ug.id IN ('.implode(',',$user->groups).')))';
		} else $where[] = '(p.gid = 0 OR p.gid IS NULL)';

		return $where?('WHERE '.implode(' AND ', $where)):'';
	}

	function _getOrderClause() {
		$params = JComponentHelper :: getParams('com_fwgallery');
		switch ($this->getUserState('order', $params->get('ordering_images'))) {
			case 'new' : $order = 'f.created DESC';
			break;
			case 'old' : $order = 'f.created';
			break;
			case 'name' : $order = 'f.name';
			break;
			case 'voting' : $order = '_rating_value DESC';
			break;
			default : $order = 'f.ordering';
		}
		return 'ORDER BY '.$order;
	}

    function getObj() {
        $db = JFactory :: getDBO();
        $db->setQuery('SELECT * FROM #__fwg_tags WHERE name = '.$db->quote(JRequest :: getString('id')));
        if ($tag = $db->loadObject()) return $tag;
        $tag = new stdclass;
        $tag->name = JRequest :: getString('id');
        return $tag;
    }

    function getQty() {
        $db = JFactory :: getDBO();
        $db->setQuery('
SELECT
	COUNT(*)
FROM
	#__fwg_files AS f
	LEFT JOIN #__fwg_projects AS p ON f.project_id = p.id
	LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
'.$this->_getWhereClause()
		);
        return $db->loadResult();
    }

    function getPagination() {
        $params = JComponentHelper :: getParams('com_fwgallery');
        jimport('joomla.html.pagination');
        $pagination = new JPagination(
        	$this->getQty(),
        	JRequest :: getInt('limitstart'),
        	$this->getUserState('limit', $params->get('images_limit', 20))
    	);
        return $pagination;
    }

    function getList() {
        $db = JFactory :: getDBO();
        $user = JFactory :: getUser();
        $params = JComponentHelper :: getParams('com_fwgallery');

        $db->setQuery('
SELECT
    f.*,
    p.user_id AS _user_id,
	p.color AS _color,
    u.name AS _user_name,
    p.name AS _project_name,
	t.name AS _type_name,
	t.plugin AS _plugin_name,
	CASE WHEN (SELECT COUNT(*) FROM #__fwg_files_vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0))) > 0 THEN (SELECT SUM(v.value) FROM #__fwg_files_vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0)))/(SELECT COUNT(*) FROM #__fwg_files_vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0))) ELSE 0 END AS _rating_value,
	(SELECT SUM(v.value) FROM #__fwg_files_vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0))) AS _rating_sum,
	(SELECT COUNT(*) FROM #__fwg_files_vote AS v LEFT JOIN #__users AS u ON u.id = v.user_id WHERE v.file_id = f.id AND ((u.id IS NULL AND v.user_id = 0) OR (u.id IS NOT NULL AND v.user_id <> 0))) AS _rating_count,
'.($user->id?(
'	(SELECT COUNT(*) FROM #__fwg_files_vote AS v WHERE v.file_id = f.id AND v.user_id = '.(int)$user->id.') AS _is_voted'
):(
	$params->get('public_voting')?(
'	(SELECT COUNT(*) FROM #__fwg_files_vote AS v WHERE v.file_id = f.id AND v.ipaddr = '.(int)ip2long(JFHelper :: getIP()).') AS _is_voted'
	):(
'	1 AS _is_voted'
	)
)).',
	\'\' AS _tags
FROM
    #__fwg_files AS f
	LEFT JOIN #__fwg_types AS t ON t.id = f.type_id
    LEFT JOIN #__fwg_projects AS p ON f.project_id = p.id
    LEFT JOIN #__users AS u ON u.id = f.user_id
    LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
'.$this->_getWhereClause().'
'.$this->_getOrderClause(),
        	JRequest :: getInt('limitstart'),
        	$this->getUserState('limit', $params->get('images_limit', 20))
		);
		$list = $db->loadObjectList('id');

		if ($list) {
			if ($params->get('display_image_tags')) {
				$ids = array_keys($list);
				$db->setQuery('SELECT t.id, t.name, ft.file_id FROM #__fwg_files_tags AS ft, #__fwg_tags AS t WHERE ft.tag_id = t.id AND ft.file_id IN ('.implode(',', $ids).') ORDER BY t.name');
				if ($buff = $db->loadObjectList()) foreach ($buff as $row) {
					if (!is_array($list[$row->file_id]->_tags)) $list[$row->file_id]->_tags = array();
					$list[$row->file_id]->_tags[] = $row->name;
				}
			}
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper :: importPlugin('fwgallery');
			$dispatcher->trigger('handleFilesLoad', array(&$list));
		}
        return $list;
    }

    function vote() {
    	if (!($id = JRequest :: getInt('id'))) {
			$this->setError(JText :: _('FWG_NO_IMAGE_ID_PASSED'));
			return;
    	}
    	$user = JFactory :: getUser();
        $params = JComponentHelper :: getParams('com_fwgallery');
        $public_voting = $params->get('public_voting');
    	if ($user->id or $public_voting) {
    		$db = JFactory :: getDBO();
    		if ($user->id)
	    		$db->setQuery('SELECT COUNT(*) FROM #__fwg_files_vote WHERE user_id = '.$user->id.' AND file_id = '.(int)$id);
	    	else
	    		$db->setQuery('SELECT COUNT(*) FROM #__fwg_files_vote WHERE ipaddr = '.(int)ip2long(JFHelper :: getIP()).' AND file_id = '.(int)$id);

    		if ($db->loadResult()) {
    			$this->setError(JText :: _('FWG_YOU_HAVE_VOTED_ON_THIS_IMAGE'));
    		} else {
    			$value = max(0, min(5, (int)JRequest :: getInt('value')));
	    		if ($user->id)
	    			$db->setQuery('INSERT INTO #__fwg_files_vote (user_id, file_id, value) VALUES ('.$user->id.', '.(int)$id.', '.$value.')');
	    		else
	    			$db->setQuery('INSERT INTO #__fwg_files_vote (ipaddr, file_id, value) VALUES ('.(int)ip2long(JFHelper :: getIP()).', '.(int)$id.', '.$value.')');
    			if ($db->query()) {
	    			$this->setError(JText :: _('FWG_YOUR_VOTE_WAS_SUCCESFULLY_RECORDED'));
    			} else {
	    			$this->setError(JText :: _('FWG_THERE_WAS_AN_ERROR_WHILE_YOUR_VOTE_RECORDING'));
    			}
    		}
    	} else {
			$this->setError(JText :: _('FWG_YOU_ARE_NOT_LOGGED_IN'));
    	}
    }

    function getImage() {
    	$table = $this->getTable('file');
    	if ($id = JRequest :: getInt('id')) $table->load($id);
    	return $table;
    }

}
?>