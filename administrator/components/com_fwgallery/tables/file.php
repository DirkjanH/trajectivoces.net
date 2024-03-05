<?php
/**
 * FW Gallery x.x.x
 * @copyright (C) 2012 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class TableFile extends JTable {
    var $id = null,
    	$ordering = null,
    	$published = 1,
    	$project_id = null,
    	$type_id = 1,
    	$user_id = null,
    	$created = null,
    	$name = null,
    	$descr = null,
    	$filename = null,
    	$selected = 0,
    	$hits = 0,
    	$longitude = null,
    	$latitude = null,
    	$media = null,
    	$media_code = null,
    	$copyright = null;

    var $_user_id = 0,
    	$_user_name = '',
    	$_type_name = '',
    	$_plugin_name = '',
    	$_is_type_published = '',
    	$_gallery_name = '',
    	$_is_gallery_published = '',
    	$_rating_sum = 0,
    	$_rating_count = 0,
    	$_rating_value = 0,
    	$_is_voted = 0,
    	$_tags = null,
    	$_color = null;

    function __construct(&$db) {
        parent::__construct('#__fwg_files', 'id', $db);
    }

    function load($oid = NULL, $reset = true) {
        if ($oid and is_numeric($oid)) {
        	$db = JFactory :: getDBO();
        	$user = JFactory :: getUser();
	        $params = JComponentHelper :: getParams('com_fwgallery');
	        $app = JFactory :: getApplication();

        	$db->setQuery('
SELECT
	f.*,
	u.name AS _user_name,
	p.user_id AS _user_id,
	p.color AS _color,
	p.name AS _gallery_name,
	p.published AS _is_gallery_published,
	t.name AS _type_name,
	t.plugin AS _plugin_name,
	t.published AS _is_type_published,
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
)).'
FROM
	#__fwg_files AS f
	LEFT JOIN #__users AS u ON u.id = f.user_id
	LEFT JOIN #__fwg_projects AS p ON p.id = f.project_id
	LEFT JOIN #__fwg_types AS t ON t.id = f.type_id
    LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
WHERE
	'.($app->isSite()?('(
		p.gid = 0
		OR
		p.gid IS NULL
'.($user->id?('
		OR
		pg.lft = (SELECT ug.lft FROM #__usergroups AS ug WHERE ug.id IN ('.implode(',',$user->groups).'))'):'').'
	)
	AND
	'):'').'
	f.id = '.(int)$oid
			);
			if ($obj = $db->loadObject()) {
				foreach ($obj as $key=>$val) $this->$key = $val;

				if ($params->get('display_image_tags')) {
					$db->setQuery('SELECT t.name FROM #__fwg_files_tags AS ft, #__fwg_tags AS t WHERE t.id = ft.tag_id AND ft.file_id = '.(int)$this->id.' ORDER BY t.name');
					$this->_tags = (array)$db->loadColumn();
				}

				$dispatcher = JDispatcher::getInstance();
				JPluginHelper :: importPlugin('fwgallery');
				$dispatcher->trigger('handleFileLoad', array(&$this));

	            return true;
			} else $this->setError(JText :: _('FWG_REQUESTED_FILE_NOT_FOUND_OR_ACCESS_DENIED'));
        }
    }

    function clockwise($cid, $user_id = null) {
    	if ($cid) foreach ($cid as $id) if ($this->load((int)$id, $user_id) and JFHelper :: isFileExists($this)) {
            $img_path = FWG_STORAGE_PATH.'files'.'/'.$this->_user_id.'/';
    		GPMiniImg :: rotate($img_path, 'th_'.$this->filename, 270);
    		GPMiniImg :: rotate($img_path, 'mid_'.$this->filename, 270);
    		GPMiniImg :: rotate($img_path, $this->filename, 270);
    	}
    }

    function counterclockwise($cid, $user_id = null) {
    	if ($cid) foreach ($cid as $id) if ($this->load((int)$id, $user_id) and JFHelper :: isFileExists($this)) {
            $img_path = FWG_STORAGE_PATH.'files'.'/'.$this->_user_id.'/';
    		GPMiniImg :: rotate($img_path, 'th_'.$this->filename, 90);
    		GPMiniImg :: rotate($img_path, 'mid_'.$this->filename, 90);
    		GPMiniImg :: rotate($img_path, $this->filename, 90);
    	}
    }

    function select($cid, $user_id = null) {
        if ($id = JArrayHelper::getValue($cid, 0)) {
            if ($this->load($id, $user_id)) {
		    	$db = JFactory :: getDBO();
                $db->setQuery('UPDATE #__fwg_files SET selected = 0 WHERE project_id = '.(int)$this->project_id);
                $db->query();

                $this->selected = 1;
                return $this->store();
            }
        }
    }

    function unselect($cid, $user_id = null) {
        if ($id = JArrayHelper::getValue($cid, 0)) {
            if ($this->load($id, $user_id)) {
                $this->selected = 0;
                return $this->store();
            }
        }
        return false;
    }

    function check() {
        if (parent::check()) {
        	jimport('joomla.filesystem.file');
	    	$db = JFactory :: getDBO();

            $db->setQuery('
SELECT
	(SELECT p.user_id FROM #__fwg_projects AS p WHERE p.id = '.(int)$this->project_id.') AS new_user_id,
	(SELECT p.user_id FROM #__fwg_files AS f, #__fwg_projects AS p WHERE p.id = f.project_id AND f.id = '.(int)$this->id.') AS old_user_id');
			$user_data = $db->loadObject();
/* check project change */
        	if ($this->filename and $user_data->new_user_id and $user_data->old_user_id and $user_data->new_user_id != $user_data->old_user_id) {
                $src_img_path = FWG_STORAGE_PATH.'files'.'/'.$user_data->old_user_id.'/';
                $dst_img_path = FWG_STORAGE_PATH.'files'.'/'.$user_data->new_user_id.'/';
/* create destination folder if needed */
                if (!file_exists($dst_img_path)) JFile :: write($dst_img_path.'/index.html', $body = '<html><head><title></title></head><body></body></html>');
                $error_moving = false;
/* move image files */
                if (file_exists($src_img_path.$this->original_filename) and !JFile :: move($src_img_path.$this->original_filename, $dst_img_path.$this->original_filename)) $error_moving = true;
                elseif (file_exists($src_img_path.$this->filename) and !JFile :: move($src_img_path.$this->filename, $dst_img_path.$this->filename)) {
                	JFile :: move($dst_img_path.$this->original_filename, $src_img_path.$this->original_filename);
                	$error_moving = true;
                } elseif (file_exists($src_img_path.'mid_'.$this->filename) and !JFile :: move($src_img_path.'mid_'.$this->filename, $dst_img_path.'mid_'.$this->filename)) {
                	JFile :: move($dst_img_path.$this->original_filename, $src_img_path.$this->original_filename);
                	JFile :: move($dst_img_path.$this->filename, $src_img_path.$this->filename);
                	$error_moving = true;
                } elseif (file_exists($src_img_path.'th_'.$this->filename) and !JFile :: move($src_img_path.'th_'.$this->filename, $dst_img_path.'th_'.$this->filename)) {
                	JFile :: move($dst_img_path.$this->original_filename, $src_img_path.$this->original_filename);
                	JFile :: move($dst_img_path.$this->filename, $src_img_path.$this->filename);
                	JFile :: move($dst_img_path.'mid_'.$this->filename, $src_img_path.'mid_'.$this->filename);
                	$error_moving = true;
                }
                if ($error_moving) {
                	$this->setError(JText :: _('FWG_ERROR_MOVING_FILES'));
                	return false;
                }
        	}

	    	if (!$this->id) {
/* storing user_id */
	    		 if (!$this->user_id) {
	    		 	$user = JFactory :: getUser();
	    		 	$this->user_id = $user->id;
	    		 }
	    		 if (!$this->ordering) {
			        $db->setQuery('SELECT MAX(ordering) FROM #__fwg_files AS f WHERE f.project_id = '.(int)$this->project_id);
			        $this->ordering = $db->loadResult()+1;
	    		 }
	    	}
/* checking selected */
            $this->selected = JRequest::getInt('selected');
            if ($this->selected) {
                $db->setQuery('UPDATE #__fwg_files SET selected=0 WHERE project_id = '.(int)$this->project_id);
                $db->query();
            }

/* file upload */
            if ($file = JArrayHelper :: getValue($_FILES, 'filename') and JArrayHelper :: getValue($file, 'name') and !JArrayHelper :: getValue($file, 'error')) {
                $old_original_filename = $this->original_filename;
                $old_filename = $this->filename;

                $img_path = FWG_STORAGE_PATH.'files'.'/'.$user_data->new_user_id.'/';
                $conf = JComponentHelper::getParams('com_fwgallery');

				$ext = JFile :: getExt($file['name']);
				$original_filename = '';
				do {
					$original_filename = md5($row->filename.rand().microtime()).'.'.$ext;
				} while (file_exists($img_path.$original_filename));

				if (JFile :: copy($file['tmp_name'], $img_path.$original_filename)) $this->original_filename = $original_filename;

				if ($conf->get('use_watermark')) {
	                $wmfile = ($conf->get('watermark_file') and file_exists(FWG_STORAGE_PATH.'/'.$conf->get('watermark_file')))?(FWG_STORAGE.'/'.$conf->get('watermark_file')):'';
					$wmtext = $conf->get('watermark_text');
				} else {
					$wmfile = '';
					$wmtext = '';
				}

                $this->filename = GPMiniImg::imageProcessing('filename', $img_path, $conf->get('im_max_w',800), $conf->get('im_max_h',600), $wmfile, $wmtext, $conf->get('watermark_position'));
                GPMiniImg::makeThumb($this->filename, $img_path, 'mid_', $conf->get('im_mid_w',340), $conf->get('im_mid_h',255), $conf->get('im_just_shrink'));
                GPMiniImg::makeThumb($this->filename, $img_path, 'th_', $conf->get('im_th_w',160), $conf->get('im_th_h',120), $conf->get('im_just_shrink'));
				if ($this->filename) {
					if (function_exists('exif_read_data') and $exif = @exif_read_data($file['tmp_name'])) {
/* geotags */
						if ($longitude = JArrayHelper :: getValue($exif, 'GPSLongitude') and $longitude_ref = JArrayHelper :: getValue($exif, 'GPSLongitudeRef')) $this->longitude = JFHelper :: getGps($longitude, $longitude_ref);
						if ($latitude = JArrayHelper :: getValue($exif, 'GPSLatitude') and $latitude_ref = JArrayHelper :: getValue($exif, 'GPSLatitudeRef')) $this->latitude = JFHelper :: getGps($latitude, $latitude_ref);
/* copyright */
						if ($copyright = JArrayHelper :: getValue($exif, 'Copyright')) $this->copyright = $copyright;
						if ($date = JArrayHelper :: getValue($exif, 'DateTime')) $this->created = date('Y-m-d H:i:s', strtotime($date));
					}
/* remove previous images */
	                if ($old_original_filename and file_exists($img_path.$old_original_filename)) {
	                	JFile :: delete($img_path.$old_original_filename);
	                }
	                if ($old_filename and $old_filename != $this->filename) {
	                    if (file_exists($img_path.$old_filename)) JFile :: delete($img_path.$old_filename);
	                    if (file_exists($img_path.'mid_'.$old_filename)) JFile :: delete($img_path.'mid_'.$old_filename);
	                    if (file_exists($img_path.'th_'.$old_filename)) JFile :: delete($img_path.'th_'.$old_filename);
	                }
				}
            }
        	if ($this->latitude) $this->latitude = str_replace(',', '.', $this->latitude);
        	if ($this->longitude) $this->longitude = str_replace(',', '.', $this->longitude);
            return true;
        }
        return false;
    }

    function store($upd=null) {
    	$is_new = !$this->id;
    	if (parent :: store($upd)) {
/* tags part */
    		$db = JFactory :: getDBO();
    		if (!$is_new) {
	    		$db->setQuery('DELETE FROM #__fwg_files_tags WHERE file_id = '.(int)$this->id);
	    		$db->query();
    		}
    		if ($buff = JRequest :: getString('tags')) {
    			$buff = explode(',', $buff);
    			$tags = array();
    			foreach ($buff as $tag) {
    				$tags[] = $db->quote(trim($tag));
    			}
				$db->setQuery('SELECT id, LOWER(name) AS name FROM #__fwg_tags WHERE name IN ('.implode(',', $tags).')');
				$list = $db->loadObjectList('name');
				foreach ($buff as $tag) {
					$tag =  mb_strtolower(trim($tag));
					if (!isset($list[$tag])) {
						$db->setQuery('INSERT INTO #__fwg_tags SET name = '.$db->quote(mb_convert_case($tag, MB_CASE_TITLE, 'UTF-8')));
						if ($db->query()) {
							$obj = new stdclass;
							$obj->id = $db->insertid();
							$list[$tag] = $obj;
						}
					}
				}
				if ($list) {
					$query = '';
					foreach ($list as $obj) {
						$query .= ($query?',':'').'('.(int)$this->id.','.(int)$obj->id.')';
					}
					if ($query) {
						$db->setQuery('INSERT INTO #__fwg_files_tags (file_id, tag_id) VALUES '.$query);
						$db->query();
					}
				}
    		}

			$dispatcher = JDispatcher::getInstance();
			JPluginHelper :: importPlugin('fwgallery');
			$dispatcher->trigger('handleFileStore', array($this, $is_new));
    		return true;
    	}
    }

    function delete($oid = null, $user_id = null) {
    	if ($this->load($oid, $user_id)) {
	        if (parent::delete($oid)) {
	        	$db = JFactory :: getDBO();
	        	$db->setQuery('DELETE FROM #__fwg_files_vote WHERE file_id = '.(int)$oid);
	        	$db->query();
	        	$db->setQuery('DELETE FROM #__fwg_files_tags WHERE file_id = '.(int)$oid);
	        	$db->query();
	            if ($this->_user_id) {
	                $path = FWG_STORAGE_PATH.'files'.'/'.$this->_user_id.'/';
	                if (file_exists($path.$this->filename)) {
	                    @unlink($path.$this->filename);
	                    @unlink($path.'mid_'.$this->filename);
	                    @unlink($path.'th_'.$this->filename);
	                }
	            }
				$dispatcher = JDispatcher::getInstance();
				JPluginHelper :: importPlugin('fwgallery');
				$dispatcher->trigger('handleFileDelete', array($this));
	            return true;
	        }
    	}
    }
}

?>