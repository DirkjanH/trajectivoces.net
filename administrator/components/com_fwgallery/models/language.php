<?php
/**
 * FW Real Estate x.x.x - Joomla! Property Manager
 * @copyright (C) 2011 Fastw3b
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fastw3b.net/ Official website
 **/

defined( '_JEXEC' ) or die( 'Restricted access' );

class fwGalleryModelLanguage extends JModelLegacy {
    function getUserState($name, $def='', $type='cmd') {
        $app = JFactory::getApplication();
        $context = 'com_fwgallery.language.';
        return $app->getUserStateFromRequest($context.$name, $name, $def, $type);
    }
	function getLanguageData() {
		jimport('joomla.filesystem.file');
		$langs = JFHelper :: getInstalledLanguages();
		$lang = JArrayHelper :: getValue($langs, JFHelper :: getLanguage());
		$type = (int)$this->getUserState('type', 0, 'int');

		$serach = $this->getUserState('search', '', 'string');

		$src_file = ($type?JPATH_SITE:JPATH_ADMINISTRATOR).'/language/en-GB/en-GB.com_fwgallery.ini';
		$trg_file = ($type?JPATH_SITE:JPATH_ADMINISTRATOR).'/language/'.$lang->tag.'/'.$lang->tag.'.com_fwgallery.ini';

		$src_buff = file_exists($src_file)?explode("\n", JFile :: read($src_file)):array();
		$result = array();
		foreach ($src_buff as $i=>$row) if (trim($row)) {
			$row = explode("=", trim($row), 2);
			if (!empty($row[1]) and (!$serach or ($serach and (mb_stripos($row[0], $serach) !== false or mb_stripos($row[1], $serach) !== false))))
				$result[$row[0]] = array(
					'src' => trim($row[1], '"'),
					'trg' => ''
				);
		}

		$trg_buff = file_exists($trg_file)?explode("\n", JFile :: read($trg_file)):array();
		foreach ($trg_buff as $i=>$row) if (trim($row)) {
			$row = explode("=", trim($row), 2);
			if (!empty($row[1]) and (!$serach or ($serach and (mb_stripos($row[0], $serach) !== false or mb_stripos($row[1], $serach) !== false)))) {
				if (isset($result[$row[0]])) {
					$result[$row[0]]['trg'] = trim($row[1], '"');
				} else {
					$result[$row[0]] = array(
						'src' => '',
						'trg' => trim($row[1], '"')
					);
				}
			}
		}

		return $result;
	}
	function save() {
		$langs = JFHelper :: getInstalledLanguages();
		$lang = JArrayHelper :: getValue($langs, JFHelper :: getLanguage());
		$type = (int)JRequest :: getInt('type');

		$trg_file = ($type?JPATH_SITE:JPATH_ADMINISTRATOR).'/language/'.$lang->tag.'/'.$lang->tag.'.com_fwgallery.ini';

		$result = array();

/* load existing values */
		$trg_buff = file_exists($trg_file)?explode("\n", JFile :: read($trg_file)):array();
		foreach ($trg_buff as $i=>$row) if (trim($row)) {
			$row = explode("=", trim($row), 2);
			if (!empty($row[1]))
				$result[$row[0]] = trim($row[1], '"');
		}

/* update changes */
		if ($data = (array)JRequest :: getVar('lang_data', array(), 'post', 'ARRAY', JREQUEST_ALLOWRAW)) {
			foreach ($data as $const => $val) {
				$result[$const] = $val;
			}

/* collect language file */
			$buff = '';
			foreach ($result as $const=>$val) if (!empty($val)) {
				$buff .= $const.'="'.str_replace(array('"', "\r", "\n", "\t"), array('&quot;', ' ', ' ', ' '), $val).'"'."\n";
			}

/* store updated language file */
			jimport('joomla.filesystem.file');
			if (JFile :: write($trg_file, $buff)) {
				$this->setError(JText :: _('FWG_LANGUAGE_FILE_SUCCESFULLY_UPDATED'));
				return true;
			} else {
				$this->setError(JText :: _('FWG_LANGUAGE_CANT_WRITE_FILE'));
			}
		}
	}
}