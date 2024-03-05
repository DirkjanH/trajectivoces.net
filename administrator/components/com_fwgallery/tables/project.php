<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class TableProject extends JTable {
    /** @var int Primary key */
    var $id = null,
    	$parent = null,
    	$user_id = null,
    	$gid = null,
    	$name = null,
    	$descr = null,
    	$ordering = null,
    	$published = 1,
    	$created = null,
    	$color = null;

    var $_user_name = null,
    	$_group_name = null;

    function __construct(&$db) {
        parent::__construct('#__fwg_projects', 'id', $db);
    }

    function load($oid = null, $reset = true) {
        if (is_numeric($oid) and $oid) {
        	$db = JFactory :: getDBO();
        	$user = JFactory :: getUser();
			$app = JFactory :: getApplication();

        	$db->setQuery('
SELECT
	p.*,
	(SELECT u.name FROM #__users AS u WHERE u.id = p.user_id) AS _user_name,
	pg.title AS _group_name
FROM
	#__fwg_projects AS p
    LEFT JOIN #__usergroups AS pg ON pg.id = p.gid
WHERE
	p.id = '.(int)$oid
			);
        	if ($obj = $db->loadObject()) {
        		foreach ($obj as $key=>$val) $this->$key = $val;
	            return true;
        	}
        } else $this->id = 0;
    }

    function check() {
        $user = JFactory::getUser();
        if ($user->id and parent::check()) {
            if (!$this->id) {
            	if (!$this->user_id) $this->user_id = $user->id;
            	if (!$this->ordering) {
            		$db = JFactory :: getDBO();
            		$db->setQuery('SELECT MAX(ordering) FROM #__fwg_projects WHERE parent = '.(int)$this->parent);
            		$this->ordering = (int)$db->loadResult() + 1;
            	}
            }
            if ($created = JFHelper::decodeDate(JRequest::getString('created'))) $this->created = $created;
            if ($this->color) $this->color = trim($this->color, '#');
            return true;
        }
        return false;
    }

    function store($upd=null) {
		$db = JFactory :: getDBO();
		$old_user_id = 0;
    	if ($this->id) {
/* find out previous user_id */
    		$db->setQuery('SELECT id, user_id FROM #__fwg_projects WHERE id = '.(int)$this->id);
    		if ($obj = $db->loadObject()) {
    			if ($obj->user_id != $this->user_id) $old_user_id = $obj->user_id;
    		} else $this->id = 0;
    	}
    	if (parent :: store($upd)) {
/* sync images */
    		if ($old_user_id) {
    			$path_from = FWG_STORAGE_PATH.'files'.'/'.$old_user_id.'/';
    			$path_to = FWG_STORAGE_PATH.'files'.'/'.$this->user_id.'/';
    			jimport('joomla.filesystem.file');
    			if (!file_exists($path_to)) JFile :: write($path_to.'index.html', $body='<html><body></body></html>');
    			$db->setQuery('SELECT filename FROM #__fwg_files WHERE project_id = '.(int)$this->id);
    			if ($files = $db->loadResultArray()) foreach ($files as $file) {
    				if (file_exists($path_from.'th_'.$file)) JFile :: move($path_from.'th_'.$file, $path_to.'th_'.$file);
    				if (file_exists($path_from.'mid_'.$file)) JFile :: move($path_from.'mid_'.$file, $path_to.'mid_'.$file);
    				if (file_exists($path_from.$file)) JFile :: move($path_from.$file, $path_to.$file);
    			}
    		}

			$dispatcher = JDispatcher::getInstance();
			JPluginHelper :: importPlugin('fwgallery');
			$dispatcher->trigger('handleProjectStore', array($this));

    		return  true;
    	}
    }

    function delete($oid=null) {
    	$db = JFactory :: getDBO();
		$db->setQuery('SELECT id FROM #__fwg_files WHERE project_id = '.(int)$oid);
		if ($ids = $db->loadResultArray()) foreach ($ids as $id) {
			$file = JTable::getInstance('file', 'Table');
			$file->delete($id);
		}
    	if (parent::delete($oid)) {
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper :: importPlugin('fwgallery');
			$dispatcher->trigger('handleProjectDelete', array($oid));
			return true;
    	}
    }
}

?>