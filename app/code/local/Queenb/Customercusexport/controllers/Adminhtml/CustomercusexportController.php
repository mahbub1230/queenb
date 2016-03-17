<?php

class Queenb_Customercusexport_Adminhtml_CustomercusexportController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('customercusexport/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	public function savecsvcpanelAction() {
		$data = $this->getRequest()->getPost();
		
		$output='"Customer Lifetime Spend","Orders since","value of each order","email address","first name","last name"'."\n";
		
		
		$filename = "eParcel.csv";
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
	
		echo $output;
		exit;
	}
	
	
	public function savecsvAction() {
		$data = $this->getRequest()->getPost();
		$reportfromarr=explode('/',$data['report_from']);
		$reporttoarr=explode('/',$data['report_to']);
		
		$fyy=$reportfromarr[2];
		$fmm=$reportfromarr[0];
		$fdd=$reportfromarr[1];
		
		$tyy=$reporttoarr[2];
		$tmm=$reporttoarr[0];
		$tdd=$reporttoarr[1];
		
		$fromint=date("Y-m-d H:s:i",mktime(0,0,0,$fmm,$fdd,$fyy));
		$toint=date("Y-m-d H:s:i",mktime(23,59,59,$tmm,$tdd,$tyy));
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$output='"Customer Lifetime Spend","Orders since","value of each order","email address","first name","last name"'."\n";
		$readresultweight=$write->query("SELECT customer_email,customer_firstname,customer_lastname,GROUP_CONCAT(created_at SEPARATOR ',') as 'alldate',GROUP_CONCAT(subtotal_invoiced SEPARATOR ',') as 'oamts' ,sum(subtotal_invoiced) as 'totalorder' FROM mg_sales_flat_order WHERE status='complete' and created_at between '".$fromint."' and '".$toint."' group by customer_email");
		$AllDataresults=$readresultweight->fetchAll();
		foreach ($AllDataresults as $result)
		{
			$customer_email=$result['customer_email'];
			$customer_firstname=$result['customer_firstname'];
			$customer_lastname=$result['customer_lastname'];
			$alldate=$result['alldate'];
			$oamts=$result['oamts'];
			$totalorder=$result['totalorder'];

			
			$output.='"'.$totalorder.'","'.$alldate.'","'.$oamts.'","'.$customer_email.'","'.$customer_firstname.'","'.$customer_lastname.'"'."\n";
		}
		

		/*$usercustomer = Mage::getModel('customer/customer')->getCollection();
		$usercustomer->addAttributeToFilter('created_at', array('from'=>$fromint, 'to'=>$toint));
		$usercustomer->addAttributeToSort('firstname', 'ASC');
		
		
		foreach($usercustomer as $user):
		$id=$user->getId();
		$customer = Mage::getModel('customer/customer')->load($id);
		
			$readresult=$write->query("SELECT sum(subtotal_invoiced) as 'tinvoice' FROM mg_sales_flat_order WHERE customer_email = '".$customer->getEmail()."'");
			$AllData=$readresult->fetchAll();
			$tinvoice=$AllData[0]['tinvoice'];
			
			$readresult=$write->query("SELECT DATE_FORMAT(created_at,'%d-%b-%Y') AS niceDate FROM mg_sales_flat_order WHERE STATUS != 'pending' and customer_email = '".$customer->getEmail()."' order by entity_id asc LIMIT 0 , 1");
			$AllData=$readresult->fetchAll();
			//$createdat=strtotime(date("d-M-Y",$AllData[0]['created_at']));
			$createdat=$AllData[0]['niceDate'];
			
			$readresultweight=$write->query("SELECT subtotal_invoiced FROM mg_sales_flat_order WHERE customer_email = '".$customer->getEmail()."'");
			$ivtot="";
			$AllDataresults=$readresultweight->fetchAll();
			foreach ($AllDataresults as $result)
			{
				$subtotalinvoiced=$result['subtotal_invoiced'];
				$ivtot.=round($subtotalinvoiced,2).', ';
			}
			$ivtot=substr($ivtot,0,-2);
			$output.='"'.$tinvoice.'","'.$createdat.'","'.$ivtot.'","'.$customer->getEmail().'","'.$customer->getFirstname().'","'.$customer->getLastname().'"'."\n";
		endforeach;*/
		
		
		$filename = "CustomerOrderCsvFile.csv";
		header('Content-type: application/csv');
		header('Content-Disposition: attachment; filename='.$filename);
	
		echo $output;
		exit;
	}
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('customercusexport/customercusexport')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('customercusexport_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('customercusexport/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('customercusexport/adminhtml_customercusexport_edit'))
				->_addLeft($this->getLayout()->createBlock('customercusexport/adminhtml_customercusexport_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customercusexport')->__('Item does not exist'));
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
	  			
	  			
			$model = Mage::getModel('customercusexport/customercusexport');		
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customercusexport')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customercusexport')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('customercusexport/customercusexport');
				 
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
        $customercusexportIds = $this->getRequest()->getParam('customercusexport');
        if(!is_array($customercusexportIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($customercusexportIds as $customercusexportId) {
                    $customercusexport = Mage::getModel('customercusexport/customercusexport')->load($customercusexportId);
                    $customercusexport->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($customercusexportIds)
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
        $customercusexportIds = $this->getRequest()->getParam('customercusexport');
        if(!is_array($customercusexportIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($customercusexportIds as $customercusexportId) {
                    $customercusexport = Mage::getSingleton('customercusexport/customercusexport')
                        ->load($customercusexportId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($customercusexportIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'customercusexport.csv';
        $content    = $this->getLayout()->createBlock('customercusexport/adminhtml_customercusexport_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'customercusexport.xml';
        $content    = $this->getLayout()->createBlock('customercusexport/adminhtml_customercusexport_grid')
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