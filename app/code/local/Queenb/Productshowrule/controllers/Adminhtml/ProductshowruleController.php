<?php

class Queenb_Productshowrule_Adminhtml_ProductshowruleController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('productshowrule/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	public function saveproductAction() {
		$data = $this->getRequest()->getPost();
		
		$selcusgroup=$data['selcusgroup'];
		$checkboxvar=$data['checkboxvar'];
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$write->query("delete from  mg_productshowrule where cusgroupid ='$selcusgroup'");
		foreach($checkboxvar as $key=>$val){
			$write->query("insert into mg_productshowrule (cusgroupid,productid) values('$selcusgroup','$val')");
		}
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productshowrule')->__('Data was successfully saved'));
		$this->_redirect('*/*/');
	}
	public function fetchproductAction() {
		//die('L 32');
		$gid=$_GET['gid'];
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$productarray=array();
		$readresultweight=$write->query("select * from  mg_productshowrule where cusgroupid ='$gid'");
  		$AllDataresults=$readresultweight->fetchAll();
		foreach ($AllDataresults as $result)
		{
			$productarray[]=$result['cusgroupid'].'~'.$result['productid'];
		} 
		
	   $products = Mage::getModel('catalog/product')->getCollection();
	   $products->addAttributeToSelect('*');
 
	   $str="<ul>";
	   foreach($products as $prod){
		   $ptxt=$gid.'~'.$prod->getId();
		   if (in_array($ptxt, $productarray)) {
				$sel=" checked";
			}
			else
			{
				$sel="";
			}
		   $str.='<li><input type="checkbox" class="chkcheckbox" name="checkboxvar[]" value="'.$prod->getId().'"'.$sel.'>&nbsp;'.$prod->getName().'</li>';
	   }
	   $str.="</ul>";
	   echo $str;
	   
	   
	}
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('productshowrule/productshowrule')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('productshowrule_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('productshowrule/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('productshowrule/adminhtml_productshowrule_edit'))
				->_addLeft($this->getLayout()->createBlock('productshowrule/adminhtml_productshowrule_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productshowrule')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			
			if(isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {	
					/* Starting upload */	
					$uploader = new Varien_File_Uploader('filename');
					
					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	        
		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
	  			
	  			
			$model = Mage::getModel('productshowrule/productshowrule');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('productshowrule')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('productshowrule')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('productshowrule/productshowrule');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $productshowruleIds = $this->getRequest()->getParam('productshowrule');
        if(!is_array($productshowruleIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($productshowruleIds as $productshowruleId) {
                    $productshowrule = Mage::getModel('productshowrule/productshowrule')->load($productshowruleId);
                    $productshowrule->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($productshowruleIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $productshowruleIds = $this->getRequest()->getParam('productshowrule');
        if(!is_array($productshowruleIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($productshowruleIds as $productshowruleId) {
                    $productshowrule = Mage::getSingleton('productshowrule/productshowrule')
                        ->load($productshowruleId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($productshowruleIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'productshowrule.csv';
        $content    = $this->getLayout()->createBlock('productshowrule/adminhtml_productshowrule_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'productshowrule.xml';
        $content    = $this->getLayout()->createBlock('productshowrule/adminhtml_productshowrule_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}