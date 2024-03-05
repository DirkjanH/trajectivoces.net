<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelfwGallery extends JModelLegacy {
    function getUserState($name, $def='', $type='cmd') {
        $app = JFactory::getApplication();
        $context = 'com_fwgallery.projects.';
        return $app->getUserStateFromRequest($context.$name, $name, $def, $type);
    }

    function _collectWhere() {
        $where = array();

        if ($data = $this->getUserState('client', 0, 'int')) {
            $where[] = 'p.user_id = '.$data;
        }

        return $where?('WHERE '.implode(' AND ', $where)):'';
    }

    function getProjectsQty() {
        $db = JFactory::getDBO();
        $db->setQuery('SELECT COUNT(*) FROM #__fwg_projects AS p '.$this->_collectWhere());
        return $db->loadResult();
    }

    function getPagination() {
        $app = JFactory::getApplication();
        jimport('joomla.html.pagination');
        $pagination = new JPagination(
        	$this->getProjectsQty(),
        	$this->getUserState('limitstart', 0),
        	$this->getUserState('limit', $app->getCfg('list_limit'))
    	);
        return $pagination;
    }

    function getProject() {
        $project = $this->getTable('project');
        if (($ids = (array)JRequest::getVar('cid') and $id = JArrayHelper::getValue($ids, 0)) or $id = JRequest::getInt('id', 0)) {
            $project->load($id);
        }
        return $project;
    }

    function getProjects() {
        $db = JFactory::getDBO();
        $app = JFactory::getApplication();
        $limit = (int)$this->getUserState('limit', $app->getCfg('list_limit'));
        $limitstart = (int)$this->getUserState('limitstart', 0);
		$list = array();
    	if ($this->getUserState('client', 0, 'int')) {
	        $db->setQuery('
SELECT
    p.*,
	p.name AS treename,
	p.user_id AS _user_id,
    u.name AS _user_name,
    CASE WHEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) IS NOT NULL THEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) ELSE (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id ORDER BY ordering LIMIT 1) END AS filename,
	(SELECT g.title FROM #__usergroups AS g WHERE g.id = p.gid) AS _group_name,
	(SELECT COUNT(*) FROM #__fwg_files AS f WHERE f.project_id = p.id) AS _qty
FROM
    #__fwg_projects AS p
    LEFT JOIN #__users AS u ON u.id = p.user_id
'.$this->_collectWhere().'
ORDER BY
	p.parent,
    p.ordering',
    			$limitstart,
    			$limit
			);
	        $list = $db->loadObjectList();
    	} else {
	        $db->setQuery('
SELECT
    p.*,
    u.name AS _user_name,
	p.user_id AS _user_id,
    CASE WHEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) IS NOT NULL THEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) ELSE (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id ORDER BY ordering LIMIT 1) END AS filename,
	(SELECT g.title FROM #__usergroups AS g WHERE g.id = p.gid) AS _group_name,
	(SELECT COUNT(*) FROM #__fwg_files AS f WHERE f.project_id = p.id) AS _qty
FROM
    #__fwg_projects AS p
    LEFT JOIN #__users AS u ON u.id = p.user_id
'.$this->_collectWhere().'
ORDER BY
	p.parent,
    p.ordering'
			);
	        if ($rows = $db->loadObjectList()) {
	            $children = array();
	            foreach ($rows as $v) {
	                $pt = $v->parent;
	                $list = @$children[$pt] ? $children[$pt] : array();
	                array_push( $list, $v );
	                $children[$pt] = $list;
	            }
		        $levellimit = (int)$this->getUserState('levellimit', 10);
	            $list = JHTML::_('fwGalleryCategory.treerecurse', 0, '', array(), $children, max( 0, $levellimit-1 ) );
	            if ($limit) $list = array_slice($list, $limitstart, $limit);
	        }
    	}

        return $list;
    }

    function saveorder() {
        $cid = (array)JRequest::getVar('cid');
        $order = (array)JRequest::getVar('order');

        if (count($cid) and count($cid) == count($order)) {
            $db = JFactory::getDBO();
            $project = $this->getTable('project');
            foreach ($cid as $num=>$id) {
                $db->setQuery('UPDATE #__fwg_projects SET ordering = '.(int)JArrayHelper::getValue($order, $num).' WHERE id = '.(int)$id);
                $db->query();
            }
            JArrayHelper :: toInteger($cid);
            $db->setQuery('SELECT DISTINCT parent FROM  #__fwg_projects WHERE id IN ('.implode(',',$cid).')');
            if ($parents = $db->loadResultArray()) foreach ($parents as $parent) $project->reorder('parent='.(int)$parent);
            return true;
        }
        return false;
    }

    function orderdown() {
        if ($cid = (array)JRequest::getVar('cid') and $id = JArrayHelper::getValue($cid, 0)) {
            $project = $this->getTable('project');
            if ($project->load($id)) $project->move(1, 'parent='.(int)$project->parent);
            return true;
        }
        return false;
    }

    function orderup() {
        if ($cid = (array)JRequest::getVar('cid') and $id = JArrayHelper::getValue($cid, 0)) {
            $project = $this->getTable('project');
            if ($project->load($id)) $project->move(-1, 'parent='.(int)$project->parent);
            return true;
        }
        return false;
    }

    function save() {
        $project = $this->getTable('project');
        if ($id = JRequest::getInt('id') and !$project->load($id)) JRequest :: setVar('id', 0);

        if ($project->bind(JRequest::get('default', JREQUEST_ALLOWHTML)) and $project->check() and $project->store()) {
            $this->setError(JText::_('FWG_THE_GALLERY_DATA').' '.JText::_('stored successfully'));
            return $project->id;
        } else
            $this->setError(JText::_('FWG_THE_GALLERY_DATA').' '.JText::_('FWG_STORING_ERROR').':'.$project->getError());
    }

    function getClients() {
        $db = JFactory::getDBO();
        $db->setQuery('SELECT u.id, u.name FROM #__users AS u ORDER BY u.name');
        return $db->loadObjectList();
    }

    function remove() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $project = $this->getTable('project');
            foreach ($cid as $id) $project->delete($id);
            $this->setError(JText::_('FWG_GALLERY_IES_REMOVED'));
            return true;
        } else $this->setError(JText::_('FWG_NO_GALLERY_ID_PASSED_TO_REMOVE'));
        return false;
    }

    function publish() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('project');
            $image->publish($cid, 1);
            $this->setError(JText::_('FWG_GALLERY_IES_PUBLISHED'));
            return true;
        } else $this->setError(JText::_('FWG_NO_GALLERY_ID_PASSED_TO_PUBLISH'));
        return false;
    }

    function unpublish() {
        if ($cid = (array)JRequest::getVar('cid')) {
            $image = $this->getTable('project');
            $image->publish($cid, 0);
            $this->setError(JText::_('FWG_GALLERY_IES_UNPUBLISHED'));
            return true;
        } else $this->setError(JText::_('FWG_NO_GALLERY_ID_PASSED_TO_PUBLISH'));
        return false;
    }

    function getGroups() {
		$acl = JFactory::getACL();
		return array_merge(
			array(JHTML :: _('select.option', '0', JText :: _('FWG_GUESTS'))),
			(array)$acl->get_group_children_tree(null, 'Public Frontend', false)
		);
    }
}
?>