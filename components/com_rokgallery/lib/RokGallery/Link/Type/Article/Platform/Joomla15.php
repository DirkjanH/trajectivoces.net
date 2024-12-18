<?php
 /**
  * @version   $Id: Joomla15.php 10871 2013-05-30 04:06:26Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
 
class RokGallery_Link_Type_Article_Platform_Joomla15 implements RokGallery_Link_Type_Article_Platform {
    public function &getArticleInfo($id)
    {
        $article_info = new RokGallery_Link_Type_Article_Info();
        $article_info->setId($id);
        $article_info->setLink('index.php?option=com_content&view=article&id='.$id);
        //get the article info from joomla
        $db = JFactory::getDBO();
        // Get the articles
		$query = 'SELECT c.title'.
				' FROM #__content AS c' .
                ' WHERE c.id = '. $id;
		$db->setQuery($query);
		$article_info->setTitle($db->loadResult());
        return $article_info;
    }
}
