<?php
/**
 * FW Gallery 2.3.2
 * @copyright (C) 2013 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

set_error_handler("fwgErrorHandler");

function fwgErrorHandler($errno, $errstr, $errfile, $errline) {
    if ($errno == E_STRICT) return true;
}

if (!class_exists('JViewLegacy')) {
	jimport('joomla.application.component.view');
	class JViewLegacy extends JView {
	}
}
if (!class_exists('JModelLegacy')) {
	jimport('joomla.application.component.model');
	class JModelLegacy extends JModel {
	}
}
if (!class_exists('JControllerLegacy')) {
	jimport('joomla.application.component.controller');
	class JControllerLegacy extends JController {
	}
}

if (!class_exists('fwGalleryViews')) {
	class fwGalleryView extends JViewLegacy {
		function loadTemplate($tpl = null)	{
			$mainframe = JFactory :: getApplication();
			$view = JRequest :: getCmd('view');
			if (!$view) $view = 'fwgallery';

			$selected_tmpl = JFHelper :: getCurrentTemplate();
			if ($selected_tmpl == 'default') return parent :: loadTemplate($tpl);

			$this->_output = null;
			$this->_template = false;

			$file = isset($tpl) ? $this->_layout.'_'.$tpl : $this->_layout;
			$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);

			$path = JPATH_SITE.'/templates/'.$mainframe->getTemplate().'/com_fwgallery/'.$view.'/'.$file.'.php';
			if (file_exists($path)) $this->_template = $path;
			if (!$this->_template) {
				$path = JPATH_SITE.'/components/com_fwgallerytmpl'.$selected_tmpl.'/html/'.$view.'/'.$file.'.php';
				if (file_exists($path)) $this->_template = $path;
			}
			if (!$this->_template) {
				$path = JPATH_SITE.'/components/com_fwgallery/views/'.$view.'/tmpl/'.$file.'.php';
				$this->_template = $path;
			}

			if ($this->_template) {
				ob_start();
				include $this->_template;
				$this->_output = ob_get_clean();

				return $this->_output;
			} else {
				return JError::raiseError( 500, 'Layout "' . $file . '" not found' );
			}
		}
	}
}

if (!class_exists('GPMiniImg')) {
    class GPMiniImg {
        function imageProcessing($name, $dst_path='', $max_x=160, $max_y=120, $watermark='', $watermark_text='', $watermark_pos='') {
			jimport('joomla.filesystem.file');
            $filedata = JArrayHelper::getValue($_FILES, $name, array());
            if (!$filedata or JArrayHelper :: getValue($filedata, 'error') or !preg_match('/\.(gif|png|jpg|jpeg)$/i', JArrayHelper :: getValue($filedata, 'name'))) {
                return '';
            }

            $filename = $filedata['name'];
            $name = trim(JFilterOutput :: stringURLSafe(JFile :: stripExt($filename)), '-');
            $ext = JFile :: getExt($filename);

            if (!$name) $name = md5($filename.rand());

            if (!$dst_path) $dst_path = JPATH_SITE.'/images/stories/users';

            if (!file_exists($dst_path))
                JFile :: write($dst_path.'/index.html', $body='<html><body></body></html>');

// check for the file existing
            $index = 0;
            do {
                $filename = $name.(($index>0)?('-'.$index):'').($ext?('.'.$ext):'');
                $index++;
            } while (file_exists($dst_path.'/'.$filename));

			if ($image = GPMiniImg :: loadImage($filedata['tmp_name'], '', $ext)) {
				$rotate = 0;
				$mirror = 0;
				if (function_exists('exif_read_data') and $exif = @exif_read_data($filedata['tmp_name'])) {
		    		switch (JArrayHelper :: getValue($exif, 'Orientation')) {
		    			case 3 :
		    				$rotate = 180;
		    			break;
		    			case 6 :
		    				$rotate = 270;
		    			break;
		    			case 8 :
		    				$rotate = 90;
		    			break;
/* part with a mirror effect */
		    			case 2 :
		    				$mirror = 1;
		    			break;
		    			case 4 :
		    				$mirror = 1;
		    				$rotate = 180;
		    			break;
		    			case 5 :
		    				$mirror = 1;
		    				$rotate = 270;
		    			break;
		    			case 7 :
		    				$mirror = 1;
		    				$rotate = 90;
		    			break;
		    		}
				}

				if ($rotate) $image = imagerotate($image, $rotate, 0);
				if ($mirror) {
				    $width = imagesx($image);
				    $height = imagesy($image);

					$buff_image = imagecreatetruecolor($width, $height);
				    $y = 1;

				    while ( $y < $height ) {
				        for ( $i = 1; $i <= $width; $i++ )
				            imagesetpixel($buff_image, $i, $y, imagecolorat($image, ( $i ), ($height - $y)));
				        $y = $y + 1;
				    }
				    imagecopy($image, $buff_image, 0, 0, 0, 0, $width, $height);
				    imagedestroy($buff_image);
				}
	            GPMiniImg :: reduceImage($image, $dst_path, $filename, $max_x, $max_y, $watermark, $watermark_text, $watermark_pos);
	            return $filename;
			}
        }

        function makeThumb($filename, $path, $prefix = 'th_', $max_x=160, $max_y=120, $is_just_shrink=1) {
            if (file_exists($path.'/'.$filename) and $prefix) {
                if ($image = GPMiniImg :: loadImage($path, $filename)) {
	                GPMiniImg::reduceImage($image, $path, $prefix.$filename, $max_x, $max_y, '', '', '', $is_just_shrink);
                }
            }
        }

        function reduceImage(&$image, $path, $filename, $max_x=0, $max_y=0, $watermark='', $watermark_text='', $watermark_pos='', $is_just_shrink=1) {
			jimport('joomla.filesystem.file');

            $org_width = imagesx($image);
            $org_height = imagesy($image);

			if (!$max_x and !$max_y) {
				$max_x = $org_width;
				$max_y = $org_height;
			} elseif (!$max_x) $max_x = $max_y * $org_width / $org_height;
			elseif (!$max_y) $max_y = $max_x * $org_height / $org_width;

			$org_ratio = $org_width/$org_height;

			if ($is_just_shrink) {
				$x_offset = 0;
				$y_offset = 0;
				$s_width = $org_width;
				$s_height = $org_height;

				if ($org_ratio > 1) { /* width larger, so srink by width */
					if ($org_width <= $max_x) {
						$max_x = $org_width;
						$max_y = $org_height;
					} else $max_y = round($max_x / $org_ratio);
				} else { /* height larger or eq */
					if ($org_height <= $max_y) {
						$max_x = $org_width;
						$max_y = $org_height;
					} else $max_x = round($max_y * $org_ratio);
				}
			} else {
				if (!($trg_ratio = $max_x/$max_y)) {
					return false;
				}

				if ($org_ratio < $trg_ratio) { /*cut vertical top & shrink */
					$s_width = $org_width;
					$s_height = (int)($org_width / $trg_ratio);

					$x_offset = 0;
					$y_offset = (int)(($org_height-$s_height)/5);
				} elseif ($org_ratio > $trg_ratio) { /* cut horisontal middle & shrink */
					$s_width = (int)($org_height * $trg_ratio);
					$s_height = $org_height;

					$x_offset = (int)(($org_width-$s_width)/2);
					$y_offset = 0;
				} else { /* images fully proportional - just shrink */
					$s_width = $org_width;
					$s_height = $org_height;

					$x_offset = 0;
					$y_offset = 0;
				}
			}

            $thumb = imagecreatetruecolor($max_x, $max_y);

            $ct = imagecolortransparent($image);
            if ($ct >= 0) {
	            $color_tran = @imagecolorsforindex($image, $ct);
	            $ct2 = imagecolorexact($thumb, $color_tran['red'], $color_tran['green'], $color_tran['blue']);
	            imagefill($thumb, 0, 0, $ct2);
            }

			imagecopyresampled(
				$thumb,
				$image,
				0,
				0,
				$x_offset,
				$y_offset,
				$max_x,
				$max_y,
				$s_width,
				$s_height
			);
	        imagedestroy($image);
	        unset($image);

			if ($watermark and file_exists(JPATH_SITE.'/'.$watermark)) {
				$wmfile = imagecreatefrompng(JPATH_SITE.'/'.$watermark);
			} elseif ($watermark_text) {
				/* calculate text size */
				$font_path = FWG_ASSETS_PATH.'fonts/chesterfield.ttf';

				$bbox = imagettfbbox(36, 0, $font_path, $watermark_text);
				if ($bbox[0] < -1) $box_width = abs($bbox[2]) + abs($bbox[0]) - 1;
				else $box_width = abs($bbox[2] - $bbox[0]);
				if ($bbox[3] > 0) $box_height = abs($bbox[7] - $bbox[1]) - 1;
				else $box_height = abs($bbox[7]) - abs($bbox[1]);

				if ($wmfile = imagecreatetruecolor($box_width, $box_height + 2)) {
					$colorTransparent = imagecolortransparent($wmfile);
					imagefill($wmfile, 0, 0, $colorTransparent);

		            $black = imagecolorallocate($wmfile, 0, 0, 0);
					imagettftext($wmfile, 36, 0, 0, abs($bbox[7]), $black, $font_path, $watermark_text);
				}
			} else $wmfile = false;

			if ($wmfile) {
				$watermark_width = imagesx($wmfile);
				$watermark_height = imagesy($wmfile);

				switch ($watermark_pos) {
					case  'center' :
						$dest_x = round(($max_x - $watermark_width)/2);
						$dest_y = round(($max_y - $watermark_height)/2);
					break;
					case  'left top' :
						$dest_x = 5;
						$dest_y = 5;
					break;
					case  'right top' :
						$dest_x = $max_x - $watermark_width - 5;
						$dest_y = 5;
					break;
					case  'left bottom' :
						$dest_x = 5;
						$dest_y = $max_y - $watermark_height - 5;
					break;
					default :
						$dest_x = $max_x - $watermark_width - 5;
						$dest_y = $max_y - $watermark_height - 5;
				}
				imagecopy($thumb, $wmfile, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
			}
			GPMiniImg :: storeImage($thumb, $path, $filename);
	        imagedestroy($thumb);
	        unset($thumb);
        }
	    function rotate($path, $filename, $rotate) {
	    	if ($image = GPMiniImg :: loadImage($path, $filename)) {
	    		$image = imagerotate($image, $rotate, 0);
	    		GPMiniImg :: storeImage($image, $path, $filename);
	    	}
	    }
	    function loadImage($path, $filename, $ext=null) {
	    	$fullname = $path.($filename?$filename:'');
	    	if (file_exists($fullname) and is_file($fullname)) {
				ini_set('memory_limit', '256M');
		    	if (is_null($ext)) {
					jimport('joomla.filesystem.file');
		    		$ext = JFile :: getExt($filename);
		    	}
		    	if (file_exists($fullname)) {
			        switch ($ext) {
			            case 'gif' :
			                $image = @imagecreatefromgif($fullname);
				            break;
			            case 'png' :
			                $image = @imagecreatefrompng($fullname);
				            break;
			            default :
			                $image = @imagecreatefromjpeg($fullname);
			        }
			        return $image;
		    	}
	    	}
	    }
	    function storeImage($image, $path, $filename) {
	    	if ($filename) {
				if (file_exists($path.$filename) and is_file($path.$filename) and is_writable($path.$filename)) @unlink($path.$filename);
				jimport('joomla.filesystem.file');
				ob_start();
		        switch (JFile :: getExt($filename)) {
		            case 'gif' :
		                @imagegif($image);
			            break;
		            case 'png' :
		                @imagepng($image);
			            break;
		            default :
		                @imagejpeg($image);
		        }
		        if ($buff = ob_get_clean()) JFile :: write($path.$filename, $buff);
	    	}
	    }
    }
}

if (!class_exists('JFHelper')) {
    define('FWG_ASSETS','components/com_fwgallery/assets/');
    define('FWG_ASSETS_PATH',JPATH_SITE.'/components/com_fwgallery/assets/');
    define('FWG_ASSETS_URI',JURI::base(true).'/components/com_fwgallery/assets/');

    define('FWG_STORAGE', 'images/com_fwgallery/');
    define('FWG_STORAGE_PATH', JPATH_SITE.'/images/com_fwgallery/');
    define('FWG_STORAGE_URI', JURI::base(true).'/'.FWG_STORAGE);

    class JFHelper {
		static function getInstalledLanguages() {
			static $languages;
			if (!is_array($languages)) {
				$buff = JLanguage :: getKnownLanguages(JPATH_BASE);
				foreach ($buff as $row) {
					$code = substr($row['tag'], 0, 2);
					$lang = new stdclass;
					$lang->id = $code;
					$lang->tag = $row['tag'];
					$lang->name = $row['name'];
					$languages[$code] = $lang;
				}
			}
			return $languages;
		}
		static function getLanguage() {
			$app = JFactory :: getApplication();
			$lang = JFactory :: getLanguage();
			$lang_name = strtolower($app->getUserStateFromRequest('com_fwgallery.language', 'lang', substr($lang->getTag(), 0, 2)));
			if (!$lang_name or ($lang_name and !in_array($lang_name, array_keys(JFHelper :: getInstalledLanguages())))) {
				$db = JFactory :: getDBO();
				$db->setQuery('SELECT `sef` FROM #__languages WHERE `published` = 1 AND `ordering` = 1');
				$lang_name = $db->loadResult();
				if (!$lang_name) $lang_name = 'en';
			}
			return $lang_name;
		}
	    static function getGroups() {
	    	static $groups;
	    	if (!is_array($groups)) {
				$user_field = 'title';
		    	$db = JFactory :: getDBO();
		    	$db->setQuery('
SELECT
	id,
	'.$user_field.' AS name
FROM
	#__usergroups
WHERE
	'.$user_field.' <> \'Public\'
	AND
	'.$user_field.' <> \'Public frontend\'
	AND
	'.$user_field.' <> \'ROOT\'
	AND
	'.$user_field.' <> \'USERS\'
ORDER BY
	lft');
				$groups = array_merge(
					array(JHTML :: _('select.option', '0', JText :: _('FWG_GUESTS'), 'id', 'name')),
					(array)$db->loadObjectList()
				);
	    	}
	    	return $groups;
	    }
    	static function escape($text) {
    		return str_replace('"', '&quot;', strip_tags($text));
    	}
    	static function getCenteringStyleArray($file, $prefix, $x=0, $y=0) {
    		$result = array();
    		if ($x or $y) {
	    		$filename = JFHelper :: getFileFilename($file, $prefix);
	    		if (file_exists(JPATH_SITE.'/'.$filename)) {
	    			$size = getimagesize(JPATH_SITE.'/'.$filename);
	    			if (!empty($size[0]) and !empty($size[1])) {
	    				if ($x and $val = ceil(($x - $size[0])/2)) $result[] = 'margin-left:'.$val.'px';
	    				if ($y and $val = ceil(($y - $size[1])/2)) $result[] = 'margin-top:'.$val.'px';
	    			}
	    		}
    		}
    		return $result;
    	}
    	static function getCenteringStyle($file, $prefix, $x, $y) {
    		if ($result = JFHelper :: getCenteringStyleArray($file, $prefix, $x, $y)) {
				return 'style="'.implode(';', $result).'"';
    		}
    	}
    	static function getCurrentTemplate() {
    		$templates = JFHelper :: getTemplates();
    		$params = JComponentHelper :: getParams('com_fwgallery');
    		$template = $params->get('template');
    		if (!$template) $template = 'default';
    		else {
	    		$tmpl_found = false;
	    		foreach ($templates as $item) if ($item->folder == $template) {
	    			$tmpl_found = true;
	    			break;
	    		}
	    		if (!$tmpl_found) $template = 'default';
    		}
    		return $template;
    	}
    	static function getTemplates() {
			static $result;
			if (!is_array($result)) {
				$result = array();
				jimport('joomla.filesystem.folder');
				$templates = JFolder :: folders (JPATH_ADMINISTRATOR.'/components', 'com_fwgallerytmpl.*');
				if (count($templates) > 0) {
					foreach ($templates as $template) {
						$template = strtolower(str_replace('com_fwgallerytmpl', '', $template));
						if ($template != 'default') {
		                    jimport( 'joomla.filesystem.file' );
		                    $fname = '/components/com_fwgallerytmpl'.$template.'/';
							if (JFile :: exists(JPATH_ADMINISTRATOR.$fname.'fwgallerytmpl'.$template.'.xml')) {
								$obj = new stdclass;
								$fields = array('name', 'date', 'author', 'version', 'preview');
								foreach ($fields as $field) $obj->$field = '';
								$obj->folder = $template;

								$file = JFile :: read(JPATH_ADMINISTRATOR.$fname.'fwgallerytmpl'.$template.'.xml');
		   				        if (preg_match('#<name>(.*?)</name>#i', $file, $m)) $obj->name = str_replace(' Tmpl ', ' Template ', $m[1]);
		   				        if (preg_match('#<creationDate>(.*?)</creationDate>#i', $file, $m)) $obj->date = $m[1];
		   				        if (preg_match('#<author>(.*?)</author>#i', $file, $m)) $obj->author = $m[1];
		   				        if (preg_match('#<version>(.*?)</version>#i', $file, $m)) $obj->version = $m[1];
		   				        if (file_exists(JPATH_SITE.$fname.'assets/images/template_thumbnail.png')) $obj->preview = JURI :: root(true).$fname.'assets/images/template_thumbnail.png';

		   				        $result[] = $obj;
							}
						}
					}
				}
				$obj = new stdclass;
				$fields = array('name'=>'FW Gallery Template Fancy', 'folder'=>'default', 'date'=>'10 Feb 2011', 'author'=>'Fastw3b - We develop while you are thinking', 'version'=>'1.0.0', 'preview' => JURI :: base(true).'/components/com_fwgallery/assets/images/template_thumbnail.png');
				foreach ($fields as $field=>$value) $obj->$field = $value;
				$result[] = $obj;
			}
			return $result;
    	}
    	static function loadTypes($published_only = true) {
			static $published, $all;
			if (!is_array($published)) {
				$published = array();
				$db = JFactory :: getDBO();
				$db->setQuery('SELECT * FROM #__fwg_types');
				$all = (array)$db->loadObjectList('plugin');
				if ($all) foreach ($all as $row) if ($row->published) $published[$row->plugin] = $row;
			}
			return $published_only?$published:$all;
    	}
    	static function getTypeId($name) {
			$types = JFHelper :: loadTypes();
			return (!empty($types[$name]->id))?$types[$name]->id:0;
    	}
    	static function loadStylesheet() {
    		if (!defined('FWG_STYLESHEET_LOADED')) {
				$app = JFactory :: getApplication();
				$path = 'templates/'.$app->getTemplate().'/css/';
				if (file_exists(JPATH_SITE.'/'.$path.'com_fwgallery.css')) JHTML::stylesheet($path.'com_fwgallery.css');
				else {
					$path = 'components/com_fwgallerytmpl'.JFHelper :: getCurrentTemplate().'/assets/css/';
					if (file_exists(JPATH_SITE.'/'.$path.'style.css')) JHTML::stylesheet($path.'style.css');
					else JHTML::stylesheet(FWG_ASSETS.'css/style.css');
				}
				define('FWG_STYLESHEET_LOADED', true);
    		}
    	}
        static function encodeDate($date) {
            if (!$date or (is_string($date) and $date[0]=='0')) {
                return '';
            } else {
	        	$params = JComponentHelper :: getParams('com_fwgallery');
	        	$date_format = $params->get('date_format');
                $date = JFactory::getDate($date);
                return $date->format($date_format);
            }
        }
        static function decodeDate($date) {
            if ($date = explode('/', $date) and count($date) == 3) {
                return $date[2].'-'.$date[0].'-'.$date[1];
            } elseif ($date = @strtotime($date)) {
                return date('Y-m-d', $date);
            } else {
                return '';
            }
        }
        static function getFileMimeImage($file) {
            if (preg_match('/\.([^\.]{2,4})$/',$file->filename,$m)) {
                $filename = 'images/mime/'.$m[1].'_icon.png';
                if (file_exists(JPATH_COMPONENT_SITE.'/'.$filename)) {
                    return FWG_ASSETS.$filename;
                } else {
                    return FWG_ASSETS.'images/mime/file_icon.png';
                }
            }
        }
        static function isFileExists($file, $prefix='') {
        	if (strpos($prefix, 'orig') !== false) {
	            $path = 'files/'.$file->_user_id.'/';
	            if ($file->original_filename and file_exists(FWG_STORAGE_PATH.$path.$file->original_filename)) {
	                return true;
	            }
        	} else {
	            if ($prefix and strpos($prefix, '_') === false) $prefix .= '_';
	            $path = 'files/'.$file->_user_id.'/';
	            if ($file->filename and file_exists(FWG_STORAGE_PATH.$path.$prefix.$file->filename)) {
	                return true;
	            }
        	}
        }
        static function getFileFilename($file, $prefix='') {
        	if (strpos($prefix, 'orig') !== false) {
	            $path = 'files/'.$file->_user_id.'/';
	            if ($file->original_filename and file_exists(FWG_STORAGE_PATH.$path.$file->original_filename)) {
	                return FWG_STORAGE.$path.$file->original_filename;
	            } elseif (isset($file->parent) and $gallery = JFHelper :: findGalleryImage($file, $prefix)) {
	                return FWG_STORAGE.'files/'.$gallery->_user_id.'/'.$gallery->original_filename;
				} else {
	                return FWG_ASSETS.'images/no_'.$prefix.'image.jpg';
	            }
        	} else {
	            if ($prefix and strpos($prefix, '_') === false) $prefix .= '_';
	            $path = 'files/'.$file->_user_id.'/';
	            if ($file->filename and file_exists(FWG_STORAGE_PATH.$path.$prefix.$file->filename)) {
	                return FWG_STORAGE.$path.$prefix.$file->filename;
	            } elseif (isset($file->parent) and $gallery = JFHelper :: findGalleryImage($file, $prefix)) {
	                return FWG_STORAGE.'files/'.$gallery->_user_id.'/'.$prefix.$gallery->filename;
				} else {
	                return FWG_ASSETS.'images/no_'.$prefix.'image.jpg';
	            }
        	}
        }
		static function findGalleryImage($gallery, $prefix='') {
			static $galleries;
			if (!is_array($galleries)) {
				$db = JFactory :: getDBO();
				$user = JFactory::getUser();
				$where = array(
					'p.published = 1'
				);
				if ($user->id) {
					$where[] = '(p.gid = 0 OR p.gid IS NULL OR EXISTS(SELECT * FROM #__usergroups AS ug WHERE pg.lft=ug.lft AND ug.id IN ('.implode(',',$user->groups).')))';
				} else $where[] = '(p.gid = 0 OR p.gid IS NULL)';

				$db->setQuery('
SELECT
	p.id,
	p.user_id AS _user_id,
	p.parent,
	CASE WHEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) IS NOT NULL THEN (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) ELSE (SELECT filename FROM #__fwg_files AS f WHERE f.project_id = p.id ORDER BY ordering LIMIT 1) END AS filename,
	CASE WHEN (SELECT original_filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) IS NOT NULL THEN (SELECT original_filename FROM #__fwg_files AS f WHERE f.project_id = p.id AND selected = 1 LIMIT 1) ELSE (SELECT original_filename FROM #__fwg_files AS f WHERE f.project_id = p.id ORDER BY ordering LIMIT 1) END AS original_filename
FROM
	#__fwg_projects AS p
	LEFT JOIN #__usergroups AS pg ON (pg.id = p.gid)
WHERE
	'.implode(' AND ', $where));
				$galleries = (array)$db->loadObjectList();
			}
			/* first, look all child galleries */
			foreach ($galleries as $g) {
				$path = 'files/'.$g->_user_id.'/';
	        	if (strpos($prefix, 'orig') !== false) {
					if ($g->parent == $gallery->id and $g->original_filename and file_exists(FWG_STORAGE_PATH.$path.$prefix.$g->original_filename)) {
						return $g;
					}
	        	} else {
					if ($g->parent == $gallery->id and $g->filename and file_exists(FWG_STORAGE_PATH.$path.$prefix.$g->filename)) {
						return $g;
					}
	        	}
			}
			/* second - look all grand child galleries */
			foreach ($galleries as $g) {
				if ($g->parent == $gallery->id and $buff = JFHelper :: findGalleryImage($g, $prefix)) {
					return $buff;
				}
			}

		}
        static function getLogoViewFilename($obj=null) {
            if ($obj and $obj->logo and file_exists(FWG_STORAGE_PATH.'files/'.$obj->user_id.'/'.$obj->logo)) {
                return FWG_STORAGE.'files/'.$obj->user_id.'/'.$obj->logo;
            } else {
                return FWG_ASSETS.'images/no_th_image.jpg';
            }
        }
        static function getWatermarkFilename() {
        	$params = JComponentHelper :: getParams('com_fwgallery');
        	if ($watermark = $params->get('watermark_file')) {
        		if (file_exists(FWG_STORAGE_PATH.$watermark)) {
        			return FWG_STORAGE.$watermark;
        		}
        	}
        }
        function loadGalleries() {
        	static $galleries;
        	if (!is_array($galleries)) {
        		$db = JFactory :: getDBO();
        		$db->setQuery('SELECT * FROM #__fwg_projects');
        		$galleries = (array)$db->loadObjectList();
        	}
        	return $galleries;
        }
        function getParentGalleriesIDS($gallery_id) {
        	$ids = array();

        	$galleries = JFHelper :: loadGalleries();
        	if ($galleries) foreach ($galleries as $gallery) {
        		if ($gallery->id == $gallery_id) {
        			$ids[] = $gallery->parent;
        			break;
        		}
        	}
        	if ($ids) {
				do {
					$found = false;
					foreach ($galleries as $gallery) {
						if ($gallery->parent and in_array($gallery->id, $ids) and !in_array($gallery->parent, $ids)) {
							$ids[] = $gallery->parent;
							$found = true;
						}
					}
				} while($found);
        	}
        	return $ids;
        }
        function getItemid($view='galleries', $id=0, $default=0, $exact=false) {
        	$menu = JMenu :: getInstance('site');
        	$item = null;
        	if ($items = $menu->getItems('link', 'index.php?option=com_fwgallery&view='.$view)) {
				if ($id) {
/* first, have a look for direct hit */
	        		foreach ($items as $menuItem) {
	    				if ((is_string($menuItem->params) and preg_match('/id\='.$id.'\s/ms', $menuItem->params))
	    				 or (is_object($menuItem->params) and $id == $menuItem->params->get('id'))) {
		        			$item = $menuItem;
		        			break;
	    				}
	        		}
/* then have a look for a any existing parent hit */
	        		if (!$item and $view == 'gallery') {
	        			if ($ids = JFHelper :: getParentGalleriesIDS($id)) {
			        		foreach ($items as $menuItem) {
			    				if ((is_string($menuItem->params) and preg_match('/id\='.implode('|', $ids).'\s/ms', $menuItem->params))
			    				 or (is_object($menuItem->params) and in_array($menuItem->params->get('id'), $ids))) {
				        			$item = $menuItem;
				        			break;
			    				}
			        		}
	        			}
	        		}
				}
/* then take first menu element */
        		if (!$item) $item = $items[0];
        	}
        	if (!$exact and !$item) $item = $menu->getItems('link', 'index.php?option=com_fwgallery&view=galleries', true);

        	if ($item) return $item->id;
        	elseif ($default) return $default;
        	elseif (!$exact and $item = $menu->getActive()) return $item->id;
        }
		static function getIP() {
			if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
			    $ip = getenv('HTTP_CLIENT_IP');
			else if (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
			    $ip = getenv('REMOTE_ADDR');
			else if (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
			    $ip = getenv('HTTP_X_FORWARDED_FOR');
			else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
			    $ip = $_SERVER['REMOTE_ADDR'];
			else
				$ip = '127.0.0.1';
			return $ip;
		}
		static function getGps($exifCoord, $hemi) {
		    $degrees = count($exifCoord) > 0 ? JFHelper :: gps2Num($exifCoord[0]) : 0;
		    $minutes = count($exifCoord) > 1 ? JFHelper :: gps2Num($exifCoord[1]) : 0;
		    $seconds = count($exifCoord) > 2 ? JFHelper :: gps2Num($exifCoord[2]) : 0;
		    $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;
		    return floatval($flip * ($degrees +($minutes/60)+($seconds/3600)));
		}
		static function gps2Num($coordPart) {
		    $parts = explode('/', $coordPart);
		    if (count($parts) <= 0)
		        return 0;
		    else if (count($parts) == 1)
		        return $parts[0];
		    else
		    	return floatval($parts[0]) / floatval($parts[1]);
		}
		static function getGalleryColor($id) {
			static $galleries;
			if (!is_array($galleries)) {
				$db = JFactory :: getDBO();
				$db->setQuery('SELECT id, color, parent FROM #__fwg_projects ORDER BY parent');
				$galleries = $db->loadObjectList('id');
			}

			if (isset($galleries[$id])) {
				if ($galleries[$id]->color and preg_match('/[0-9a-fA-F]{3,6}/', $galleries[$id]->color)) return $galleries[$id]->color;
				else return JFHelper :: getGalleryColor($galleries[$id]->parent);
			} else {
				$params = JComponentHelper :: getParams('com_fwgallery');
				return $params->get('gallery_color');
			}
		}
		static function request($url) {
			if (function_exists('curl_init')) {
				$ch = curl_init();
				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				$result = curl_exec ($ch);
				curl_close($ch);
				return $result;
			} elseif (function_exists('file_get_contents')) {
				return file_get_contents($url);
			}
		}
		static function detectIphone() {
			if ($user_agent = JArrayHelper :: getValue($_SERVER, 'HTTP_USER_AGENT')) {
				$mobile_oses = array('iPhone','iPod','iPad','iPaid');
				foreach ($mobile_oses as $wos) if (strpos($user_agent, $wos) !== false) {
					return true;
				}
			}
		}
    }
}

?>