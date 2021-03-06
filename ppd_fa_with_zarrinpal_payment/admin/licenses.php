<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined( '_JEXEC' ) or
die( 'Direct Access to this location is not allowed.' );

/**
 * @author		Ratmil 
 * http://www.ratmilwebsolutions.com
*/

require_once(JPATH_COMPONENT.DS.'admin'.DS.'ppd.php');
require_once(JPATH_COMPONENT.DS.'data'.DS.'gentable.php');
require_once(JPATH_COMPONENT.DS.'html'.DS.'vdatabind.html.php');
require_once(JPATH_COMPONENT.DS.'html'.DS.'vdatabindcb.html.php');
require_once(JPATH_COMPONENT.DS.'html'.DS.'vdatabindmodel.html.php');
require_once(JPATH_COMPONENT.DS.'html'.DS.'vdatabindimage.html.php');
require_once(JPATH_COMPONENT.DS.'html'.DS.'pricecur.html.php');
require_once(JPATH_COMPONENT.DS.'html'.DS.'vdgroupselect.html.php');

/************************************************************
Class to manage licenses
*************************************************************/
class LicensesForm extends PPDForm
{
	/**
	Class constructor
	*/
	function __construct()
	{
		parent::__construct();
		$this->context = 'com_payperdownloadplus.licenses';
		$this->formTitle = $this->toolbarTitle = JText::_('PAYPERDOWNLOADPLUS_LICENSES_83');
		$this->editItemTitle = JText::_("PAYPERDOWNLOADPLUS_EDIT_LICENSE_84");
		$this->newItemTitle = JText::_("PAYPERDOWNLOADPLUS_NEW_LICENSE_85");
		$this->toolbarIcon = 'licenses.png';
		$this->registerTask('copy');
		$this->deleteErrorMessage = JText::_("PAYPERDOWNLOADPLUS_LICENSE_DELETE_ERROR");
		$this->updateNulls = true;
	}
	
	/**
	Create the elements that define how data is to be shown and handled. 
	*/
	function createDataBinds()
	{
		if($this->dataBindModel == null)
		{
			$option = JRequest::getVar('option');
		
			$this->dataBindModel = new VisualDataBindModel();
			$this->dataBindModel->setKeyField("license_id");
			$this->dataBindModel->setTableName("#__payperdownloadplus_licenses");
			
			$bind = new VisualDataBind('license_name', JText::_('PAYPERDOWNLOADPLUS_NAME_86'));
			$bind->setMaxLength(255);
			$bind->setMinLength(1);
			$bind->setColumnWidth(20);
			$bind->setEditLink(true);
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_LICENSE_NAME_87"));
			$this->dataBindModel->addDataBind( $bind );
			
			$bind = new VisualDataBind('member_title', JText::_('PAYPERDOWNLOADPLUS_MEMBER_TITLE_88'));
			$bind->setMaxLength(255);
			$bind->setMinLength(1);
			$bind->setColumnWidth(20);
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_TITLE_GIVEN_TO_USER_AFTER_PURCHASING_THIS_LICENSE_89"));
			$this->dataBindModel->addDataBind( $bind );
			
			$bind = new VisualEditCheckboxDataBind('expiration', JText::_('PAYPERDOWNLOADPLUS_EXPIRATION_DAYS_90'),
				JText::_("PAYPERDOWNLOADPLUS_UNLIMITED_VALID_DAYS"));
			$bind->setColumnWidth(10);
			$bind->setRegExp("\\s*\\d+\\s*");
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_LICENSE_EXPIRATION_IN_DAYS_91"));
			$this->dataBindModel->addDataBind( $bind );
			
			$bind = new PriceCurrencyVisualDataBind('price', JText::_('PAYPERDOWNLOADPLUS_PRICE_92'), 'currency_code');
			$bind->setColumnWidth(10);
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_LICENSE_PRICE_93"));
			$this->dataBindModel->addDataBind( $bind );
		
			$bind = new VisualEditCheckboxDataBind('level', JText::_('PAYPERDOWNLOADPLUS_LEVEL_96'),
				JText::_('PAYPERDOWNLOADPLUS_IGNORE_LEVEL'));
			$bind->setColumnWidth(10);
			$bind->setRegExp("\\s*\\d+\\s*");
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_LEVEL_DESC"));
			$this->dataBindModel->addDataBind( $bind );
			
			$bind = new VisualEditCheckboxDataBind('max_download', JText::_('PAYPERDOWNLOADPLUS_MAX_DOWNLOAD_COUNT'), 
				JText::_('PAYPERDOWNLOADPLUS_MAX_DOWNLOADS_UNLIMITED'));
			$bind->setColumnWidth(10);
			$bind->setRegExp("\\s*\\d+\\s*");
			$bind->showInGrid = true;
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_MAX_DOWNLOAD_COUNT_DESC"));
			$this->dataBindModel->addDataBind( $bind );
			
			$bind = new VisualDataBind('aup', JText::_('PAYPERDOWNLOADPLUS_LICENSE_AUP'));
			$bind->setRegExp("\\s*\\d+\\s*");
			$bind->showInGrid = false;
			$bind->allowBlank = true;
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_LICENSE_AUP_DESC"));
			$this->dataBindModel->addDataBind( $bind );
			
			$version = new JVersion();
			if($version->RELEASE >= "1.6")
			{
				$bind = new JoomlaGroupSelect('user_group', JText::_('PAYPERDOWNLOADPLUS_LICENSE_USER_GROUP'), "", "", "");
				$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_LICENSE_USER_GROUP_DESC"));
				$bind->defaultValue = 0;
				$bind->showInGrid = false;
				$bind->useForFilter = false;
				$bind->firstItem = JText::_("PAYPERDOWNLOADPLUS_NO_USER_GROUP");
				$this->dataBindModel->addDataBind( $bind );
			}
			
			$bind = new ImageVisualDataBind('license_image', JText::_('PAYPERDOWNLOADPLUS_LICENSE_IMAGE'));
			$bind->showInGrid = false;
			$bind->showImage = true;
			$bind->allowBlank = true;
			$bind->setImageLimits(5, 5, 100, 100);
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_LICENSE_IMAGE_DESC"));
			$this->dataBindModel->addDataBind( $bind );
			
			$bind = new RadioVisualDataBind('enabled', JText::_('PAYPERDOWNLOADPLUS_LICENSE_ENABLED'));
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_LICENSE_ENABLED_DESC"));
			$bind->setColumnWidth(5);
			$bind->defaultValue = 1;
			$bind->yes_task = "unpublish";
			$bind->no_task = "publish";
			$bind->yes_image = "administrator/components/$option/images/published.png";
			$bind->no_image = "administrator/components/$option/images/unpublished.png";
			$this->dataBindModel->addDataBind( $bind );
			
			$bind = new ComboVisualDataBind('renew', JText::_('PAYPERDOWNLOADPLUS_DISABLE_RENOVATION'), "", "", "");
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_DISABLE_RENOVATION_DESC"));
			$bind->defaultValue = 0;
			$bind->showInGrid = false;
			$bind->useForFilter = false;
			$bind->addItem(0, JText::_("PAYPERDOWNLOADPLUS_ALWAYS_ALLOWED"));
			$bind->addItem(1, JText::_("PAYPERDOWNLOADPLUS_IF_NOT_ACTIVE"));
			$bind->addItem(2, JText::_("PAYPERDOWNLOADPLUS_NEVER"));
			$this->dataBindModel->addDataBind( $bind );
			
			$bind = new WYSIWYGEditotVisualDataBind('description', JText::_('PAYPERDOWNLOADPLUS_DESCRIPTION_97'));
			$bind->showInGrid = false;
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_DESCRIPTION_FOR_THE_LICENSE_98"));
			$this->dataBindModel->addDataBind( $bind );
			
			$bind = new WYSIWYGEditotVisualDataBind('thankyou_text', JText::_('PAYPERDOWNLOADPLUS_LICENSE_AFTER_PAYMENT_TEXT'));
			$bind->showInGrid = false;
			$bind->setEditToolTip(JText::_("PAYPERDOWNLOADPLUS_LICENSE_AFTER_PAYMENT_TEXT_DESC"));
			$this->dataBindModel->addDataBind( $bind );
		}
	}
	
	function createToolbar($task, $option)
	{
		JHTML::_('stylesheet', 'backend.css', 'administrator/components/'. $option . '/css/');
		if($task == 'display' || $task == 'cancel' || $task == '')
		{
			JToolBarHelper::publish();
			JToolBarHelper::unpublish();
			JToolBarHelper::custom('copy', 'copy', '', JText::_("PAYPERDOWNLOADPLUS_COPY"), true);
		}
		parent::createToolbar($task, $option);
	}
	
	function copy($task, $option)
	{
		$cid = JRequest::getVar('cid', array(0), '', 'array' );
		$id = (int)$cid[0];
		$copy_suffix = JText::_("PAYPERDOWNLOADPLUS_COPY_TEXT");
		$db = JFactory::getDBO();
		$db->setQuery(
			"INSERT INTO #__payperdownloadplus_licenses
				(license_name, member_title, expiration, price, 
				currency_code, level, description, notify_url, max_download, aup)
			SELECT CONCAT(license_name, ' - " . $db->getEscaped($copy_suffix) . "'), member_title, expiration, price, 
				currency_code, level, description, notify_url, max_download, aup 
			FROM #__payperdownloadplus_licenses
			WHERE license_id = $id");
		$db->query();
		$this->redirectToList();
	}
	
	function getHtmlObject()
	{
		$htmlObject = parent::getHtmlObject();
		$htmlObject->enctype = true;
		$htmlObject->showId = true;
		return $htmlObject;
	}
}
?>